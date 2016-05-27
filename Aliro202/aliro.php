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
 * This is the initial code for all user interactions with Aliro, and is invoked 
 * from the index.php file for the user side.  It contains a minimum of code.
 *
 * The criticalInfo class is a very simple class to obtain basic directory
 * information in a way that should be resistant to hacking.  There is a slightly
 * different version of this class in the admin side index.php.
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
 * Then it loads essential classes using robust file paths, and invokes the user
 * side logic of the class aliroRequest.
 *
 * The aliro constructor starts buffering of all output so that any diagnostic
 * output (deliberate or accidental) during core processing and the running of
 * components, modules and plugins is trapped until after headers have been sent.
 * Or indefinitely for any component that wishes to send a file to the browser,
 * or similar.
 *
 */

/** Set flag that this is a parent file */
define( '_VALID_MOS', 1 );

require_once(dirname(__FILE__).'/classes/aliroBase.php');

final class criticalInfo {
	private static $instance = null;
	public $absolute_path;
	public $class_base;
	public $admin_dir;
	public $isAdmin = false;

	private function __construct() {
		$this->absolute_path = str_replace('\\', '/', dirname(__FILE__));
		define('_ALIRO_ABSOLUTE_PATH', $this->absolute_path);
		define('_CMSAPI_ABSOLUTE_PATH', $this->absolute_path);
		define('_CMSAPI_CMS_BASE', 'Aliro');
		define('_ALIRO_CURRENT_PATH', $this->absolute_path);
		if (!defined('_ALIRO_CLASS_BASE')) define ('_ALIRO_CLASS_BASE', $this->absolute_path);
        $this->class_base = _ALIRO_CLASS_BASE;
        if (!defined('_ALIRO_SITE_BASE')) {
        	$abovesite = dirname($this->absolute_path);
        	foreach (array('cache','configs','tmp') as $dirname) if (!is_dir($abovesite.$dirname)) {
        		define ('_ALIRO_SITE_BASE', $this->absolute_path);
        		break;
        	}
        	if (!defined('_ALIRO_SITE_BASE')) define ('_ALIRO_SITE_BASE', $abovesite);
        }
        define ('_ALIRO_IS_ADMIN', 0);
		define ('_ALIRO_ADMIN_PATH', '');
		// Provided only for compatibility, no actual value supplied
		$this->admin_dir = '';
		define ('_ALIRO_ADMIN_DIR', '');
		define ('_ALIRO_CLASS_MAPPER', 'smartClassMapper');
		if (!defined('_ALIRO_ADMIN_CLASS_BASE')) define ('_ALIRO_ADMIN_CLASS_BASE', '');
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}

}

final class aliro extends aliroBase {
	private static $instance = null;
	
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}
	
	public function startup ($runController=false) {
		$this->commonStartup();
		$this->classLoader = smartClassMapper::getInstance();
		$this->setAutoload();
		$config = aliroCore::getInstance();
		try {
			if ($config->getCfg('offline') AND (!aliroSession::isAdminPresent())) {
				$offline = new aliroOffline();
				$offline->show();
				exit;
			}
			$max_load = (float) $config->getCfg('max_load');
			if (function_exists('sys_getloadavg')) {
				$load = sys_getloadavg();
				if ($max_load AND $load[0] > $max_load) {
					$retry = 60.0 * (mt_rand(75, 150)/100.0);
					header ('Retry-After: '.(int)$retry);
					$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP 1.1';
					header($protocol.' 503 Too busy, try again later');
					die($protocol.' 503 Server too busy. Please try again later.');
				}
			}
		}
		catch (Exception $exception) {
			trigger_error('An uncaught database error has occurred while admin is present');
			exit;
		}
		//if ($this->installed) $database = aliroDatabase::getInstance();

		try {
			// Force creation of pseudo-Joomla environment
			$joomla = new aliroJoomla();
			$controller = aliroRequest::getInstance();
			$errorhandler = aliroErrorRecorder::getInstance();
			set_error_handler(array($errorhandler, 'PHPerror'));
			register_shutdown_function(array($errorhandler, 'PHPFatalError'));
			if ($runController) $controller->doControl();
		}
		catch (Exception $exception) {
			$config->makeOffline($exception);
		}
	}

}