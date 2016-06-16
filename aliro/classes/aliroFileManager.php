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
 * aliroFileManager is the singleton class to insulate Aliro from actual file system
 * operations.  This gives the option to elaborate the class to handle issues such as
 * coping with safe mode.  The class also provides a utility method "forceCopy" (used
 * by the installer) to make a copy happen if at all possible.  Some old methods are
 * kept here for backwards compatibility, but they really need review.
 *
 * aliroDirectory allows the creation of an object that corresponds to a directory in
 * the file system, and then provides a number of useful methods to manipulate the
 * directory and its contents, including finding out what the contents are.
 *
 */

class aliroFileManager {

	private static $instance = __CLASS__;

	private function __construct () {
		// Enforce singleton
	}

	private function __clone () {
		// Enforce singleton
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

    function deleteFile ($file) {
        if (file_exists($file)) {
            @chmod($file, 0644);
            return unlink($file);
        }
        return true;
    }

    function deleteDirectory ($dir) {
        if (file_exists($dir)) {
            if (is_dir($dir)) {
                @chmod($dir, 0755);
                return rmdir($dir);
            }
            return false;
        }
        return true;
    }

    function setPermissions ($fileSysObject, $mode=null) {
    	$result = true;
    	if (file_exists($fileSysObject))  {
    		if ($mode);
    		elseif (is_dir($fileSysObject)) $mode = octdec(aliroCore::get('mosConfig_dirperms'));
    		else $mode = octdec(aliroCore::get('mosConfig_fileperms'));
    		if ($mode) {
		    	$origmask = @umask(0);
		    	$result = @chmod($fileSysObject, $mode);
				@umask($origmask);
			}
		}
		return $result;
	}

	function makeDirectory ($dir) {
		$result = @mkdir($dir, 0755);
		if ($result) $this->setPermissions($dir);
		return $result;
	}

    public function createDirectory ($dir, $onlyCheck=false) {
        if (file_exists($dir)) {
            if (is_dir($dir) AND is_writable($dir)) return true;
            else return false;
        }
        list($upDirectory, $count) = $this->containingDirectory($dir);
        if ($count > 1 AND !file_exists($upDirectory) AND !($result = $this->createDirectory($upDirectory, $onlyCheck))) return false;
        if ($onlyCheck AND isset($result)) return true;
        if (!is_dir($upDirectory) OR !is_writable($upDirectory)) return false;
        if ($onlyCheck) return true;
        else return $this->makeDirectory($dir);
    }

    private function containingDirectory ($dir) {
        $dirs = preg_split('*[/|\\\]*', $dir);
        for ($i = count($dirs)-1; $i >= 0; $i--) {
            $text = trim($dirs[$i]);
            unset($dirs[$i]);
            if ($text) break;
        }
        $result2 = count($dirs);
        $result1 = implode('/',$dirs).($result2 > 1 ? '' : '/');
        return array($result1, $result2);
    }

    function simpleCopy ($from, $to) {
        if (@copy($from, $to)) {
            $this->setPermissions($to);
            return true;
        }
        else return false;
    }

    function forceCopy ($from, $to, $reportErrors=false) {
    	if (!file_exists($from)) {
        	if ($reportErrors) aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Copy requested for %s, but source file not found'), $from), _ALIRO_ERROR_WARN);
        	return false;
    	}
        $todir = dirname($to);
        if (!file_exists($todir)) $this->createDirectory($todir);
        if (!file_exists($todir)) {
        	if ($reportErrors) aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Copy requested for %s, but could not create destination directory'), $from), _ALIRO_ERROR_WARN);
        	return false;
        }
        $name = basename($from);
        $this->deleteFile($to.$name);
        if ($this->simpleCopy ($from, $to)) return true;
        elseif ($reportErrors) aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Copy requested for %s, source found but could not copy to %s'), $from, $to), _ALIRO_ERROR_WARN);
        return false;
    }

    function lightCopy ($from, $to) {
        $name = basename($from);
        if (file_exists($to.$name)) return false;
        $todir = dirname($to);
        if (!file_exists($todir)) $this->createDirectory($todir);
        if (!file_exists($todir)) return false;
        return $this->simpleCopy ($from, $to);
    }

    function acceptCopy ($to) {
        $todir = dirname($to);
        return $this->createDirectory($todir, true);
    }

	// Provided for compatibility - not really needed in full
	// PHP is happy with paths containing forward or backwards slashes, or even a mixture
	// Aliro normally converts all paths to use forward slashes for tidiness
    function mosPathName($p_path, $p_addtrailingslash=true) {
        if (substr(PHP_OS, 0, 3) == 'WIN')	{
            $retval = str_replace( '/', '\\', $p_path );
            if ($p_addtrailingslash AND substr( $retval, -1 ) != '\\') $retval .= '\\';
            // Remove double \\
            $retval = str_replace( '\\\\', '\\', $retval );
        }
        else {
            $retval = str_replace( '\\', '/', $p_path );
            if ($p_addtrailingslash AND substr( $retval, -1 ) != '/') $retval .= '/';
            // Remove double //
            $retval = str_replace('//','/',$retval);
        }
        return $retval;
    }

    /**
	* Chmods files and directories recursively to mos global permissions. Available from 4.5.2 up.
	* @param path The starting file or directory (no trailing slash)
	* @param filemode Integer value to chmod files. NULL = dont chmod files.
	* @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
	* @return TRUE=all succeeded FALSE=one or more chmods failed
	*/
    function mosChmod($path)
    {
        $filemode = octdec(aliroCore::get('mosConfig_fileperms'));
        $dirmode = octdec(aliroCore::get('mosConfig_dirperms'));
        if ($filemode OR $dirmode) return $this->mosChmodRecursive($path, $filemode, $dirmode);
        return true;
    } // mosChmod

    /**
	* Chmods files and directories recursively to given permissions. Available from 4.5.2 up.
	* @param path The starting file or directory (no trailing slash)
	* @param filemode Integer value to chmod files. NULL = dont chmod files.
	* @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
	* @return TRUE=all succeeded FALSE=one or more chmods failed
	*/
    function mosChmodRecursive($path, $filemode=NULL, $dirmode=NULL) {
        $ret = true;
        if (is_dir($path)) {
            $topdir = new aliroDirectory($path);
            $files = $topdir->listFiles ('', 'file', true, true);
            $dirs = $topdir->listFiles ('', 'dir', true, true);
        }
        else {
            $files = array($path);
            $dirs = array();
        }
        if (isset($filemode)) foreach ($files as $file) $ret = @chmod($file, $filemode) ? $ret : false;
        if (isset($dirmode)) foreach ($dirs as $dir) $ret = @chmod($dir, $dirmode) ? $ret : false;
        return $ret;
    }
    
    public function makeUploadSafe ($name, $useRealName=false) {
    	$tempdir = $this->makeTemp();
    	$files = array();
    	$tempfiles = (array) $_FILES[$name]['tmp_name'];
    	$filenames = (array) $_FILES[$name]['name'];
    	foreach ($tempfiles as $key=>$temp) {
    		if ($useRealName) $filename = $filenames[$key];
    		else $filename = basename($temp);
    		if (move_uploaded_file($temp, $tempdir.$filename)) {
    			$files[$filenames[$key]] = $tempdir.$filename;
    			$this->setPermissions($tempdir.$filename);
    		}
    	}
    	return $files;
    }

	public function extractArchive ($filename, $extractDir='') {
		if (!$extractDir) $extractDir = $this->makeTemp();
		if (preg_match( '/\.zip$/i', $filename )) {
			$zipfile = new PclZip( $filename );
			$ret = $zipfile->extract( PCLZIP_OPT_PATH, $extractDir );
			if($ret == 0) {
				aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Installer unrecoverable ZIP error %s in %s'), $zipfile->errorName(true), $filename), _ALIRO_ERROR_FATAL);
				return false;
			}
		} else {
			error_reporting(E_ALL);
			$archive = new Archive_Tar( $filename );
			$archive->setErrorHandling( PEAR_ERROR_PRINT );

			if (!$archive->extractModify( $extractDir, '' )) {
				aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Installer unrecoverable TAR error in %s'), $filename), _ALIRO_ERROR_FATAL);
				return false;
			}
			error_reporting(E_ALL|E_STRICT);
		}
		$this->mosChmodRecursive($extractDir);
		return $extractDir;
	}
	
	public function makeTemp () {
		$tempDir = criticalInfo::getInstance()->class_base.'/media/'.uniqid('_aliro_temp_').'/';
		$this->createDirectory($tempDir);
		return $tempDir;
	}
	
}

class aliroDirectory {
    private $path = '';

