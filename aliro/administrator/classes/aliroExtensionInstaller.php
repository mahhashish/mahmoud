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
 * aliroInstaller is the abstract base class for the following:
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
	protected $xmlobject = null;
	protected $here = '';
	protected $purifier = null;

	// This is given a real value by the install method
	protected $handler = null;

	public function __construct ($xmlfile) {
		$this->xmlfile = $xmlfile;
		$this->xmlobject = new aliroXML;
		try {
			$this->xmlobject->loadFile($xmlfile);
		} catch (aliroXMLException $exception) {
	    		aliroRequest::getInstance()->setErrorMessage ($exception->getMessage(), _ALIRO_ERROR_FATAL);
	    		$this->xmlobject = null;
		}
		if ($this->xmlobject) {
			$this->here = dirname($xmlfile).'/';
			$this->purifier = new HTMLPurifier;
		}
	}

	public function install () {
		if (!$this->xmlobject) return;
		try {
			$extension = $this->createExtension();
			$isUpgrade = $this->getParam($_POST, 'upgrade') ? true : false;
			if ($this->extensionExists($extension)) {
				if (!$isUpgrade) throw new installerException (T_('cannot be installed - already present'), $extension);
				// Must be an upgrade, and $isUpgrade must be true
				aliroExtensionHandler::getInstance()->removeExtensions($extension->formalname, $isUpgrade);
			}
			elseif ($isUpgrade) throw new installerException (T_('cannot be upgraded - not presently installed'), $extension);
			$this->handler = aliroExtensionHandler::getExtensionTypeHandler($extension->type);
			if (!$this->handler) throw new installerException (sprintf(T_('cannot be installed - no handler for type %s'), $extension->type), $extension);
			if ('component' == $extension->type) $this->admin = 1;
			else $this->admin = $extension->admin;
			$extension->store();
			$method = 'install_'.$extension->type;
			$this->putFilesInPlace($extension);
			$this->handleClasses($extension);
			if (!$isUpgrade) {
				$queries = $this->getXML('install->queries->query');
				if ($queries) $this->doQueries($queries);
				$jqueries = $this->getXML('install->sql->file');
				if ($jqueries) $this->doJQueries($jqueries);
			}
			if (method_exists($this, $method)) $this->$method($isUpgrade, $extension);
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
			$this->handler->clearCache(('component' != $extension->type));
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
					new $class();
				}
				else aliroRequest::getInstance()->invokeRetroCode($dofile, $retro);
	    	}
			catch (databaseException $exception) {
	    		$message = sprintf(T_('Extension XML %s database error on %s at %s'), $this->xmlfile, date('Y-M-d'), date('H:i:s'));
	    		$errorkey = "SQL/{$exception->getCode()}/installer/$exception->dbname/{$exception->getMessage()}/$exception->sql";
	    		aliroErrorRecorder::getInstance()->recordError($message, $errorkey, $message, $exception);
			}
		}
	}

	protected function createExtension () {
		$extension = new aliroExtension;
		$message = $extension->populateFromXML($this->xmlobject);
		if ($message) throw new installerException ($message, $extension);
		/*
		$extension->name = $this->purifier->purify((string) $this->getXML('name'));
		$extension->type = $this->xmlobject->baseAttribute('type');
		if (!in_array($extension->type, $this->legalTypes)) throw new installerException (T_('has no valid type'), $extension);
		if ('plugin' == $extension->type) $extension->type = 'mambot';
		$extension->formalname = $this->purifier->purify((string) $this->getXML('formalname'));;
		if (!$extension->formalname AND 'component' == strtolower($extension->type)) $extension->formalname = 'com_'.str_replace(' ', '', strtolower($extension->name));
		if (!$extension->formalname) throw new installerException (T_('has no formal name'), $extension);
		// validate formalname
		$extension->admin = ('administrator' == $this->xmlobject->baseAttribute('client')) ? 2 : 1;
		if ('template' == $extension->type) {
			$currentDefault = aliroTemplateHandler::getInstance()->getDefaultTemplateProperty('formalname', $extension->admin);
			if (!$currentDefault OR $currentDefault == $extension->formalname) {
				$extension->default_template = '1';
			}
		}
		$extension->author = $this->purifier->purify((string) $this->getXML('author'));
		$extension->version = $this->purifier->purify((string) $this->getXML('version'));
		$extension->date = $this->purifier->purify((string) $this->getXML('creationdate'));
		$extension->authoremail = $this->purifier->purify((string) $this->getXML('authoremail'));
		$extension->authorurl = $this->purifier->purify((string) $this->getXML('authorurl'));
		$extension->description = $this->purifier->purify((string) $this->getXML('description'));
		$extension->class = $this->xmlobject->baseAttribute('userclass');
		$extension->adminclass = $this->xmlobject->baseAttribute('adminclass');
		$extension->menuclass = $this->xmlobject->baseAttribute('menuclass');
		$extension->timestamp = date('Y-m-d');
		*/
		return $extension;
	}

	protected function getXML ($properties) {
		return is_null($this->xmlobject) ? null : $this->xmlobject->getXML($properties);
	}

	protected function handleClasses ($extension) {
		$side = $extension->admin & 2 ? 'admin' : 'user';
		$classfiles = $this->getXML('files->filename');
		$this->handleClassFiles ($classfiles, $extension, $side);
		$classfiles = $this->getXML('classfiles->filename');
		$this->handleClassFiles ($classfiles, $extension, $side, true);
		if ('component' == $extension->type) {
			$classfiles = $this->getXML('administration->files->filename');
			$this->handleClassFiles ($classfiles, $extension, 'admin');
			$classfiles = $this->getXML('administration->classfiles->filename');
			$this->handleClassFiles ($classfiles, $extension, 'admin', true);
		}
		smartAdminClassMapper::getInstance()->clearCache();
		smartClassMapper::getInstance()->clearCache();
	}

	protected function handleClassFiles ($classfiles, $extension, $side, $classRequired=false) {
		if ($classfiles) {
			$database = aliroCoreDatabase::getInstance();
			foreach ($classfiles as $classfile) {
				$classes = explode(',', (string) $classfile['classes']);
				$filename = (string) $classfile;
				$filedir = dirname($filename);
				$filemap = ('.' == $filedir ? '' : $filedir.'/').basename($filename, '.php');
				foreach ($classes as $class) {
					$class = trim($class);
					if (!$class) {
						if ($classRequired) aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Installer error: XML file %s for extension %s - filename %s has no valid classes attribute'), basename($this->xmlfile), $extension->formalname, $filename), _ALIRO_ERROR_SEVERE);
						continue;
					}
					$database->doSQL("INSERT INTO #__classmap VALUES (0, '$extension->type', '$extension->formalname', '$side', '$filemap', '$class')");
				}
			}
		}
	}

	protected function putFilesInPlace ($extension) {
		$xmlfilename = basename($this->xmlfile);
		if (!$this->handler->createDirectory($extension->formalname, $extension->admin)) {
			throw new installerException (sprintf(T_('unable to create user directory for %s %s'), $extension->type, $extension->formalname), $extension);
		}
		$side = ($extension->admin & 2) ? 'admin' : 'user';
		$dirpath = $this->handler->getPath($extension->formalname, $extension->admin);
		$classpath = $this->handler->getClassPath($extension->formalname, $extension->admin);
		$this->moveFiles($this->getXML('classfiles->filename'), $classpath);
		$this->moveFiles($this->getXML('files->filename'), $dirpath, $this->getXML('files->[folder]'));
		$this->moveFiles($this->getXML('images->filename'), $dirpath);
		$this->moveFiles($this->getXML('css->filename'), $dirpath);
		$this->moveFiles($this->getXML('media->filename'), $this->absolute_path.'/images/stories/');
		if ('component' == $extension->type) {
			if (!$this->handler->createDirectory($extension->formalname, 2)) {
				throw new installerException (sprintf(T_('unable to create user directory for %s %s'), $extension->type, $extension->formalname), $extension);
			}
			$dirpath = $this->handler->getPath($extension->formalname, 2);
			$classpath = $this->handler->getClassPath($extension->formalname, 2);
			$this->moveFiles($this->getXML('administration->files->filename'), $dirpath, $this->getXML('administration->files->[folder]'));
			$this->moveFiles($this->getXML('administration->classfiles->filename'), $classpath);
			$this->moveFiles($this->getXML('administration->images->filename'), $dirpath);
			$this->moveFiles(array($this->getXML('installfile')), $classpath);
			$this->moveFiles(array($this->getXML('uninstallfile')), $classpath);
			$this->moveFiles(array($this->getXML('upgradefile')), $classpath);
			$this->moveFiles(array($xmlfilename), $this->handler->getXMLPath($extension->formalname, 2));
		}
		$this->moveFiles(array($xmlfilename), $this->handler->getXMLPath($extension->formalname, $extension->admin));
		aliroCoreDatabase::getInstance()->doSQL("UPDATE #__extensions SET xmlfile = '{$this->handler->getXMLRelativePath($extension->formalname, $extension->admin)}/{$xmlfilename}' WHERE formalname = '$extension->formalname'");
	}

	protected function moveFiles ($files, $path, $subdir='') {
		$fromdir = $subdir ? $this->here.$subdir.'/' : $this->here;
		if ($files) {
			$fmanager = aliroFileManager::getInstance();
			foreach ($files as $file) {
				$filename = trim((string) $file);
				if ($filename) {
					if (false !== strpos($filename, '..')) {
						throw new installerException (sprintf(T_('file %s contains illegal ".."'), $filename), $extension);
					}
					else $fmanager->forceCopy($fromdir.$filename, $path.'/'.$filename, true);
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
		$mainmenu = $this->getXML('administration->menu');
		$component = new aliroComponent();
        $component->name = ($mainmenu ? $this->purifier->purify((string) $mainmenu) : $extension->name);
        $component->option = $component->extformalname = $extension->formalname;
        $component->ordering = 0;
        $component->class = $this->xmlobject->baseAttribute('userclass');
        $component->adminclass = $this->xmlobject->baseAttribute('adminclass');
        $component->menuclass = $this->xmlobject->baseAttribute('menuclass');
        $component->params = '';
        $component->store();
   		if ($mainmenu) {
			$topid = $this->createComponentMenu ($mainmenu, $extension->formalname, aliroAdminMenuHandler::getInstance()->baseComponentMenu());
			$submenus = $this->getXML('administration->submenu->menu');
			$this->submenuordering = 1;
			if ($submenus) foreach ($submenus as $submenu) {
				$this->createComponentMenu ($submenu, $extension->formalname, $topid, true);
			}
		}
		aliroAdminMenuHandler::getInstance()->clearCache(true);
	}

	public function removeComponent ($handler, $extension) {
		try {
			$classpath = $handler->getClassPath($extension->formalname, 2);
			if ($uninstallfile = $this->getXML('uninstallfile')) {
				$this->handler = $handler;
				$this->doPackageCode ($extension, (string) $uninstallfile, (string) $uninstallfile['class'], 'com_uninstall');
			}
			$queries = $this->getXML('uninstall->queries->query');
			if ($queries) foreach ($queries as $query) {
				aliroDatabase::getInstance()->doSQL((string) $query);
			}
		}
		catch (databaseException $exception) {

		}
	}

	protected function install_module ($isUpgrade, $extension) {
		$module = new aliroModule();
		$module->title = $extension->name;
		$module->ordering = 99;
		$module->position = call_user_func(array(aliroTemplateHandler::getInstance()->getDefaultUserTemplateClass(), 'defaultModulePosition'));
		$module->showtitle = 1;
		$module->admin = $extension->admin;
		$module->module = $extension->formalname;
		$module->class = $this->xmlobject->baseAttribute('userclass');
		$module->adminclass = $this->xmlobject->baseAttribute('adminclass');
		$module->published = ('yes' == $this->xmlobject->baseAttribute('published')) ? 1 : 0;
		$module->store();
		aliroCoreDatabase::getInstance()->doSQL("INSERT INTO #__modules_menu VALUES ('$module->id', 0)");
	}

	protected function install_mambot ($isUpgrade, $extension) {
		$mambot = new aliroMambot();
		$mambot->name = $extension->name;
		$mambot->ordering = 99;
		$mambot->element = $extension->formalname;
		$mambot->class = $this->xmlobject->baseAttribute('userclass');
		$mambot->published = ('yes' == $this->xmlobject->baseAttribute('published')) ? 1 : 0;
		if ($triggers = $this->xmlobject->baseAttribute('triggers')) {
			$mambot->triggers = $triggers;
			$mambot->store();
		}
		else throw new installerException (T_('is plugin but has no triggers'), $extension);
	}

}