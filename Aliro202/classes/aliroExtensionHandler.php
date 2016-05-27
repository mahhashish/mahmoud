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
 * aliroExtensionHandler knows all about the various installed extensions in
 * the system.  Anything not integral to the core - components, modules, mambots,
 * templates - are counted as extensions.  It is a cached singleton class and
 * uses common code the implement the object cache.
 *
 * aliroCommonExtHandler is an abstract class that is the base for various
 * different extension handlers, aliroComponentHandler, aliroModuleHandler
 * and aliroMambotHandler.
 *
 */

final class aliroExtensionHandler extends cachedSingleton  {
	protected static $instance = __CLASS__;
	private $extensions = array();
	private $extensionsByType = array();
	private $extensionsByApp = array();
	private $templates = array();

	protected function __construct () {
		$results = aliroCoreDatabase::getInstance()->doSQLget("SELECT * FROM #__extensions", 'aliroExtension');
		if ($results) {
	    	foreach ($results as $extension) {
	    		$this->extensions[$extension->formalname] = $extension;
	    		if ($extension->type == 'template') $this->templates[] = $extension->formalname;
	    	}
	    	// Sort by formal name (unique)
	    	ksort($this->extensions);
	    	foreach ($this->extensions as $extension) {
	    		$this->extensionsByType[$extension->type][$extension->formalname] = $extension;
	    		$this->extensionsByApp[$extension->application][$extension->formalname] = $extension;
	    	}
		}
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}
	
	public function getExtensionByID ($id) {
		foreach ($this->extensions as $extension) if ($id == $extension->id) return $extension;
		return null;
	}

	public function checkStarterPack () {
		if (0 == count($this->extensions)) {
			$starterpack = _ALIRO_ADMIN_CLASS_BASE.'/starterpack/';
			$dir = new aliroDirectory($starterpack);
			foreach ($dir->listAll() as $package) if ('index.html' != $package) {
				$installer = new aliroInstaller();
				$installer->installfromfile($starterpack.$package);
			}
		}
	}

	public function getTemplateExtensions () {
		$result = array();
		foreach ($this->templates as $templatename) $result[$templatename] = $this->extensions[$templatename];
		return $result;
	}

	public function removeExtensions ($formalnames, $isUpgrade=false) {
		$extlist = implode("', '", (array) $formalnames);
		foreach ((array) $formalnames as $formalname) {
			if ($extension = $this->getExtensionByName ($formalname)) {
				$handler = aliroExtensionHandler::getExtensionTypeHandler($extension->type);
				if (!$isUpgrade) $this->uninstallExtension($handler, $extension);
				$this->removeExtensionFiles($handler, $extension);
			}
		}
		$database = aliroCoreDatabase::getInstance();
		$this->deleteFromTable($extlist, 'extensions', 'formalname', $database);
		$this->deleteFromTable($extlist, 'components', 'option', $database);
		if (!$isUpgrade) $this->deleteFromTable($extlist, 'modules', 'module', $database);
		if (!$isUpgrade) $this->deleteFromTable($extlist, 'mambots', 'element', $database);
		$this->deleteFromTable($extlist, 'classmap', 'formalname', $database);
		if (!$isUpgrade) $database->doSQL("DELETE FROM `#__menu` WHERE `component` != '' AND `component` IN ('$extlist')");
		$database->doSQL("DELETE FROM `#__admin_menu` WHERE `component` != '' AND `component` IN ('$extlist')");
		if (!$isUpgrade) $this->tidyModulesPageControl();
		aliroMenuHandler::getInstance()->clearCache();
		$this->clearCache();
	}
	
	public function removeAllBut ($type, $formalnames, $application) {
		$all = $this->getNamesByApplicationAndType($application, $type);
		$this->removeExtensions(array_diff($all, $formalnames));
	}
	
	public function tidyModulesPageControl () {
		aliroCoreDatabase::getInstance()->doSQL("DELETE `#__modules_menu` FROM `#__modules_menu` LEFT JOIN `#__modules` ON `moduleid`=`id` WHERE `id` IS NULL");
	}
	
	public function removeApplications ($applications, $isUpgrade=false) {
		foreach ((array) $applications as $application) {
			$extensions = $this->getExtensionsByApplication($application);
			$this->removeExtensions(array_keys($extensions), $isUpgrade);
		}
		$applist = implode("', '", (array) $applications);
		if (!$isUpgrade) {
			$database = aliroCoreDatabase::getInstance();
			$this->deleteFromTable($applist, 'urilinks', 'application', $database);
		}
	}

	private function deleteFromTable ($extlist, $table, $fieldname, $database) {
		$database->doSQL("DELETE FROM `#__$table` WHERE `$fieldname` IN ('$extlist')");
	}

	private function removeExtensionFiles ($handler, $extension) {
		if ($handler) {
			$handler->remove($extension->formalname, $extension->admin);
			$handler->clearCache();
		}
	}