    public function __construct ($path) {
        $path = str_replace('\\', '/', $path);
        $this->path = ('/' == substr($path,-1)) ? $path : $path.'/';
    }
	
	public function getPath () {
		return $this->path;
	}

    public function listAll ($type='file', $recurse=false, $fullpath=false) {
        $results = array();
        if ($dir = @opendir($this->path)) {
            while ($file = readdir($dir)) {
                if ($file == '.' OR $file == '..') continue;
                if (is_dir($this->path.$file)) {
                    if ($recurse) {
                        $subdir = new aliroDirectory($this->path.$file);
                        $results = array_merge($results, $subdir->listAll($type, $recurse, $fullpath));
                        unset($subdir);
                    }
                    if ($type == 'file') continue;
                }
                elseif ($type == 'dir') continue;
                if ($fullpath) $results[] = $this->path.$file;
                else $results[] = $file;
            }
            closedir($dir);
        }
        return $results;
    }

    public function soleDir () {
        $found = '';
        if ($dir = @opendir($this->path)) {
            while ($file = readdir($dir)) {
                if ($file == '.' OR $file == '..') continue;
                if (is_dir($this->path.$file)) {
                    if ($found) return '';
                    else $found = $file;
                }
                else return '';
            }
            closedir($dir);
        }
        return $found;
    }

