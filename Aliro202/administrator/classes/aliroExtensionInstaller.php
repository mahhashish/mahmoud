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
 * aliroExtensionInstaller is the class that handles the installation of extensions
 *
 * aliroExtensionUninstaller is the class that handles removal of any extension.
 *
 */

class installerException extends Exception {
	public $extension = null;

	public function __construct ($message, $extension) {
		$basemessage = T_('Installer error: XML file %s for extension %s - ');
		$this->extension = $extension;
		parent::__construct($basemessage.$message, 0);
	}

}

class aliroLanguageHandler extends aliroCommonExtHandler  {
	protected static $instance = __CLASS__;
	protected $extensiondir = '/language/';

	// Singleton accessor with cache
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}
}

class aliroPatchHandler extends aliroCommonExtHandler {
	protected static $instance = __CLASS__;
	protected $extensiondir = '/';

	// Singleton accessor with cache
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}
}

class aliroIncludeHandler extends aliroCommonExtHandler  {
	protected static $instance = __CLASS__;
	protected $extensiondir = '/includes/';

	// Singleton accessor with cache
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}
}

class aliroParameterHandler extends aliroCommonExtHandler  {
	protected static $instance = __CLASS__;
	protected $extensiondir = '/parameters/';

	// Singleton accessor with cache
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}
}

class aliroExtensionInstaller extends aliroFriendlyBase  {
	//protected $legalTypes = array('component', 'module', 'mambot', 'plugin', 'template', 'language', 'patch');

	// These are given real values by the constructor
	protected $xmlfile = '';
	protected $package = '';
	protected $xmlobject = null;
	protected $here = '';
	protected $purifier = null;
	protected $uppedexts = array();

	// This is given a real value by the install method
	protected $handler = null;

	public function __construct ($xmlfile, $package='') {
		$this->xmlfile = $xmlfile;
		$this->package = $package;
		$this->xmlobject = new aliroXML;
		try {
			$this->xmlobject->loadFile($xmlfile);
		} catch (aliroXMLException $exception) {
	    		if (!$exception->isDTD) aliroRequest::getInstance()->setErrorMessage ($exception->getMessage(), _ALIRO_ERROR_FATAL);
	    		$this->xmlobject = null;
		}
		if ($this->xmlobject) {
			$this->here = dirname($xmlfile).'/';
			$this->purifier = new aliroPurifier;
		}
	}

	public function install ($isUpgrade) {
		if (!$this->xmlobject) return;
		try {
			$extension = $this->createExtension();
			if ($this->extensionExists($extension)) {
				if (!$isUpgrade) throw new installerException (T_('cannot be installed - already present'), $extension);
				// Must be an upgrade, and $isUpgrade must be true
			}
			elseif ($isUpgrade) throw new installerException (T_('cannot be upgraded - not presently installed'), $extension);
			aliroExtensionHandler::getInstance()->removeApplications($extension->application, $isUpgrade);
			$handlertype = 'application' == $extension->type ? 'component' : $extension->type;
			$this->handler = aliroExtensionHandler::getExtensionTypeHandler($handlertype);
			if (!$this->handler) throw new installerException (sprintf(T_('cannot be installed - no handler for type %s'), $extension->type), $extension);
			if ('component' == $handlertype) $this->admin = 1;
			else $this->admin = $extension->admin;
			$extension->parmspec = aliroParameters::getParameterStringFromXMLObject($this->getXML('params'));
			$extension->store();
			$method = 'install_'.$extension->type;
			$this->putFilesInPlace($extension);
			if ($isUpgrade) {
				$queries = $this->getXML('upgrade->queries->query');
				if ($queries) $this->doQueries($queries);
			}
			else {
				$queries = $this->getXML('install->queries->query');
				if ($queries) $this->doQueries($queries);
				$jqueries = $this->getXML('install->sql->file');
				if ($jqueries) $this->doJQueries($jqueries);
			}
			if (method_exists($this, $method)) $this->$method($isUpgrade, $extension);
			$this->handler->clearCache(('component' != $handlertype));
			aliroSEF::getInstance()->clearCache();
			smartClassMapper::clearAllCaches();
			if ($isUpgrade) {
				if ($upgradefile = $this->getXML('upgradefile')) {
					$this->doPackageCode ($extension, (string) $upgradefile, (string) $upgradefile['class'], 'com_upgrade');
				}
			}
			else {
				if ($installfile = $this->getXML('installfile')) {
					$this->doPackageCode ($extension, (string) $installfile, (string) $installfile['class'], 'com_install');
				}
			}
			aliroExtensionHandler::getInstance()->tidyModulesPageControl();
			$this->handler->clearCache(('component' != $handlertype));
			aliroDatabase::getInstance()->clearCache();
			aliroRequest::getInstance()->setErrorMessage (T_('Installation completed: ').$extension->description, _ALIRO_ERROR_INFORM);
			return true;
		}
		catch (installerException $exception) {
			aliroRequest::getInstance()->setErrorMessage (sprintf($exception->getMessage(), basename($this->xmlfile), $exception->extension->formalname), _ALIRO_ERROR_FATAL);
		}
		catch (databaseException $exception) {
    		$message = sprintf(T_('Extension XML %s database error on %s at %s'), $this->xmlfile, date('Y-M-d'), date('H:i:s'));
    		$errorkey = "SQL/{$exception->getCode()}/installer/$exception->dbname/{$exception->getMessage()}/$exception->sql";
    		aliroErrorRecorder::getInstance()->recordError($message, $errorkey, $message, $exception);
			aliroRequest::getInstance()->setErrorMessage ($message, _ALIRO_ERROR_SEVERE);
		}
		catch (xmlException $exception) {
			$message = $exception->getMessage();
			aliroErrorRecorder::getInstance()->recordError($message, $message, $message, $exception);
			aliroRequest::getInstance()->setErrorMessage ($message, _ALIRO_ERROR_FATAL);
		}
		return false;
	}

