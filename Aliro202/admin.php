<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Copyright in this code is strictly reserved by its owner, Aliro Software Limited.
 *
 * Copyright (c) 2008-12 Aliro Software Limited
 *
 * http://aliro.org
 *
 * info@aliro.org
 *
 * Aliro is open source code and is distributed under the GPL v.2 Licence
 * Aliro contains other open source code, the copyright of which is fully
 * acknowledged.  For details please see index.php and http://aliro.org/credits.
 *
 * This is the startup code for all admin interactions with Aliro, and is invoked
 * from the index.php file for the admin side.  It contains a minimum of code.
 *
 * The criticalInfo class is a very simple class to obtain basic directory
 * information in a way that should be resistant to hacking.  There is a slightly
 * different version of this class in the user side index.php and this one gives
 * more information, including the name of the administrator directory.
 *
 * __autoload is one of a tiny number of functions outside classes.  It is a special
 * PHP5 name and is invoked whenever there is a reference to an unknown class.
 * The smart class mapper is used to try to locate the class, in which case it is
 * loaded.  There are very few uses of "require" or "include" in the core of Aliro
 * and this is one of the few.  It is important that they be resistant to hacker
 * attempts to load external code.
 *
 * The startup function exists so that the amount of code executing in a global
 * context is minimal.  It checks for attempts to inject values into global data.
 * Then it loads essential classes using robust file paths, and invokes the admin
 * side logic of the class aliroRequest.
 *
 * The code initially executed simply buffers all output so that any diagnostic
 * output (deliberate or accidental) during core processing and the running of
 * components, modules and mambots is trapped until after headers have been sent.
 * Or indefinitely for any component that wishes to send a file to the browser,
 * or similar.
 *
 */

if (basename(@$_SERVER['REQUEST_URI']) == basename(__FILE__)) die ('This software is for use within a larger system');

/** Set flag that this is a parent file */
define( '_VALID_MOS', 1 );

require_once(dirname(__FILE__).'/classes/aliroBase.php');

class criticalInfo {
	private static $instance = __CLASS__;
	public $absolute_path;
	public $admin_absolute_path;
	public $admin_dir;
	public $class_base;
	public $isAdmin = true;

	private function __construct($adminpath) {
		if (!$adminpath) return;
		$this->admin_absolute_path = $adminpath; // str_replace('\\', '/', dirname(__FILE__));
		define ('_ALIRO_ADMIN_PATH', $this->admin_absolute_path);
		define ('_ALIRO_CURRENT_PATH', $this->admin_absolute_path);
		$this->absolute_path = dirname($this->admin_absolute_path);
		define('_ALIRO_ABSOLUTE_PATH', $this->absolute_path);
		define('_CMSAPI_ABSOLUTE_PATH', $this->absolute_path);
		define('_CMSAPI_CMS_BASE', 'Aliro');
        if (!defined('_ALIRO_SITE_BASE')) {
        	$abovesite = dirname($this->absolute_path);
        	foreach (array('cache','configs','tmp') as $dirname) if (!is_dir($abovesite.$dirname)) {
        		define ('_ALIRO_SITE_BASE', $this->absolute_path);
        		break;
        	}
        	if (!defined('_ALIRO_SITE_BASE')) define ('_ALIRO_SITE_BASE', $abovesite);
        }
        $this->admin_dir = substr($this->admin_absolute_path, strlen($this->absolute_path));
		define ('_ALIRO_ADMIN_DIR', $this->admin_dir);
		if (!defined('_ALIRO_CLASS_BASE')) define ('_ALIRO_CLASS_BASE', $this->absolute_path);
		if (!defined('_ALIRO_ADMIN_CLASS_BASE')) define ('_ALIRO_ADMIN_CLASS_BASE', _ALIRO_CLASS_BASE._ALIRO_ADMIN_DIR);
        $this->class_base = _ALIRO_CLASS_BASE;
        define ('_ALIRO_IS_ADMIN', 1);
		define ('_ALIRO_CLASS_MAPPER', 'smartAdminClassMapper');
	}

	public static function getInstance ($adminpath='') {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self($adminpath));
	}

}

class aliro extends aliroBase {
	private static $instance = null;

	public static function getInstance ($adminpath='') {
		if ($adminpath AND !defined('ALIRO_ADMIN_PATH')) criticalInfo::getInstance($adminpath);
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}

	public function startup ($runController=false) {
		$this->commonStartup();
		smartAdminClassMapper::getInstance();
		$this->setAutoload();
		if (!$this->installed) {
            $newinstall = aliroInstallerFactory::getInstaller();
            $newinstall->install();
            exit();
		}
		try {
			$controller = aliroRequest::getInstance();
			$errorhandler = aliroErrorRecorder::getInstance();
			set_error_handler(array($errorhandler, 'PHPerror'));
			register_shutdown_function(array($errorhandler, 'PHPFatalError'));
			new aliroJoomla();
			if ($runController) $controller->doControl();
		}
		catch (Exception $exception) {
			// Most errors should be captured and handled more fully - this is just the fallback
			echo $exception->getMessage();
			if (isset($exception->trace)) echo '<br />'.$exception->trace;
			if (isset($exception->sql)) echo '<br />SQL: '.$exception->sql;
			if (isset($exception->dbtrace)) echo '<br />'.$exception->dbtrace;
			trigger_error('Uncaught Exception');
		}
	}
}