	private function uninstallExtension ($handler, $extension) {
		if ($extension->package) {
			$packagefile = _ALIRO_ADMIN_CLASS_BASE.'/starterpack/'.$extension->package;
			if (file_exists($packagefile)) aliroFileManager::getInstance()->moveFile($packagefile, _ALIRO_ADMIN_CLASS_BASE.'/oldextensions');
		}
		$installer = new aliroExtensionInstaller(_ALIRO_ADMIN_PATH.$extension->xmlfile);
		if (('application' == $extension->type OR 'component' == $extension->type) AND file_exists(_ALIRO_ADMIN_PATH.$extension->xmlfile)) {
			if ('component' == $extension->type) $installer->removeComponent($handler, $extension);
			else $installer->removeApplication($handler, $extension);
		}
		elseif ('mambot' == $extension->type) $installer->removeMambot($handler, $extension);
	}

	public function getExtensions ($type='') {
		if ($type) return isset($this->extensionsByType[$type]) ? $this->extensionsByType[$type] : array();
		return $this->extensions;
	}

	public function getExtensionByName ($formalname) {
		return isset($this->extensions[$formalname]) ? $this->extensions[$formalname] : null;
	}
	
	public function getExtensionsByApplication ($application) {
		return isset($this->extensionsByApp[$application]) ? $this->extensionsByApp[$application] : array();
	}
	
	protected function getNamesByApplicationAndType ($application, $type) {
		$extensions = $this->getExtensionsByApplication($application);
		foreach ($extensions as $extension) if ($type == $extension->type) $results[] = $extension->formalname;
		return isset($results) ? $results : array();
	}

	public function getParmSpecString ($formalname) {
		if ($ext = $this->getExtensionByName ($formalname)) return $ext->parmspec;
		return '';
	}
	
	public function getParamsObject ($params, $formalname) {
		$parmspecstring = $this->getParmSpecString($formalname);
		$pobject = new aliroParameters($params, $parmspecstring);
		return $pobject;
	}

	public static function getExtensionTypeHandler ($type) {
		$prettytype = strtoupper(substr($type,0,1)).strtolower(substr($type,1));
		$handlername = 'aliro'.$prettytype.'Handler';
		if (aliro::getInstance()->classExists($handlername)) return call_user_func(array($handlername, 'getInstance'));
		else return null;
	}

}

abstract class aliroCommonExtHandler extends cachedSingleton {

	// Provided mainly for the installer
	public function getPath ($formalname, $admin) {
		return _ALIRO_ABSOLUTE_PATH.$this->getRelativePath($formalname, $admin);
	}

	// Provided mainly for the installer
	public function getRelativePath ($formalname, $admin) {
		$extradir = (_ALIRO_ADMIN_SIDE == $admin) ? criticalInfo::getInstance()->admin_dir : '';
		return $extradir.$this->extensiondir.$formalname;
	}

	// Provided mainly for the installer
	public function getClassPath ($formalname, $admin) {
		return _ALIRO_CLASS_BASE.$this->getRelativePath($formalname, $admin);
	}

	// Mainly for the installer 
	// NOTE the XML path is relative to CMS root for user side, or admin dir for admin side
	public function getXMLRelativePath ($formalname) {
		return $this->getRelativePath ($formalname, false);
	}

	// Mainly for the installer - overrides default method
	public function getXMLPath ($formalname, $admin) {
		$mainpath = $admin ? _ALIRO_ADMIN_PATH : _ALIRO_ABSOLUTE_PATH;
		return $mainpath.$this->getXMLRelativePath ($formalname, $admin);
	}

	// Provided mainly for the installer
	public function createDirectory ($formalname, $admin) {
		$dir = new aliroDirectory ($this->getPath($formalname, $admin));
		return $dir->createFresh();
	}

	// Provided for uninstaller, but not currently used 
	public function remove ($formalname) {
		$this->deleteDirectory ($this->getPath($formalname, _ALIRO_USER_SIDE));
		$this->deleteDirectory ($this->getPath($formalname, _ALIRO_ADMIN_SIDE));
		$this->clearCache();
	}

	private function deleteDirectory ($path) {
		$dir = new aliroDirectory($path);
		$dir->deleteAll();
	}

	public function clearCache ($immediate=false) {
		aliroExtensionHandler::getInstance()->clearCache(true);
		parent::clearCache($immediate);
	}

}

abstract class aliroCommonExtBase extends aliroDatabaseRow {
	protected $xmlobject = null;

	public function getXMLObject () {
		if (is_null($this->xmlobject)) {
			$field = $this->formalfield;
			$formalname = $this->$field;
			$extension = aliroExtensionHandler::getInstance()->getExtensionByName ($formalname);
			$this->xmlobject = simplexml_load_file(_ALIRO_ADMIN_PATH.$extension->xmlfile);
		}
		return $this->xmlobject;
	}

}