	protected function extensionExists ($extension) {
		return (aliroExtensionHandler::getInstance()->getExtensionByName($extension->formalname)) ? true : false;
	}

	protected function doPackageCode ($extension, $file, $class, $retro) {
		if (false !== strpos($file, '..')) throw new installerException (sprintf(T_('file %s contains illegal ".."'), $file), $extension);
		$dofile = $this->handler->getClassPath($extension->formalname, 2).'/'.$file;
		if (is_file($dofile)) {
	    	try {
				if ($class) {
					require_once($dofile);
					if (class_exists($class, false)) new $class();
					else {
			    		$message = sprintf(T_('Extension XML %s specified install/uninstall/upgrade class %s but it was not found'), $this->xmlfile, $class);
			    		aliroErrorRecorder::getInstance()->recordError($message, $message, $message);
						aliroRequest::getInstance()->setErrorMessage ($message, _ALIRO_ERROR_SEVERE);
					}
				}
				else aliroRequest::getInstance()->invokeRetroCode($dofile, $retro);
	    	}
			catch (databaseException $exception) {
	    		$message = sprintf(T_('Extension XML %s database error on %s at %s'), $this->xmlfile, date('Y-M-d'), date('H:i:s'));
	    		$errorkey = "SQL/{$exception->getCode()}/installer/$exception->dbname/{$exception->getMessage()}/$exception->sql";
	    		aliroErrorRecorder::getInstance()->recordError($message, $errorkey, $message, $exception);
				aliroRequest::getInstance()->setErrorMessage ($message, _ALIRO_ERROR_SEVERE);
			}
		}
	}

	protected function createExtension () {
		$extension = new aliroExtension($this->package);
		$message = $extension->populateFromXML($this->xmlobject);
		if ($message) throw new installerException ($message, $extension);
		return $extension;
	}

	protected function getXML ($properties) {
		return is_null($this->xmlobject) ? null : $this->xmlobject->getXML($properties);
	}

	protected function handleClassFile ($extension, $side, $filename, $path) {
		$tokens = @token_get_all(file_get_contents($path.'/'.$filename)); //http://php.filearena.net/manual/en/function.token-get-all.php#79502
		if (!empty($tokens)) foreach ($tokens as $key=>$token) {
			if (T_CLASS == $token[0]) {
				$classname = isset($tokens[$key+2][1]) ? $tokens[$key+2][1] : '';
				$extends = (isset($tokens[$key+4][0]) AND T_EXTENDS == $tokens[$key+4][0] AND isset($tokens[$key+6][1])) ? $tokens[$key+6][1] : '';

				if (isset($classes[$classname])) {
					if ($extends != $classes[$classname]) $classes[$classname] = '';
				}
				else $classes[$classname] = $extends;
			}
		}
		if (!empty($classes)) {
			$filedir = dirname($filename);
			$filemap = ('.' == $filedir ? '' : $filedir.'/').basename($filename, '.php');
			foreach ($classes as $classname=>$extends) {
				smartClassMapper::insertClass($extension->type, $extension->formalname, $side, $filemap, $classname, $extends);
			}
		}
		unset($tokens);
	}