    public function deleteAll () {
        if (!file_exists($this->path)) return;
        $subdirs = $this->listAll ('dir', false, true);
        foreach ($subdirs as $subdir) {
            $subdirectory = new aliroDirectory($subdir);
            $subdirectory->deleteAll();
            unset($subdirectory);
        }
		$this->deleteFiles(false);
        aliroFileManager::getInstance()->deleteDirectory($this->path);
    }
	
	public function deleteFiles ($keepstandard=true) {
        $filemanager = aliroFileManager::getInstance();
        $files = $this->listAll ('file', false, true);
        foreach ($files as $file) {
			$filename = basename($file);
			if (!$keepstandard OR ('index.html' != $filename AND '.' != $filename[0])) $filemanager->deleteFile($file);
		}
	}

    public function createFresh () {
        $this->deleteAll();
        $filemanager = aliroFileManager::getInstance();
        $filemanager->createDirectory($this->path);
        return true;
    }

    public function createIfNeeded () {
        if (!file_exists($this->path)) {
            $filemanager = aliroFileManager::getInstance();
            $filemanager->createDirectory($this->path);
        }
    }

    public function listFiles ($pattern='', $type='file', $recurse=false, $fullpath=false) {
        $results = array();
        $all = $this->listAll($type, $recurse, $fullpath);
        foreach ($all as $file) {
            $name = basename($file);
            if ($pattern AND !preg_match( "/$pattern/", $name )) continue;
            if (($name != 'index.html') AND ($name[0] != '.')) $results[] = $file;
        }
        return $results;
    }

    public function getSize () {
        $totalsize = 0;
        $files = $this->listFiles();
        foreach ($files as $file) $totalsize += filesize($this->path.$file);
        return $totalsize;
    }
	
	public function zip ($zipname) {
        $zipfile = $this->path.$zipname;
		$files = $this->listAll('file', true, true);
		$zip = new ZipArchive();
		if (!$zip->open($zipfile, ZIPARCHIVE::CREATE)) trigger_error(T_('Unable to open zip file ').$zipfile);
		else foreach ($files as $file) $zip->addFile($file,substr($file,strlen($this->path)));
		$zip->close();
		return $zipfile;
	}


}