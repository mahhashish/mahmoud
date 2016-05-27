<?php

/**
 * Part of Aliro - copyright 2009-13 - http://aliro.org
 */

// fix missing DOCUMENT_ROOT in IIS
if (!isset($_SERVER['DOCUMENT_ROOT']) AND isset($_SERVER['SCRIPT_FILENAME'])) {
	$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));
}
if(!isset($_SERVER['DOCUMENT_ROOT']) AND isset($_SERVER['PATH_TRANSLATED'])) {
	$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF'])));
}

if (!function_exists('apache_request_headers')) {
        function apache_request_headers() {
            foreach ($_SERVER as $key=>$value) {
                if ('HTTP_' == substr($key,0,5)) {
                    $key = str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                    $out[$key] = $value;
                }
                                else $out[$key] = $value;
            }
            return isset($out) ? $out : array();
        }
}

abstract class aliroBase {
	protected  $timer = null;
	public $installed = false;
	
	protected function __construct () {
		ob_start();
		ob_implicit_flush(false);
		if (!defined('_ALIRO_LOCAL_PROCESSING')) define ('_ALIRO_LOCAL_PROCESSING', 0);
		// Force setting of defined symbols
		criticalInfo::getInstance();
		if (!defined('_HTTP_PROTOCOL')) define ('_HTTP_PROTOCOL', (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP 1.1'));
		spl_autoload_register(array(__CLASS__, 'simpleAutoload'));
	}
	
	public static function simpleAutoload ($classname) {
		if (is_readable(_ALIRO_CLASS_BASE.'/customclasses/'.$classname.'.php')) {
			require_once(_ALIRO_CLASS_BASE.'/customclasses/'.$classname.'.php');
			return true;
		}
		if (is_readable(_ALIRO_CLASS_BASE.'/classes/'.$classname.'.php')) {
			require_once(_ALIRO_CLASS_BASE.'/classes/'.$classname.'.php');
			return true;
		}
		if (_ALIRO_IS_ADMIN AND is_readable(_ALIRO_ADMIN_CLASS_BASE.'classes'.$classname.'.php')) {
			require_once(_ALIRO_ADMIN_CLASS_BASE.'/classes/'.$classname.'.php');
			return true;
		}
		return false;
	}
	
	public static function trace ($error=true) {
	    static $counter = 0;
		$html = '';
		foreach(debug_backtrace() as $back) {
		    if (isset($back['file']) AND $back['file']) {
			    $html .= '<br />'.$back['file'].':'.$back['line'];
			}
		}
		if ($error) $counter++;
		if (1000 < $counter) {
		    echo $html;
		    die (T_('Program killed - Probably looping'));
        }
		return $html;
	}

	/*
	 * This is used to recode all calls to file_put_contents as aliroBase::file_put_contents
	 * The aim is to gather information about what is being written by the syste
	 * for performance tuning or similar purposes.
	 * The file path for the diagnostics requires setting according to local setup.
	public static function file_put_contents ($filepath, $content, $flags=0) {
		$fpclog = fopen('/var/www/tmp/fpclog.txt', 'a');
		fwrite($fpclog, time().','.$filepath.','.strlen($content)."\n");
		fclose($fpclog);
		return @file_put_contents($filepath, $content, $flags);
	}
	*/

	public function classExists ($classname) {
		$mapper = call_user_func(array(_ALIRO_CLASS_MAPPER, 'getInstance'));
		return $mapper->classExists($classname);
	}
	
	public function firstSubClass ($classname) {
		$mapper = call_user_func(array(_ALIRO_CLASS_MAPPER, 'getInstance'));
		$subclasses = $mapper->getSubclasses($classname);
		return count($subclasses) ? $subclasses[0] : false;
	}
	
	protected function commonStartup () {

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
		
		clearstatcache();
		
		$stat = @stat(__FILE__);
		if (empty($stat) OR !is_array($stat)) $stat = array(php_uname());
		mt_srand(crc32(microtime().implode('|', $stat)));

		if (false !== strpos(@$_SERVER['REQUEST_URI'], 'mosConfig_absolute_path')) die ('Invalid Request.');

		require_once (_ALIRO_CLASS_BASE.'/bootstrap/definitions.php');

		if (!empty($_GET['oldpath'])) $this->migrateConfig();
		$filepath = _ALIRO_SITE_BASE.'/configs/'.md5(_ALIRO_ABSOLUTE_PATH.'/configuration.php').'.php';
		if (file_exists($filepath) AND filesize($filepath) > 10 ) $this->installed = true;
	
		require_once (_ALIRO_CLASS_BASE.'/bootstrap/objectcache.php');
		$this->timer = new aliroProfiler();
        // The include path is needed for HTMLpurifier (will possibly serve for other extensions too):
        set_include_path(_ALIRO_CLASS_BASE.'/extclasses/'.PATH_SEPARATOR.get_include_path());
		require_once (_ALIRO_CLASS_BASE.'/extclasses/HTMLPurifier/Bootstrap.php');
		require_once (_ALIRO_CLASS_BASE.'/bootstrap/classloader.php');
		if (_ALIRO_ADMIN_CLASS_BASE) require_once (_ALIRO_ADMIN_CLASS_BASE.'/bootstrap/classloader.php');
	}

	protected function migrateConfig () {
		$configs = array('/configuration.php', '/corecredentials.php', '/credentials.php');
		clearstatcache();
		foreach ($configs as $cfile) if (!file_exists(_ALIRO_SITE_BASE.'/configs/'.md5($_GET['oldpath'].$cfile).'.php')) return;
		foreach ($configs as $cfile) rename (_ALIRO_SITE_BASE.'/configs/'.md5($_GET['oldpath'].$cfile).'.php', _ALIRO_SITE_BASE.'/configs/'.md5(_ALIRO_ABSOLUTE_PATH.$cfile).'.php');
	}
	
	protected function setAutoload () {
		spl_autoload_unregister(array(__CLASS__, 'simpleAutoload'));
		spl_autoload_register(array('smartClassMapper', 'autoloadClass'));
        // Initiate HTML Purifier autoloading
   		// HTML Purifier needs unregister for our pre-registering functionality
   		HTMLPurifier_Bootstrap::registerAutoload();
        // Be polite and ensure that userland autoload gets retained (not permitted in Aliro
   	    // if (function_exists('__autoload')) spl_autoload_register('__autoload');
		// End of HTML Purifier related code       
	}

	public function getElapsed () {
		return $this->timer->getElapsed();
	}
	
	public function getTimeMessage () {
		return sprintf(T_('Time to generate page %s seconds'), $this->getElapsed());
	}

	public static function convertClass ($object, $toclass) {
		if (is_object($object)) {
			$quotedfrom = '"'.get_class($object).'"';
			$ser = serialize($object);
			$serfix = substr_replace($ser, "\"$toclass\"", strpos($ser, $quotedfrom), strlen($quotedfrom));
			return unserialize($serfix);
		}
		return null;
	}
}

class jaliroDebug {
	public static function trace () {
		return aliroBase::trace();
	}
}