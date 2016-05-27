<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the 
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more 
 * information on this, please see the index.php file or visit 
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * aliroFileInDB is a file object that is stored in the database
 *
 */

class aliroFileInDB extends aliroDatabaseRow {
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__file_system';
	protected $rowKey = 'id';
	protected $attrib = false;

	public function __construct ($filename) {
		// First time we have been given the filename, so we load the information
		if (!$filename) trigger_error(T_('Must provide a file name to create an aliroFileInDB object'));
		else {
			$database = aliroCoreDatabase::getInstance();
			$file = null;
			$database->setQuery("SELECT * FROM #__file_system WHERE filename = '$filename'");
			$file = $database->loadAssocList();
			if ($file AND 1 == count($file)) $this->bind($file[0]);
			else {
				$this->filename = $filename;
				$this->created = $this->modified = $database->dateNow();
			}
		}
	}
	
	public function setAttributes ($filename='', $mimetype='', $headers=array()) {
		if ($mimetype) $this->mimetype = $mimetype;
		if (!empty($headers)) $this->headers = base64_encode(serialize($headers));
		if ($filename AND $filename != $this->filename) {
			// This must be a rename operation
			$this->filename = $filename;
			$this->touch();
			return;
		}
		$this->store();
	}
	
	public function touch () {
		$this->modified = aliroCoreDatabase::getInstance()->dateNow();
		$this->store();
	}

	public function fromFile ($source, $delete=false) {
		if (!$this->id) $this->touch();
        $this->f = fopen($source,'rb');
        if ($this->f) {
			$this->putDBData('getFileData');
			fclose($this->f);
			if ($delete) @unlink($source);
			return true;
        }
        else return false;
	}
	
	private function getFileData () {
		if ($this->f AND !feof($this->f)) return fread($this->f, 60000);
		return false;
	}
	
	public function fromString ($string) {
		if (!$this->id) $this->touch();
		$this->stringdata = $string;
		$this->offset = 0;
		$this->putDBData('getStringData');
		unset($this->stringdata, $this->offset);
		return true;
	}
	
	private function getStringData () {
		if ($this->offset < strlen($this->stringdata)) {
			$offset = $this->offset;
			$this->offset += 60000;
			return (substr($this->stringdata,$offset,60000));
		}
		return false;
	}
	
	private function putDBData ($method) {
		if (0 == $this->id) trigger_error(T_('Attempt to store file in database using aliroFileInDB, but object not properly created with file name.'));
		$this->deleteData();
		$this->filesize = 0;
		$chunkid = 0;
		$database = aliroCoreDatabase::getInstance();
		$sql = "INSERT INTO #__file_system_data (fileid, chunkid, datachunk, bloblength) VALUES ($this->id, ";
		while ($chunk = $this->$method()) {
			$this->filesize += strlen($chunk);
			$chunk = $database->getEscaped($chunk);
			$database->doSQL($sql."$chunkid, '$chunk', LENGTH(datachunk))");
			$chunkid++;
		}
		$this->store();
	}
	
	private function deleteData () {
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL("DELETE FROM #__file_system_data WHERE fileid = '$this->id'");
	}
	
	public function linkToURI ($uri) {
		if (0 == $this->id) trigger_error(T_('Attempt to link file in database to URI using aliroFileInDB, but object not properly created.'));
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL("UPDATE #__urilinks SET uri = '$uri', uri_crc = CRC32('$uri') WHERE application = 'aliroFileInDB' AND name = '$this->filename'");
		if (0 == $database->getAffectedRows()) {
			$description = T_('URI link to a pseudo file held in the database using the class aliroFileInDB.');
			$database->doSQL("INSERT INTO #__urilinks (application, published, notemplate, nohtml, uri_crc, name, uri, class, description) "
			."\n VALUES ('aliroFileInDB', 1, 1, 1, CRC32('$uri'), '$this->filename', '$uri', 'aliroFileInDB', '$description'");
		}
	}
	
	public function deleteURILink () {
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL("DELETE FROM #__urilinks WHERE application = 'aliroFileInDB' AND name = '$this->filename'");
	}
	