	protected function putFilesInPlace ($extension) {
		$xmlfilename = basename($this->xmlfile);
		if (!$this->handler->createDirectory($extension->formalname, $extension->admin)) {
			throw new installerException (sprintf(T_('unable to create user directory for %s %s'), $extension->type, $extension->formalname), $extension);
		}
		$side = ($extension->admin & 2) ? 'admin' : 'user';
		$dirpath = $this->handler->getPath($extension->formalname, $extension->admin);
		$classpath = $this->handler->getClassPath($extension->formalname, $extension->admin);
		$this->moveClassFiles($extension, $side, $this->getXML('classfiles->filename'), $classpath);
		$this->moveClassFiles($extension, $side, $this->getXML('files->filename'), $dirpath, $this->getXML('files->[folder]'));
		$this->moveFiles($extension, $this->getXML('images->filename'), $dirpath);
		$this->moveFiles($extension, $this->getXML('css->filename'), $dirpath);
		$this->moveFiles($extension, $this->getXML('media->filename'), $this->absolute_path.'/images/stories/');
		if ('component' == $extension->type OR 'application' == $extension->type) {
			if (!$this->handler->createDirectory($extension->formalname, 2)) {
				throw new installerException (sprintf(T_('unable to create user directory for %s %s'), $extension->type, $extension->formalname), $extension);
			}
			$dirpath = $this->handler->getPath($extension->formalname, 2);
			$classpath = $this->handler->getClassPath($extension->formalname, 2);
			$this->moveFiles($extension, $this->getXML('administration->files->filename'), $dirpath, $this->getXML('administration->files->[folder]'));
			$this->moveClassFiles($extension, 'admin', $this->getXML('administration->classfiles->filename'), $classpath);
			$this->moveFiles($extension, $this->getXML('administration->images->filename'), $dirpath);
			$this->moveFiles($extension, array($this->getXML('installfile')), $classpath);
			$this->moveFiles($extension, array($this->getXML('uninstallfile')), $classpath);
			$this->moveFiles($extension, array($this->getXML('upgradefile')), $classpath);
			$this->moveFiles($extension, array($xmlfilename), $this->handler->getXMLPath($extension->formalname, true));
		}
		else $this->moveFiles($extension, array($xmlfilename), $this->handler->getXMLPath($extension->formalname, ($extension->admin & 2)));
		aliroCoreDatabase::getInstance()->doSQL("UPDATE #__extensions SET xmlfile = '{$this->handler->getXMLRelativePath($extension->formalname, $extension->admin)}/{$xmlfilename}' WHERE formalname = '$extension->formalname'");
	}

	protected function moveFiles ($extension, $files, $path, $subdir='') {
		$this->moveClassFiles ($extension, '', $files, $path, $subdir);
	}

	protected function moveClassFiles ($extension, $side, $files, $path, $subdir='') {
		$fromdir = $subdir ? $this->here.$subdir.'/' : $this->here;
		if ($files) {
			$fmanager = aliroFileManager::getInstance();
			foreach ($files as $file) {
				$filename = trim((string) $file);
				if ($filename) {
					if (false !== strpos($filename, '..')) {
						throw new installerException (sprintf(T_('file %s contains illegal ".."'), $filename), $extension);
					}
					else {
						$fmanager->forceCopy($fromdir.$filename, $path.'/'.$filename, true);
						if ($side) $this->handleClassFile($extension, $side, $filename, $path);
					}
				}
			}
		}
	}
	
	protected function doQueries ($queries) {
		$database = aliroDatabase::getInstance();
		foreach ($queries as $query) if ($query = trim((string)$query)) $database->doSQL($query);
	}
	
	protected function doJQueries ($jqueries) {
		$database = aliroDatabase::getInstance();
		$subdir = $this->getXML('administration->files->[folder]').'/';
		foreach ($jqueries as $query) {
			if ($query = trim((string)$query)) {
				$realquery = @file_get_contents($this->here.$subdir.$query);
				if ($realquery) {
					$database->setQuery($realquery);
					$database->query_batch();
				}
			}
		}
	}
	
