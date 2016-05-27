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
 * Everything here is to do with database management.
 *
 * aliroInstallXML is the class that handles the XML that defines the packaginf
 * of any extension.  It is an extension of aliroCommonInstallXML - the base
 * class for both install and uninstall XML handling.
 *
 * aliroInstaller does the real work of installing extensions
 *
 */

class aliroInstaller {
	private $request = null;
	private $archiveName = '';
	private $extractDir = '';
	private $cleanDir = '';
	private $baseDir = '';
	private $package = '';
	private $isUpgrade = false;

	function __construct () {
		$this->request = aliroRequest::getInstance();
		$this->baseDir = _ALIRO_SITE_BASE.'/tmp/';
	}

	function __destruct() {
		if ($this->archiveName) {
			$fmanager = aliroFileManager::getInstance();
			$fmanager->deleteFile($this->archiveName);
		}
		if ($this->cleanDir) {
			$edir = new aliroDirectory ($this->cleanDir);
			$edir->deleteAll();
		}
	}

	public function uploadfile($isUpgrade=false) {
		$this->isUpgrade = $isUpgrade;
		// Check if file uploads are enabled
		if (!(bool)ini_get('file_uploads')) {
			$this->request->setErrorMessage (T_('The installer can\'t continue before file uploads are enabled. Please use the install from directory method.'), _ALIRO_ERROR_FATAL);
			return;
		}
		// Check that the zlib is available
		if(!extension_loaded('zlib')) {
			$this->request->setErrorMessage (T_('The installer can\'t continue before zlib is installed'), _ALIRO_ERROR_FATAL);
			return;
		}
		$userfile = $this->request->getParam( $_FILES, 'userfile', null, _MOS_NOSTRIP );
		if (!$userfile) {
			$this->request->setErrorMessage ( T_('No file selected'), _ALIRO_ERROR_FATAL);
			return;
		}
		$userfile_name = $userfile['name'];
		$msg = '';
		if ($this->getUpload( $userfile['tmp_name'], $userfile['name'], $msg )) {
			if (!$this->extractInstallArchive( $this->archiveName )) {
				return;
			}
			$ret = $this->topLevelInstall();
		}
		else $this->request->setErrorMessage (sprintf(T_('Upload add-on:  Upload Error - %s'), $msg), _ALIRO_ERROR_FATAL);
	}

	public function installfromfile($file, $isUpgrade=false) {
		$this->isUpgrade = $isUpgrade;
		while ($file != ($cleanfile = str_replace('..', '.', $file))) $file = $cleanfile;
		// Check that the zlib is available
		if(!extension_loaded('zlib')) {
			$this->request->setErrorMessage (T_('The installer can\'t continue before zlib is installed'), _ALIRO_ERROR_FATAL);
			return;
		}
		$fmanager = aliroFileManager::getInstance();
		if (file_exists( $this->baseDir )) {
			if (is_writable( $this->baseDir )) {
				if ($fmanager->forceCopy($file, $this->baseDir.basename($file), true)) {
					$this->archiveName = $this->baseDir.basename($file);
					if ($this->extractInstallArchive( $this->archiveName)) {
						$ret = $this->install();
						return;
					}
					else $msg = sprintf(T_('Failed to extract archive %s'), basename($file));
				}
				else {
					var_dump($file, $this->baseDir);
					$msg = sprintf(T_('Failed to copy file %s to <code>/tmp</code> directory.'), $file);
				}
			}
			else $msg = T_('Upload failed as <code>/tmp</code> directory is not writable.');
		}
		else $msg = T_('Upload failed as <code>/tmp</code> directory does not exist.');
		$this->request->setErrorMessage (sprintf(T_('Upload add-on:  Upload Error - %s'), $msg), _ALIRO_ERROR_FATAL);
	}

	public function installfromurl($userurl, $isUpgrade=false) {
		$this->isUpgrade = $isUpgrade;
		// Check that the zlib is available
		if(!extension_loaded('zlib')) {
			$this->request->setErrorMessage ( T_('The installer can\'t continue before zlib is installed'), _ALIRO_ERROR_FATAL);
			return;
		}
		if (!$userurl) {
			$this->request->setErrorMessage (T_('Please select an HTTP URL'), _ALIRO_ERROR_FATAL);
			return;
		}
		$http = new aliroHTTP();
		$options['STREAMS_RETURNTRANSFER']	= 1;							// return results as string
		$options['STREAMS_ASYNCRONOUS']		= 0;							// wait for/read results
		$http->setOptions($options);
		$filedata = $http->get($userurl);
		$headers = $http->getHeaders();
		$split = explode('filename="', (isset($headers['Content-Disposition']) ? $headers['Content-Disposition'] : ''));
		if (isset($split[1])) $userfilename = substr($split[1],0,-1);
		else $userfilename = basename($userurl);
		if (!$userfilename) {
			$this->request->setErrorMessage (T_('The URL did not define a file name'), _ALIRO_ERROR_FATAL);
			return;
		}
		$msg = '';
		if ($this->getUrl($filedata, $userfilename, $msg )) {
			if (!$this->extractArchive($userfilename)) {
				$this->request->setErrorMessage (T_('Upload add-on:  unable to extract archive'), _ALIRO_ERROR_FATAL);
				return;
			}
			$ret = $this->topLevelInstall();
		} else $this->request->setErrorMessage (sprintf(T_('Upload add-on:  Upload Error - %s'), $msg), _ALIRO_ERROR_FATAL);
	}

