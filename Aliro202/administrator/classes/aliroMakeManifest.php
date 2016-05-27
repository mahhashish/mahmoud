<?php

class aliroMakeManifest {
	private $bases = array();
	private $modifiable = array();
	private $oddfiles = array();
	private $ignoreSuffix = array();
	private $manifest = '';
	private $marray = array();
	private $tempdir = '';
	private $fmanager = null;
	private $reports = array();
	private $directories = array();
	private $corefiles = array();
	private $noncorefiles = array();

	public function __construct ($adminpath='', $adminclass='') {
		$this->bases = array(
		'class' => _ALIRO_CLASS_BASE,
		'absolute' => _ALIRO_ABSOLUTE_PATH,
		'adminclass' => ($adminclass ? $adminclass : _ALIRO_ADMIN_CLASS_BASE),
		'adminabs' => ($adminpath ? $adminpath : _ALIRO_ADMIN_PATH)
		);
	
		$this->modifiable = array(
		array('class', '/bootstrap'),
		array('class', '/classes'),
		array('class', '/extclasses', true),
		array('absolute', '/editor'),
		array('absolute', '/help', true),
		array('absolute', '/includes/Archive', true),
		array('absolute', '/includes/js', true),
		array('absolute', '/includes/magpierss', true),
		array('absolute', '/templates'),
		array('absolute', '/xml'),
		array('absolute', '/images', true, true),
		array('adminclass', '/classes', true),
		array('adminabs', '/images', false, true),
		array('adminabs', '/templates'),
		array('adminabs', '/templates/images'),
		array('adminabs', '/includes', true),
		array('adminclass', '/sql'),
		array('adminclass', '/bootstrap')
		);
		
		$this->oddfiles = array(
		array('absolute', '/index.php'),
		array('absolute', '/index2.php'),
		array('absolute', '/basic.robots.txt'),
		array('absolute', '/language/index.html'),
		array('absolute', '/components/index.html'),
		array('absolute', '/modules/index.html'),
		array('absolute', '/mambots/index.html'),
		array('absolute', '/language/locales.xml'),
		array('absolute', '/language/english.ignore.php'),
		array('absolute', '/language/english.php'),
		array('absolute', '/language/en/en.xml'),
		array('class', '/configs/index.html'),
		array('class', '/cache/html/index.html'),
		array('class', '/cache/.htaccess'),
		array('class', '/cache/index.html'),
		array('class', '/cache/singleton/index.html'),
		array('class', '/cache/rssfeeds/index.html'),
		array('class', '/cache/html/index.html'),
		array('class', '/cache/HTMLPurifier/index.html'),
		array('class', '/oemclasses/index.html'),
		array('class', '/parameters/index.html'),
		array('absolute', '/media/index.html'),
		array('class', '/tmp/index.html'),
		array('class', '/datafiles/index.html'),
		array('absolute', '/includes/feedcreator.class.php'),
		array('absolute', '/includes/mambofunc.php'),
		array('absolute', '/includes/index.html'),
		array('absolute', '/includes/pageNavigation.php'),
		array('adminabs', '/index.php'),
		array('adminabs', '/index2.php'),
		array('adminabs', '/components/index.html'),
		array('adminclass', '/starterpack/index.html'),
		array('adminabs', '/modules/index.html'),
		);
		
		$this->ignoreSuffix = array ('ser');
	}
	
	private function processAllFiles ($method) {
		clearstatcache();
		foreach ($this->modifiable as $dirarr) {
			$base = $this->bases[$dirarr[0]];
			$blen = strlen($base);
			$dir = new aliroDirectory($base.$dirarr[1]);
			$recurse = isset($dirarr[2]) ? $dirarr[2] : false;
			$deletable = empty($dirarr[3]) ? 'yes' : 'no';
			$files = $dir->listAll('file', $recurse, true);
			foreach ($files as $file) {
				if ('.' == substr(basename($file),0,1) OR false !== strpos($file, '/.svn/')) continue;
				$parts = explode('.', $file);
				$last = count($parts)-1;
				if (0 < $last AND in_array(trim($parts[$last]), $this->ignoreSuffix)) continue;
				$filepart = substr($file,$blen);
				$this->$method($dirarr[0], $base, $filepart, $deletable);
				$this->directories[dirname($base.$filepart)][] = $base.$filepart;
			}
		}
		
		foreach ($this->oddfiles as $filarr) {
			$base = $this->bases[$filarr[0]];
			$this->$method($filarr[0], $base, $filarr[1], 'yes');
			$this->directories[dirname($base.$filarr[1])][] = $base.$filarr[1];
		}
	}
	
