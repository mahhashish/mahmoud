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
 * These classes provide cache logic, and for no special reason, the profiling
 * logic that supports the timing of operations, aliroProfiler.
 *
 * cachedSingleton is the base for building a singleton class whose internal
 * data is cached.  It is used extensively within the core, especially for the
 * handlers that look after information about the main building blocks of the
 * CMS, such as menus, components, modules, etc.  It provides common code for
 * them so that it becomes simple to create a cached singleton object.
 *
 * aliroBasicCache is the class containing rudimentary cache operations.  It
 * was initially independently developed, and subsequently modified to contain
 * the features of CacheLite.  Except that a number of decisions have been
 * taken as well as the code being exclusively PHP5 - these factors make the
 * code a lot simpler.
 *
 * aliroSingletonObjectCache does any special operations needed in the handling
 * of cached singletons, extending aliroBasicCache.
 *
 * Further cache related code that emulates the services of CacheLite (more or
 * less) is found elsewhere (in aliroCentral.php at the time of writing).
 *
 */

class aliroProfiler {
    private $start=0;
    private $prefix='';

    function __construct ( $prefix='' ) {
        $this->start = $this->getmicrotime();
        $this->prefix = $prefix;
    }

	public function reset () {
		$this->start = $this->getmicrotime();
	}

    public function mark( $label ) {
        return sprintf ( "\n$this->prefix %.3f $label", $this->getmicrotime() - $this->start );
    }

    public function getElapsed () {
    	return $this->getmicrotime() - $this->start;
    }

    private function getmicrotime(){
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }
}

abstract class cachedSingleton {

	protected function __clone () { /* Enforce singleton */ }

	protected static function getCachedSingleton ($class) {
		$objectcache = aliroSingletonObjectCache::getInstance();
		$object = $objectcache->retrieve($class);
		if ($object == null OR !($object instanceof $class)) {
			$object = new $class();
			$objectcache->store($object);
		}
		return $object;
	}

	public function clearCache ($immediate=false) {
		$objectcache = aliroSingletonObjectCache::getInstance();
		$classname = get_class($this);
		$objectcache->delete($classname);
		if ($immediate) {
			$instancevar = $classname.'::$instance';
			eval("$instancevar = '$classname';");
		}
	}
	
	public function cacheNow () {
		aliroSingletonObjectCache::getInstance()->store($this);
	}

}

abstract class aliroBasicCache {
	private static $mops = array();
	protected $basepath = '';
	protected $sizelimit = 0;
	protected $timeout = 0;

	public function __construct () {
		$this->basepath = _ALIRO_CLASS_BASE.'/cache/';
	}
	
	public function __destruct () {
		foreach (self::$mops as $mop) if ($mop) shmop_close($mop);
	}

	abstract protected function getCachePath ($name);

	public function store ($object, $cachename='') {
		$path = $this->getCachePath($cachename ? $cachename : get_class($object));
		if (is_object($object)) $object->aliroCacheTimer = time();
		else {
			$givendata = $object;
			$object = new stdClass();
			$object->aliroCacheData = $givendata;
			$object->aliroCacheTimer = -time();
		}
		$s = serialize($object);
		$s .= md5($s);
		if (strlen($s) > $this->sizelimit) return false;
		$result = is_writeable(dirname($path)) ? @file_put_contents($path, $s, LOCK_EX) : false;
		if (!$result) @unlink($path);
		return $result;
	}

	public function retrieve ($class, $time_limit = 0) {
		// $timer = class_exists('aliroProfiler') ? new aliroProfiler() : null;
		$result = null;
		$path = $this->getCachePath($class);
		if (file_exists($path) AND ($string = @file_get_contents($path))) {
			$s = substr($string, 0, -32);
			$object = ($s AND (md5($s) == substr($string, -32))) ? unserialize($s) : null;
			if (is_object($object)) {
				$time_limit = $time_limit ? $time_limit : $this->timeout;
				$stamp = @$object->aliroCacheTimer;
				if ((time() - abs($stamp)) <= $time_limit) $result = $stamp > 0 ? $object : @$object->aliroCacheData;
			}
			// if ($object AND $timer) echo "<br />Loaded $class in ".$timer->getElapsed().' secs';
		}
		return $result;
	}

	// Worked but slightly slower than using file system
	/*
	private function memStore ($string, $name) {
		$size = strlen($name);
		if ($mop = $this->memGetToken($name, $size+8)) {
			return shmop_write($mop, str_pad((string) $size, 8, '0', STR_PAD_LEFT).$string, $size+8);
		}
		else return false;
	}

	private function memRetrieve ($name) {
		if ($mop = $this->memGetToken($name)) {
			return shmop_read($mop, 8, intval(shmop_read($mop, 0, 8)));
		}
		return null;
	}
	
	private function memGetToken ($name, $minsize=0) {
		if (function_exists('ftok') AND function_exists('shmop_open')) {
			$id = ftok($name, 'R');
			$mop = isset(self::$mops[$id]) ? self::$mops[$id] : (self::$mops[$id] = @shmop_open($id, 'w', 0600, 0));
			if ($mop) {
				if ($minsize <= shmop_size($mop)) return $mop;
				shmop_delete($mop);
			}
			return @shmop_open($id, 'c', 0600, $minsize+128);
		}
		return false;
	}
	*/
}

class aliroSingletonObjectCache extends aliroBasicCache {
	protected static $instance = __CLASS__;
	protected $timeout = _ALIRO_OBJECT_CACHE_TIME_LIMIT;
	protected $sizelimit = _ALIRO_OBJECT_CACHE_SIZE_LIMIT;

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
	}

	protected function getCachePath ($name) {
		return $this->basepath.'singleton/'.$name;
	}

	public function delete () {
		$classes = func_get_args();
		foreach ($classes as $class) {
			$cachepath = $this->getCachePath($class);
			if (file_exists($cachepath)) unlink($cachepath);
		}
	}

	public function deleteByExtension ($type) {
		$caches = array (
		'component' => 'aliroComponentHandler',
		'module' => 'aliroModuleHandler',
		'mambot' => 'aliroMambotHandler'
		);
		if (isset($caches[$type])) $this->delete($caches[$type]);
	}

}