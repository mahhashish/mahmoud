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
 * This is the starting point for all user interactions with Aliro, the index.php
 * file for the user side.  It contains a minimum of code.
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
 * The code initially executed simply buffers all output so that any diagnostic
 * output (deliberate or accidental) during core processing and the running of
 * components, modules and mambots is trapped until after headers have been sent.
 * Or indefinitely for any component that wishes to send a file to the browser,
 * or similar.
 *
 */

/** Set flag that this is a parent file */
define( '_VALID_MOS', 1 );

class criticalInfo {
	private static $instance = __CLASS__;
	public $absolute_path;
	public $class_base;
	public $isAdmin = false;

	private function __construct() {
		$this->absolute_path = str_replace('\\', '/', dirname(__FILE__));
		define('_ALIRO_ABSOLUTE_PATH', $this->absolute_path);
		define('_ALIRO_CURRENT_PATH', $this->absolute_path);
		if (!defined('_ALIRO_CLASS_BASE')) define ('_ALIRO_CLASS_BASE', $this->absolute_path);
        $this->class_base = _ALIRO_CLASS_BASE;
        define ('_ALIRO_IS_ADMIN', 0);
		define ('_ALIRO_ADMIN_PATH', '');
		define ('_ALIRO_ADMIN_DIR', '');
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
	}

}

class aliro {
	private static $instance = __CLASS__;
	private $timer = null;
	public $installed = false;
	
	public static function getInstance () {
	    if (!is_object(self::$instance)) {
			self::$instance = new self::$instance();
			$critical = criticalInfo::getInstance();
		}
	    return self::$instance;
	}
	
	public function classExists ($classname) {
		return smartClassMapper::getInstance()->classExists($classname);
	}
	
	public function requireClass ($classname) {
		smartClassMapper::getInstance()->requireClass($classname);
	}

	public function startup () {

		$protects = array('_REQUEST', '_GET', '_POST', '_COOKIE', '_FILES', '_SERVER', '_ENV', 'GLOBALS', '_SESSION');

		foreach ($protects as $protect) {
			if ( in_array($protect , array_keys($_REQUEST)) ||
			in_array($protect , array_keys($_GET)) ||
			in_array($protect , array_keys($_POST)) ||
			in_array($protect , array_keys($_COOKIE)) ||
			in_array($protect , array_keys($_FILES))) {
				die('Invalid Request.');
			}
		}
		if (false !== strpos($_SERVER['REQUEST_URI'], 'mosConfig_absolute_path')) die ('Invalid Request.');

		require_once (dirname(__FILE__).'/definitions.php');

		$filepath = _ALIRO_CLASS_BASE.'/configs/'.md5(_ALIRO_ABSOLUTE_PATH.'/configuration.php').'.php';
		if (file_exists($filepath) AND filesize($filepath) > 10 ) $this->installed = true;
	
		require_once (_ALIRO_CLASS_BASE.'/objectcache.php');
		$this->timer = new aliroProfiler();
        // The include path is needed for HTMLpurifier (will possibly serve for other extensions too):
        set_include_path(_ALIRO_CLASS_BASE.'/extclasses/'.PATH_SEPARATOR.get_include_path());
		require_once (_ALIRO_CLASS_BASE.'/extclasses/HTMLPurifier/Bootstrap.php');
		require_once (_ALIRO_CLASS_BASE.'/classloader.php');
		smartClassMapper::getInstance();
		$this->classLoader = smartClassMapper::getInstance();

		$controller = aliroRequest::getInstance();

		$errorhandler = aliroErrorRecorder::getInstance();
		set_error_handler(array($errorhandler, 'PHPerror'));
		$controller->doControl();
	}

	public function getElapsed () {
		return $this->timer->getElapsed();
	}
	
	public function getTimeMessage () {
		return sprintf(T_('Time to generate page %s seconds'), $this->getElapsed());
	}

}

ob_start();
ob_implicit_flush(0);
aliro::getInstance()->startup();