	private function addOneFile ($type, $base, $file, $deletable) {
		$hash = $this->md5file($base.$file);
		$this->manifest .= "\n\t\t<filename basetype=\"$type\" md5=\"$hash\" deletable=\"$deletable\">$file</filename>";
	}
	
	private function checkOneFile ($type, $base, $file) {
		$hash = $this->md5file($base.$file);
		if (isset($this->marray[$type][$file]['md5'])) {
			if ($hash == $this->marray[$type][$file]['md5']) {
				unset($this->marray[$type][$file]);
				return;
			}
			else $this->marray[$type][$file]['action'] = 'update';
		}
		else $this->marray[$type][$file]['action'] = 'add';
		$this->fmanager->forceCopy($base.$file, $this->tempdir.$type.'/'.$file);
	}
	
	private function addFileToCore ($type, $base, $file) {
		$this->corefiles[$base.$file] = 1;
	}
	
	private function startManifest () {
		$this->manifest = <<<START_MANIFEST
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE manifest SYSTEM "http://www.aliro.org/xml/manifest.dtd">
<manifest type="info" classbase="{$this->bases['class']}" absbase="{$this->bases['absolute']}" adminclassbase="{$this->bases['adminclass']}" adminbase="{$this->bases['adminabs']}">
	<files>
START_MANIFEST;

	}
	
	private function midManifest ($killfiles) {
		if ($killfiles) $this->manifest .= <<<KILL_MANIFEST
		
	</files>
	<killfiles>
		$killfiles
	</killfiles>
</manifest>

KILL_MANIFEST;

		else $this->endManifest();

	}
	
	private function endManifest () {
		$this->manifest .= <<<END_MANIFEST
		
	</files>
</manifest>
		
END_MANIFEST;

	}
	
	private function reportResults () {
		$dirunwriteable = T_('Cannot write to this directory');
		$filunwriteable = T_('Cannot write to file: ');
		$problem = false;
		foreach ($this->directories as $directory=>$files) {
			if (is_dir($directory) AND !is_writeable($directory)) {
				$problemdirs[$directory] = 1;
				$problem = true;
			}
			foreach ($files as $file) if (file_exists($file) AND !is_writeable($file)) {
				if (!is_writeable($file)) {
					$problemdirs[$directory] = 1;
					$problem = true;
				}
			}
		}
			
		if (isset($problemdirs)) foreach (array_keys($problemdirs) as $directory) {
			$this->directoryHeading($directory);
			if (!is_writeable($directory)) echo <<<DIR_BLOCK

			<div>$dirunwriteable</div>
			
DIR_BLOCK;

			foreach ($this->directories[$directory] as $file) if (!is_writeable($file)) {
				$filename = basename($file);
				echo <<<FILE_BLOCK
			
			<div>$filunwriteable $file</div>
FILE_BLOCK;
				
			}
		}
		return $problem;
	}
	
	public function startUpgrade () {
		$tempdir = _ALIRO_ADMIN_PATH.'/upgrades';
		$dir = new aliroDirectory($tempdir);
		$dir->deleteAll();
		if (!aliroFileManager::getInstance()->createDirectory($tempdir)) {
			$request->setErrorMessage(sprintf(T_('Upgrading cannot be done, no writeable directory at %s.'), _ALIRO_ERROR_WARN), _ALIRO_ADMIN_PATH.'/upgrades');
			return;
		}
		$this->startManifest();
		$this->processAllFiles('addOneFile');
		$this->endManifest();
		$request = aliroRequest::getInstance();
		$problem = $this->reportResults();
		if ($problem) {
			$request->setErrorMessage(T_('Upgrading may be impossible - see details below'), _ALIRO_ERROR_WARN);
			return;
		}
		$streamer = new aliroHTTP();
		$upgradepack = $streamer->put('http://upgrade.aliro.org/upgradepack', $this->manifest);
		file_put_contents(_ALIRO_ADMIN_PATH.'/upgrades/upgrade.zip', $upgradepack);
		$request->setErrorMessage(T_('Please review the following potential upgrade, untick as required, then click on "Complete Upgrade"'), _ALIRO_ERROR_INFORM);
		$this->reviewManifest(_ALIRO_ADMIN_PATH.'/upgrades/upgrade.zip');
	}
	