	private function topLevelInstall ($p_fromdir=null) {
		$here = is_null($p_fromdir) ? $this->extractDir : $p_fromdir;
		$installdir = new aliroDirectory($here);
		$xmlfiles = $installdir->listFiles('.xml$');
		$zipfiles = $installdir->listFiles('.zip$');
		$tarfiles = $installdir->listFiles('.tar.gz$');
		if (0 == count($xmlfiles)) {
			$zipfiles = $installdir->listFiles('.zip$');
			$tarfiles = $installdir->listFiles('.tar.gz$');
			foreach ($zipfiles as $file) $this->installfromfile($here.$file, $this->isUpgrade);
			foreach ($tarfiles as $file) $this->installfromfile($here.$file, $this->isUpgrade);
		}
		else return $this->handleXMLFiles($xmlfiles, $here);
	}

	private function handleXMLFiles ($xmlfiles, $here) {
		$success = true;
		foreach ($xmlfiles as $file) {
			$parser = new aliroExtensionInstaller($here.$file, $this->package);
			if (!$parser->install($this->isUpgrade)) $success = false;
		}
		return $success;
	}

	private function getUpload( $filename, $userfile_name, &$msg ) {
		$fmanager = aliroFileManager::getInstance();
		if (file_exists( $this->baseDir )) {
			if (is_writable( $this->baseDir )) {
				if (move_uploaded_file( $filename, $this->baseDir . $userfile_name )) {
					$this->archiveName = $this->baseDir.$userfile_name;
				    if ($fmanager->mosChmod( $this->baseDir . $userfile_name )) {
				        return true;
					}
					else $msg = T_('Failed to change the permissions of the uploaded file.');
				}
				else $msg = sprintf(T_('Failed to move uploaded file to <code>%s</code> directory.'), $this->baseDir);
			}
			else $msg = sprintf(T_('Upload failed as <code>%s</code> directory is not writable.'), $this->baseDir);
		}
		else $msg = sprintf(T_('Upload failed as <code>%s</code> directory does not exist.'), $this->baseDir);
		return false;
	}

	private function getFile( $file, &$msg ) {
	}

	private function getUrl( $filedata, $userfilename, &$msg ) {
		if (file_exists( $this->baseDir )) {
			if (is_writable( $this->baseDir )) {
				if ($filedata) {
				    if (file_put_contents($this->baseDir.$userfilename, $filedata)) {
						$this->archiveName = $this->baseDir.$userfilename;
						return true;
					}
					else $msg = T_('Failed to write the local file from the URL.');
				}
				else $msg = T_('Failed to open the specified URL.');
			}
			else $msg = T_('Upload failed as <code>/tmp</code> directory is not writable.');
		}
		else $msg = T_('Upload failed as <code>/tmp</code> directory does not exist.');
		return false;
	}

	private function extractInstallArchive ($filename) {
		$extractDir = aliroFileManager::getInstance()->extractArchive($filename);
		// Try to find the correct install dir. in case that the package have subdirs
		// Save the install dir for later cleanup
		if ($extractDir) {
			$this->cleanDir = $extractDir;
			$dir = new aliroDirectory($extractDir);
			$singledir = $dir->soleDir();
			$this->extractDir = $singledir ? $extractDir.$singledir.'/' : $extractDir;
			$this->package = basename($filename);
			aliroFileManager::getInstance()->forceCopy($filename, _ALIRO_ADMIN_CLASS_BASE.'/starterpack/'.$this->package, true);
			return true;
		}
		return false;
	}

	private function extractArchive($filename) {
		$this->archiveName = $this->baseDir.$filename;
		$this->extractDir = $this->cleanDir = $this->baseDir.uniqid('install_').'/';
		if (preg_match( '/.zip$/i', $filename )) {
			$zipfile = new PclZip( $this->archiveName );
			$ret = $zipfile->extract( PCLZIP_OPT_PATH, $this->extractDir );
			if($ret == 0) {
				aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Installer unrecoverable ZIP error %s in %s'), $zipfile->errorName(true), $this->archiveName), _ALIRO_ERROR_FATAL);
				return false;
			}
		} else {
			error_reporting(E_ALL);
			$archive = new Archive_Tar( $this->archiveName );
			$archive->setErrorHandling( PEAR_ERROR_PRINT );

			if (!$archive->extractModify( $this->extractDir, '' )) {
				aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Installer unrecoverable TAR error in %s'), $this->archiveName), _ALIRO_ERROR_FATAL);
				return false;
			}
			error_reporting(E_ALL|E_STRICT);
		}
		// Try to find the correct install dir. in case that the package have subdirs
		// Save the install dir for later cleanup
		$dir = new aliroDirectory($this->extractDir);
		$singledir = $dir->soleDir();
		if ($singledir) $this->extractDir = $this->extractDir.$singledir.'/';
		$this->package = basename($this->archiveName);
		aliroFileManager::getInstance()->forceCopy($this->archiveName, _ALIRO_ADMIN_CLASS_BASE.'/starterpack/'.$this->package, true);
		return true;
	}

	private function install($p_fromdir = null) {
		$here = is_null($p_fromdir) ? $this->extractDir : $p_fromdir;
		$installdir = new aliroDirectory($here);
		$xmlfiles = $installdir->listFiles('.xml$');
		return $this->handleXMLFiles ($xmlfiles, $here);
	}

}