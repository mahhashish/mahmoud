<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Aliro is open source software, free to use, and licensed under GPL.
 * You can find the full licence at http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * The author freely draws attention to the fact that Aliro derives from Mambo,
 * software that is controlled by the Mambo Foundation.  However, this section
 * of code is totally new.  If it should contain any fragments that are similar
 * to Mambo, please bear in mind (1) there are only so many ways to do things
 * and (2) the author of Aliro is also the author and copyright owner for large
 * parts of Mambo 4.6.
 *
 * Tribute should be paid to all the developers who took Mambo to the stage
 * it had reached at the time Aliro was created.  It is a feature rich system
 * that contains a good deal of innovation.
 *
 * Your attention is also drawn to the fact that Aliro relies on other items of
 * open source software, which is very much in the spirit of open source.  Aliro
 * wishes to give credit to those items of code.  Please refer to
 * http://aliro.org/credits for details.  The credits are not included within
 * the Aliro package simply to avoid providing a marker that allows hackers to
 * identify the system.
 *
 * Copyright in this code is strictly reserved by its author, Martin Brampton.
 * If it seems appropriate, the copyright will be vested in the Aliro Organisation
 * at a suitable time.
 *
 * Copyright (c) 2007 Martin Brampton
 *
 * http://aliro.org
 *
 * counterpoint@aliro.org
 *
 * aliroFileInDB is a file object that is stored in the database
 *
 */

class aliroFileInDB {
	private $filename = '';
	private $database = null;

	public function __construct ($name) {
		$this->filename = $name;
		$this->database = aliroDatabase::getInstance();
	}

	public function fromFile ($source, $delete=false) {
		if ($f = fopen($source,'rb')) {
			$this->delete();
			$chunkid = 0;
			$sql = "INSERT INTO #__file_system (filename, chunkid, datachunk, bloblength) VALUES ($this->filename, ";
			while($f && !feof($f)) {
				$chunk = fread($f, 60000);
				$chunk = $this->database->getEscaped($chunk);
				$this->database->doSQL($sql."$chunkid, '$chunk', LENGTH(datachunk))");
				$chunkid++;
			}
			fclose($f);
			if ($delete) @unlink($source);
			return true;
		}
		else return false;
	}

	public function toFile ($destination, $delete=false) {
		$result = false;
		if (!file_exists($destination) AND $f = @fopen($destination, 'wb')) {
			$this->database->setQuery("SELECT chunkid FROM #__file_system WHERE filename='$this->filename' ORDER BY chunkid");
			$chunks = $this->database->loadResultArray();
			if ($chunks) foreach ($chunks as $chunkid) {
				$this->database->setQuery("SELECT datachunk FROM #__file_system WHERE filename='$this->filename' AND chunkid=$chunkid");
				$datachunk = $this->database->loadResult();
				if (fwrite ($f, $datachunk)) $result = true;
				else {
					$result = false;
					break;
				}
			}
			fclose($f);
		}
		if ($result AND $delete) $this->delete();
		return $result;
	}

	public function delete () {
			$this->database->doSQL("DELETE FROM #__file_system WHERE filename='$this->filename'");
	}

}

?>