	/*
	public function reviewManifest ($mfile) {
		$mdata = file_get_contents($mfile);
		$zipfile = $this->makeUpgradeFile($mfile);
		$subject = T_('Aliro Upgrade ZIP from  ').aliroCore::getInstance()->getCfg('sitename');
		$mailer = new mosMailer ('', '', $subject, '');
		$recipient = aliroUser::getInstance()->email;
		$attachment = new fileAttachment($zipfile, 'application/zip', new Base64Encoding());
		$mailresult = $mailer->mosMail($recipient, 0, null, null, $attachment);
		$request = aliroRequest::getInstance();
		if ($mailresult) $request->setErrorMessage(T_('The upgrade has been created and mailed to you'), _ALIRO_ERROR_INFORM);
		else $request->setErrorMessage(T_('The upgrade was created, but could not be mailed'), _ALIRO_ERROR_FATAL);
	}
	*/
	
	public function makeUpgradeFile ($mdata, $admindir) {
		// Must use literal value for administrator directory because this is run from user side
		// Set the define value according to where the administrator directory is located
		$this->bases = array(
		'class' => _ALIRO_CLASS_BASE,
		'absolute' => _ALIRO_ABSOLUTE_PATH,
		'adminclass' => ($adminclass ? $adminclass : _ALIRO_CLASS_BASE.$admindir),
		'adminabs' => ($adminpath ? $adminpath : _ALIRO_ABSOLUTE_PATH.$admindir)
		);
		try {
			$xmlobject = new aliroXML();
			$xmlobject->loadString($mdata);
			$manifest = $xmlobject->getXML('files->filename');
			foreach ($manifest as $manifile) {
				$type = (string) $manifile['basetype'];
				$deletable = (string) $manifile['deletable'];
				$name = (string) $manifile;
				$this->marray[$type][$name]['md5'] = (string) $manifile['md5'];
				$this->marray[$type][$name]['action'] = ('yes' == $deletable ? 'delete' : 'ignore');
			}
		}
		catch (Exception $xmlexcept) {
			$this->xmlError($xmlexcept);
		}

		$this->fmanager = aliroFileManager::getInstance();
		$this->tempdir = $this->fmanager->makeTemp();
		$this->processAllFiles('checkOneFile');
		$this->startManifest();
		foreach ($this->marray as $type=>$files) {
			foreach ($files as $filename=>$info) {
				if ('delete' == $info['action'] OR 'ignore' == $info['action']) continue;
				$md5 = $this->md5file($this->bases[$type].$filename);
				$this->manifest .= <<<FILE_LINE
				
		<filename basetype="$type" action="{$info['action']}" md5="{$md5}">$filename</filename>
FILE_LINE;

			}
		}
		$killfiles = '';
		foreach ($this->marray as $type=>$files) {
			foreach ($files as $filename=>$info) {
				if ('delete' == $info['action']) $killfiles .= <<<KILL_FILE_LINE
				
		<filename basetype="$type">$filename</filename>
KILL_FILE_LINE;

			}
		}
		$this->midManifest($killfiles);
		file_put_contents($this->tempdir.'/manifest.xml', $this->manifest);
		$dir = new aliroDirectory($this->tempdir);
		$zipfile = $dir->zip('manifest.zip');
		return $zipfile;
	}
	
