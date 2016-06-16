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
 * aliroExtension is the data class for an extension, corresponding to a row
 * in the extensions table.
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

class aliroExtension extends aliroDatabaseRow  {
	private static $legalTypes = array('component', 'module', 'mambot', 'plugin', 'template', 'language', 'patch');
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__extensions';
	protected $rowKey = 'id';

	public function populateFromXML ($xmlobject) {
		$purifier = new HTMLPurifier();
		$this->name = $purifier->purify((string) $xmlobject->getXML('name'));
		$this->type = $xmlobject->baseAttribute('type');
		if (!in_array($this->type, self::$legalTypes)) return T_('has no valid type');
		if ('plugin' == $this->type) $extension->type = 'mambot';
		$this->formalname = $purifier->purify((string) $xmlobject->getXML('formalname'));
		if (!$this->formalname AND 'component' == strtolower($this->type)) $this->formalname = 'com_'.str_replace(' ', '', strtolower($this->name));
		if (!$this->formalname) return T_('has no formal name');
		$this->admin = ('administrator' == $xmlobject->baseAttribute('client')) ? 2 : 1;
		$this->inner = ('yes' == $xmlobject->baseAttribute('inner')) ? 1 : 0;
		if ('template' == $this->type) {
			$currentDefault = aliroTemplateHandler::getInstance()->getDefaultTemplateProperty('formalname', (2 == $this->admin));
			if (!$currentDefault OR $currentDefault == $this->formalname) $this->default_template = '1';
		}
		foreach (array('author', 'version', 'authoremail', 'authorurl', 'description', 'creationdate') as $field) {
			$this->$field = $purifier->purify((string) $xmlobject->getXML($field));
		}
		$this->date = $this->creationdate;
		unset($this->creationdate);
		foreach (array('adminclass', 'menuclass', 'exportclass') as $field) {
			$this->$field = $xmlobject->baseAttribute($field);
		}
		$this->class = $xmlobject->baseAttribute('userclass');
		$this->timestamp = date('Y-m-d');
		return false;
	}
}

class aliroExtensionHandler extends cachedSingleton  {
	protected static $instance = __CLASS__;
	private $extensions = array();
	private $extensionsByType = array();
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
	    	foreach ($this->extensions as $extension) $this->extensionsByType[$extension->type][$extension->formalname] = $extension;
		}
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	public function checkStarterPack () {
		if (0 == count($this->extensions)) {
			$starterpack = criticalInfo::getInstance()->admin_absolute_path.'/starterpack/';
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
		$this->deleteFromTable($extlist, 'mambots', 'element', $database);
		$this->deleteFromTable($extlist, 'classmap', 'formalname', $database);
		if (!$isUpgrade) $database->doSQL("DELETE FROM `#__menu` WHERE `component` != '' AND `component` IN ('$extlist')");
		$database->doSQL("DELETE FROM `#__admin_menu` WHERE `component` != '' AND `component` IN ('$extlist')");
		$database->doSQL("DELETE `#__modules_menu` FROM `#__modules_menu` LEFT JOIN `#__modules` ON `moduleid`=`id` WHERE `id` IS NULL");
		aliroMenuHandler::getInstance()->clearCache();
		$this->clearCache();
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
		if ('component' == $extension->type AND file_exists(_ALIRO_ABSOLUTE_PATH.$extension->xmlfile)) {
			$installer = new aliroExtensionInstaller(_ALIRO_ABSOLUTE_PATH.$extension->xmlfile);
			$installer->removeComponent($handler, $extension);
		}
	}

	public function getExtensions ($type='') {
		if ($type) return isset($this->extensionsByType[$type]) ? $this->extensionsByType[$type] : array();
		return $this->extensions;
	}

	public function getExtensionByName ($formalname) {
		return isset($this->extensions[$formalname]) ? $this->extensions[$formalname] : null;
	}

	public function getXMLFileName ($formalname) {
		if ($ext = $this->getExtensionByName ($formalname)) return $ext->xmlfile;
		return '';
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
		return criticalInfo::getInstance()->absolute_path.$this->getRelativePath($formalname, $admin);
	}

	// Provided mainly for the installer
	public function getRelativePath ($formalname, $admin) {
		$extradir = (_ALIRO_ADMIN_SIDE == $admin) ? criticalInfo::getInstance()->admin_dir : '';
		return $extradir.$this->extensiondir.$formalname;
	}

	// Provided mainly for the installer
	public function getClassPath ($formalname, $admin) {
		return criticalInfo::getInstance()->class_base.$this->getRelativePath($formalname, $admin);
	}

	// Mainly for the installer - overrides default method
	public function getXMLRelativePath ($formalname, $admin) {
		return $this->getRelativePath ($formalname, $admin);
	}

	// Mainly for the installer - overrides default method
	public function getXMLPath ($formalname, $admin) {
		return criticalInfo::getInstance()->absolute_path.$this->getXMLRelativePath ($formalname, $admin);
	}

	// Provided mainly for the installer
	public function createDirectory ($formalname, $admin) {
		$dir = new aliroDirectory ($this->getPath($formalname, $admin));
		return $dir->createFresh();
	}

	// Provided for uninstaller, but not currently used 
	public function remove ($formalname) {
		$info = criticalInfo::getInstance();
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
			$this->xmlobject = simplexml_load_file(aliroCore::getInstance()->getCfg('absolute_path').$extension->xmlfile);
		}
		return $this->xmlobject;
	}

}