	public function serveFile (&$offset) {
		if (0 == $this->id) {
			header ($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			exit;
		}
		$offset = $this->rangeHandler($this->filesize);
		$name = basename($this->filename);
		$parts = explode('.', $name);
		$ctype = $this->setCtype(end($parts));
		if (isset($_SERVER['HTTP_USER_AGENT']) AND (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
		    	$name = urlencode($name);
		}
		else $name = str_replace(' ', '+', $name);

		header("Content-Disposition: attachment; filename=$name");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$this->filesize);
		header("Content-Type: $ctype; charset=utf-8");
		if ($this->mimetype) header ('Content-Type: '.$this->mimetype);
		if ($this->headers) {
			$headers = unserialize(base64_decode($this->headers));
			foreach ($headers as $header) header ($header);
		}
		$this->offset = $offset;
		$result = $this->getFileDataFromDB('sendToBrowser');
		unset($this->offset);
		return $result;
	}

	function setCtype ($file_extension) {
		//This will set the Content-Type to the appropriate setting for the file
		switch( $file_extension ) {
		     case "pdf": $ctype="application/pdf"; break;
		     case "exe": $ctype="application/octet-stream"; break;
		     case "zip": $ctype="application/zip"; break;
		     case "doc": $ctype="application/msword"; break;
		     case "xls": $ctype="application/vnd.ms-excel"; break;
		     case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		     case "gif": $ctype="image/gif"; break;
		     case "png": $ctype="image/png"; break;
		     case "jpeg":
		     case "jpg": $ctype="image/jpg"; break;
		     case "mp3": $ctype="audio/mpeg"; break;
		     case "wav": $ctype="audio/x-wav"; break;
		     case "mpeg":
		     case "mpg":
		     case "mpe": $ctype="video/mpeg"; break;
		     case "mov": $ctype="video/quicktime"; break;
		     case "avi": $ctype="video/x-msvideo"; break;

		     //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
		     case "php":
		     case "htm":
		     case "html": 
		     default: $ctype="application/force-download";
		}
		return $ctype;
	}
	
	private function sendToBrowser($datachunk) {
		echo $datachunk;
		return true;
	}

	private function rangeHandler ($size) {
		if (!empty($_SERVER['HTTP_RANGE'])) preg_match('/^bytes=([0-9]*)\-([0-9]*)/', $_SERVER['HTTP_RANGE'], $matches);
		$seek_end = (empty($matches[2])) ? ($size - 1) : min((integer) $matches[2] ,($size - 1));
		$seek_start = (empty($matches[1]) OR $seek_end < (integer) $matches[1]) ? 0 : max((integer) $matches[1],0);
		$partial = ($seek_start > 0 OR $seek_end < ($size - 1));
		if ($partial) header($_SERVER['SERVER_PROTOCOL'].' 206 Partial Content');
		header('Accept-Ranges: bytes');
		if ($partial) header("Content-Range: bytes $seek_start-$seek_end/$size");
		header('Content-Length: '.($seek_end - $seek_start + 1));
		return $seek_start;
	}

	public function toFile ($destination, $delete=false) {
		if (0 == $this->id) trigger_error(T_('Attempt to copy file from database to file system using aliroFileInDB, but object not properly created with file name.'));
		if (file_exists($destination) OR !($this->f = @fopen($destination, 'wb'))) return false;
		// Offset is used for range sending to browsers, not relevant here, must be zero
		$this->offset = 0;
		$result = $this->getFileDataFromDB('sendToFile');
		fclose($this->f);
		unset($this->f, $this->offset);
		if ($result AND $delete) $this->delete();
		return $result;
	}
	
	private function sendToFile ($datachunk) {
		return fwrite ($this->f, $datachunk);
	}
	
	private function getFileDataFromDB ($method) {
		// Retrieve the file data from the database and process using $method
		$result = false;
		$database = aliroCoreDatabase::getInstance();
		$chunks = $database->doSQLget("SELECT chunkid, bloblength FROM #__file_system_data WHERE fileid='$this->id' ORDER BY chunkid");
		foreach ($chunks as $chunk) {
			@set_time_limit(0);
			if ($this->offset >= $chunk->bloblength) {
				$this->offset -= $chunk->bloblength;
				continue;
			}
			$database->setQuery("SELECT datachunk FROM #__file_system_data WHERE fileid='$this->id' AND chunkid=$chunk->chunkid");
			$datachunk = $database->loadResult();
			if ($this->$method($this->offset ? substr($datachunk, $this->offset) : $datachunk)) $result = true;
			else {
				$result = false;
				break;
			}
			$this->offset = 0;
		}
		return $result;
	}

	public function deleteFile () {
		if ($this->id) {
			aliroCoreDatabase::getInstance()->doSQL("DELETE FROM #__file_system WHERE fileid='$this->id'");
			$this->deleteData();
			$this->deleteURILink();
		}
	}

}