	public function reviewManifest ($zipfile) {
		$tempdir = dirname($zipfile);
		$fmanager = aliroFileManager::getInstance();
		$zip = new ZipArchive();
		if (true === $zip->open($zipfile)) {
			$zip->extractTo($tempdir);
			$zip->close();
		}
		else trigger_error('Zip failure');
		$addhtml = '<h2>'.T_('Upgrade will add the following files:').'</h2>';
		$uphtml = '<h2>'.T_('Upgrade will update the following files:').'</h2>';
		$addcount = $upcount = $delcount = 0;
		try {
			$xmlobject = new aliroXML();
			$xmlobject->loadFile($tempdir.'/manifest.xml');
		
			$manifest = $xmlobject->getXML('files->filename');
			if ($manifest) foreach ($manifest as $manifile) {
				$type = (string) $manifile['basetype'];
				$name = (string) $manifile;
				$action = (string) $manifile['action'];
				$filename = $this->bases[$type].$name;
				if ('update' == $action) $uphtml .= $this->addFileLine($filename, ++$upcount, 'upfile');
				else $addhtml .= $this->addFileLine($filename, ++$addcount, 'addfile');
				$directory = dirname($filename);
				if (!file_exists($directory)) $fmanager->createDirectory($directory);
				clearstatcache();
				if (!file_exists($directory) OR !is_writeable($directory)) $dirproblems[$directory] = file_exists($directory) ? 1 : 2;
				elseif ('update' == $action AND !is_writeable($filename)) $fileproblems[$directory][$filename] = 1;
			}
			/*
			if (isset($dirproblems) OR isset($fileproblems)) {
				$this->reportUpgradeProblems(1, $dirproblems, $fileproblems);
				return;
			}
			foreach ($manifest as $manifile) {
				$type = (string) $manifile['basetype'];
				$name = (string) $manifile;
				$filename = $this->bases[$type].$name;
				$directory = dirname($filename);
				$result = $fmanager->forceCopy($tempdir.'/'.$type.$name, $this->bases[$type].$name);
				if (!$result) $fileproblems[$directory][$filename] = 1;
			}
			if (isset($fileproblems)) $this->reportUpgradeProblems(2, array(), $fileproblems);
			*/
		}
		catch (Exception $xmlexcept) {
			$this->xmlError($xmlexcept);
		}
		$manifest = $xmlobject->getXML('killfiles->filename');
		$delhtml = '<h2>'.T_('Upgrade will delete the following files:').'</h2>';
		if ($manifest) foreach ($manifest as $manifile) {
			$type = (string) $manifile['basetype'];
			$name = (string) $manifile;
			$filename = $this->bases[$type].$name;
			$delhtml .= $this->addFileLine($filename, ++$delcount, 'delfile');
			// $result = $fmanager->deleteFile($this->bases[$type].$name);
			// if (!$result) $delproblems[$directory][$filename] = 1;
		}
		if (isset($dirproblems) OR isset($fileproblems)) {
			$this->reportUpgradeProblems(1, (isset($dirproblems) ? $dirproblems : array()), (isset($fileproblems) ? $fileproblems : array()));
			//return;
		}
		$nonetext = '<p>'.T_('None').'</p>';
		$addnone = (0 == $addcount) ? $nonetext : '';
		$upnone = (0 == $upcount) ? $nonetext : '';
		$delnone = (0 == $delcount) ? $nonetext : '';
		echo <<<UPGRADE_REPORT
		
		$addhtml
		$addnone
		$uphtml
		$upnone
		$delhtml
		$delnone
		<div>
			<input type="hidden" id="task" name="task" value="" />
			<input type="hidden" name="core" value="cor_config" />
		</div>
		
UPGRADE_REPORT;

		return;
	}
	
	private function addFileLine ($name, $count, $field) {
		return <<<FILE_LINE
		
		<div>
			<input type="checkbox" class="inputbox" name="{$field}[{$count}]" value="1" checked="checked" />$name
		</div>
		
FILE_LINE;
	}
	
	private function reportUpgradeProblems ($type, $dirproblems, $fileproblems) {
		$dirunwriteable = T_('Cannot write to directory');
		$dirnonexistent = T_('This directory could not be created');
		$filunwriteable = (3 == $type) ? T_('Unable to delete - please fix manually:') : T_('Cannot write to file:');
		foreach ($dirproblems as $directory=>$marker) {
			$dirmessage = (1 == $marker) ? $dirunwriteable : $dirnonexistent;
			$this->directoryHeading();
			echo <<<DIR_BLOCK

			<div>$dirmessage</div>
			
DIR_BLOCK;

		}
		foreach ($fileproblems as $directory=>$files) {
			$this->directoryHeading($directory);
			foreach (array_keys($files) as $file) echo <<<FILE_BLOCK
			
			<div>$filunwriteable $file</div>
FILE_BLOCK;
			
		}
		aliroRequest::getInstance()->setErrorMessage(T_('Upgrade abandoned because of problems listed below'), _ALIRO_ERROR_FATAL);
	}
	