	protected function install_application ($isUpgrade, $extension) {
		$this->install_component($isUpgrade, $extension);
		$exthandler = aliroExtensionHandler::getInstance();
		$modules = $this->getXML('module');
		if ($modules) {
			foreach ($modules as $module) $mnames[] = $this->addIntegratedExtension($extension, $isUpgrade, $module, 'module');
			$exthandler->removeAllBut ('module', $mnames, $extension->application);
			aliroModuleHandler::getInstance()->clearCache();
		}
		$plugins = $this->getXML('plugin');
		if ($plugins) {
			foreach ($plugins as $plugin) {
				$formalname = $this->addIntegratedExtension($extension, $isUpgrade, $plugin, 'mambot');
				$pnames[] = $formalname;
				$this->handleURILinks($plugin->urilink, $formalname, $isUpgrade);
			}
			$exthandler->removeAllBut ('mambot', $pnames, $extension->application);
			aliroMambotHandler::getInstance()->clearCache();
		}
		$this->handleURILinks($this->getXML('urilink'), $extension->application, $isUpgrade);
	}

	private function handleURILinks ($urilinks, $appname, $isUpgrade) {
		$database = aliroCoreDatabase::getInstance();
		$application = $database->getEscaped($appname);
		if ($isUpgrade) {
			$oldlinks = $database->doSQLget("SELECT * FROM #__urilinks WHERE application = '$application'", 'aliroURILink', 'uri');
			foreach ($urilinks as $urilink) {
				$uri = (string) $urilink->uri;
				if (isset($oldlinks[$uri])) {
					$oldlinks[$uri]->class = (string) $urilink['class'];
					$oldlinks[$uri]->isdefault = ('yes' == (string) $urilink['default']) ? 1 : 0;
					$oldlinks[$uri]->notemplate = ('yes' == (string) $urilink['template']) ? 0 : 1;
					$oldlinks[$uri]->nohtml = ('yes' == (string) $urilink['html']) ? 0 : 1;
					$oldlinks[$uri]->name = (string) $urilink->name;
					$oldlinks[$uri]->description = (string) $urilink->description;
					$oldlinks[$uri]->store();
					$matched[$uri] = $database->getEscaped($uri);
				}
			}
			if (!empty($oldlinks)) {
				$sql = "DELETE FROM #__urilinks WHERE application = '$application'";
				if (isset($matched)) $sql .= " AND uri NOT IN ('".implode("','", $matched)."')";
				$database->doSQL($sql);
			}
		}
		else $database->doSQL("DELETE FROM #__urilinks WHERE application = '$application'");
		foreach ($urilinks as $urilink) {
			$uri = (string) $urilink->uri;
			if (isset($matched[$uri])) continue;
			$onelink = new aliroURILink();
			$onelink->application = $appname;
			$onelink->class = (string) $urilink['class'];
			$onelink->published = ('yes' == (string) $urilink['published']) ? 1 : 0;
			$onelink->isdefault = ('yes' == (string) $urilink['default']) ? 1 : 0;
			$onelink->notemplate = ('yes' == (string) $urilink['template']) ? 0 : 1;
			$onelink->nohtml = ('yes' == (string) $urilink['html']) ? 0 : 1;
			$onelink->name = (string) $urilink->name;
			$onelink->description = (string) $urilink->description;
			$onelink->uri = (string) $urilink->uri;
			$onelink->store();
			unset($onelink);
		}
	}
	
	private function addIntegratedExtension ($extension, $isUpgrade, $xmlobject, $type) {
		$intextension = new aliroExtension();
		$xml = new aliroXML($xmlobject);
		$intextension->populateFromXML($xml, $extension, $type);
		$intextension->parmspec = aliroParameters::getParameterStringFromXMLObject($xml->getXML('params'));
		$intextension->store();
		$method = 'install_'.$type;
		$this->$method($isUpgrade, $intextension);
		return $intextension->formalname;
	}

	protected function createComponentMenu ($item, $name, $parent, $toplevel=false) {
		$menuitem = new aliroAdminMenu();
		$menuitem->name = $this->purifier->purify((string) $item);
		$menuitem->parent = $toplevel;
		$link = "index.php?option=$name";
		if ($toplevel AND ($linkdetail = (string) $item['link'])) {
			if ('&' == $linkdetail[0]) $link .= $linkdetail;
			else $link = 'index.php?'.$linkdetail;
		}
		elseif ($toplevel AND ($task = (string) $item['task'])) $link .= '&task='.$task;
		elseif ($toplevel AND ($act = (string) $item['act'])) $link .= '&act='.$act;
		$menuitem->link = $link;
		$menuitem->type = 'components';
		$menuitem->published = 1;
		$menuitem->parent = $parent;
		$menuitem->component = $name;
        $menuitem->ordering = $toplevel ? $this->submenuordering++ : 0;
        $menuitem->store();
		return $menuitem->id;
	}