	private function directoryHeading ($directory) {
			echo <<<PROBLEM_DIR
			
			<h3>$directory</h3>
			
PROBLEM_DIR;

	}
	
	public function installManifest () {
		$tempdir = _ALIRO_ADMIN_PATH.'/upgrades';
		$request = aliroRequest::getInstance();
		$fmanager = aliroFileManager::getInstance();
		$addfiles = $request->getParam($_POST, 'addfile');
		$upfiles = $request->getParam($_POST, 'upfile');
		$delfiles = $request->getParam($_POST, 'delfile');
		try {
			$xmlobject = new aliroXML();
			$xmlobject->loadFile($tempdir.'/manifest.xml');
		
			$manifest = $xmlobject->getXML('files->filename');
			$addcount = $upcount = $delcount = 0;
			if ($manifest) foreach ($manifest as $manifile) {
				$type = (string) $manifile['basetype'];
				$name = (string) $manifile;
				$action = (string) $manifile['action'];
				$filename = $this->bases[$type].$name;
				if ('update' == $action AND !empty($upfiles[++$upcount])) $filemove = true;
				elseif ('add' == $action AND !empty($addfiles[++$addcount])) $filemove = true;
				else $filemove = false;
				if ($filemove) {
					$result = $fmanager->forceCopy($tempdir.'/'.$type.$name, $this->bases[$type].$name);
					if (!$result) $fileproblems[$directory][$filename] = 1;
				}
			}
			if (isset($fileproblems)) $this->reportUpgradeProblems(2, array(), $fileproblems);
		}
		catch (Exception $xmlexcept) {
			$this->xmlError($xmlexcept);
		}
		$manifest = $xmlobject->getXML('killfiles->filename');
		if ($manifest) foreach ($manifest as $manifile) {
			$type = (string) $manifile['basetype'];
			$name = (string) $manifile;
			$filename = $this->bases[$type].$name;
			if (!empty($delfiles[++$delcount])) {
				$result = $fmanager->deleteFile($this->bases[$type].$name);
				if (!$result) $delproblems[$directory][$filename] = 1;
			}
		}
		if (isset($delproblems)) $this->reportUpgradeProblems(3, array(), $delproblems);
		
		$dir = new aliroDirectory($tempdir);
		$dir->deleteAll();
		aliroCache::deleteAll();
		
		if (isset($fileproblems) OR isset($delproblems)) $request->setErrorMessages(T_('Problems occurred during upgrading'), _ALIRO_ERROR_SEVERE);
		else $request->setErrorMessage(T_('Upgrade has been completed successfully'), _ALIRO_ERROR_INFORM);
	}
	
	public function checkManifest ($manifest) {
		$this->reports = array();
		
	}
	
	private function md5file ($filename) {
		return md5(str_replace("\r\n", "\n", file_get_contents($filename)));
	}
	
	private function xmlError ($xmlexcept) {
		var_dump($xmlexcept);
	}
	
	public function nonCore () {
		$this->corefiles = array();
		$this->processAllFiles('addFileToCore');
		$dir = new aliroDirectory(_ALIRO_ABSOLUTE_PATH);
		$this->crawlPaths($dir);
		if (0 !== strpos(_ALIRO_CLASS_BASE, _ALIRO_ABSOLUTE_PATH)) {
			$dir = new aliroDirectory(_ALIRO_CLASS_BASE);
			$this->crawlPaths($dir);
		}
		if (0 !== strpos(_ALIRO_ADMIN_PATH, _ALIRO_ABSOLUTE_PATH) AND 0 !== strpos(_ALIRO_ADMIN_PATH, _ALIRO_CLASS_BASE)) {
			$dir = new aliroDirectory(_ALIRO_ADMIN_PATH);
			$this->crawlPaths($dir);
		}
		foreach (array_keys($this->noncorefiles) as $file) echo '<br />Non core file: '.$file;
	}
	
	private function crawlPaths ($dir) {
		$files = $dir->listAll ('file', true, true);
		foreach ($files as $file) if (!strpos($file, '/.svn/') AND 0 !==strpos($file, _ALIRO_CLASS_BASE.'/tmp') AND !isset($this->corefiles[$file])) $this->noncorefiles[$file] = 1;
	}
	
}