	protected function install_component ($isUpgrade, $extension) {
		$mainmenuname = $this->getXML('administration->menu');
		$component = new aliroComponent();
        $component->name = ($mainmenuname ? $this->purifier->purify((string) $mainmenuname) : $extension->name);
        $component->option = $component->extformalname = $extension->formalname;
        $component->ordering = 0;
        $component->class = $this->xmlobject->baseAttribute('userclass');
        $component->adminclass = $this->xmlobject->baseAttribute('adminclass');
        $component->menuclass = $this->xmlobject->baseAttribute('menuclass');
        $component->params = '';
        $component->store();
   		if ($mainmenuname) {
			$topid = $this->createComponentMenu ($mainmenuname, $extension->formalname, aliroAdminMenuHandler::getInstance()->baseComponentMenu());
			$submenus = $this->getXML('administration->submenu->menu');
			$this->submenuordering = 1;
			if ($submenus) foreach ($submenus as $submenu) {
				$this->createComponentMenu ($submenu, $extension->formalname, $topid, true);
			}
		}
		aliroAdminMenuHandler::getInstance()->clearCache(true);
	}
	
	public function removeApplication ($handler, $extension) {
		$this->removeComponent($handler, $extension);
		aliroCoreDatabase::getInstance()->doSQL("DELETE FROM #__urilinks WHERE application = '$extension->application'");
	}

	public function removeComponent ($handler, $extension) {
		try {
			if ($uninstallfile = $this->getXML('uninstallfile')) {
				$this->handler = $handler;
				$this->doPackageCode ($extension, (string) $uninstallfile, (string) $uninstallfile['class'], 'com_uninstall');
			}
			$queries = $this->getXML('uninstall->queries->query');
			if ($queries) foreach ($queries as $query) {
				aliroDatabase::getInstance()->doSQL((string) $query);
			}
			aliroComponentConfiguration::getInstance($extension->formalname)->delete();
			aliroAdminMenuHandler::getInstance()->clearCache(true);
		}
		catch (databaseException $exception) {

		}
	}

	public function removeMambot ($handler, $extension) {
		aliroCoreDatabase::getInstance()->doSQL("DELETE FROM #__urilinks WHERE application = '$extension->formalname'");
	}

	protected function install_module ($isUpgrade, $extension) {
		if ($isUpgrade) {
			$database = aliroCoreDatabase::getInstance();
			$database->setQuery("SELECT COUNT(*) from #__modules WHERE module = '$extension->formalname'");
			if ($database->loadResult()) {
				$database->doSQL("UPDATE #__modules SET admin = $extension->admin, class='$extension->class', adminclass = '$extension->adminclass' WHERE module = '$extension->formalname'");
				return;
			}
		}
		$module = new aliroModule();
		$module->title = $extension->name;
		$module->ordering = 99;
		$module->position = call_user_func(array(aliroTemplateHandler::getInstance()->getDefaultUserTemplateClass(), 'defaultModulePosition'));
		$module->showtitle = 1;
		$module->admin = $extension->admin;
		$module->module = $extension->formalname;
		$module->class = $extension->class;
		$module->adminclass = $extension->adminclass;
		$module->published = $extension->published;
		$module->store();
		aliroCoreDatabase::getInstance()->doSQL("INSERT INTO #__modules_menu VALUES ('$module->id', 0)");
	}

	protected function install_mambot ($isUpgrade, $extension) {
		if ($isUpgrade AND $extension->triggers) {
			$database = aliroCoreDatabase::getInstance();
			$database->setQuery("SELECT COUNT(*) from #__mambots WHERE element = '$extension->formalname'");
			if ($database->loadResult()) {
				$database->doSQL("UPDATE #__mambots SET class='$extension->class', triggers = '$extension->triggers' WHERE element = '$extension->formalname'");
				return;
			}
		}
		$mambot = new aliroMambot();
		$mambot->name = $extension->name;
		$mambot->ordering = 99;
		$mambot->element = $extension->formalname;
		$mambot->class = $extension->class;
		$mambot->published = $extension->published;
		if ($extension->triggers) {
			$mambot->triggers = $extension->triggers;
			$mambot->store();
		}
		else throw new installerException (T_('is plugin but has no triggers'), $extension);
	}

}
