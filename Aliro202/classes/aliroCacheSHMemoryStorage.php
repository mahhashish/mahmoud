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

class aliroCacheSHMemoryStorage extends aliroCacheStorage {
	private static $mops = array();

	public function __destruct () {
		foreach (self::$mops as $mop) if ($mop) shmop_close($mop);
	}

	public function getBasePath () {
		return '/cache/';
	}

	public function storeData ($id, $data, $reportSizeError=true) {
		$dir = dirname($id);
		clearstatcache();
		if (!file_exists($dir)) $this->getFileManager()->createDirectory ($dir);
		return $this->checkSize($data, $id, $reportSizeError) AND is_writeable(dirname($id)) ? @file_put_contents($id, $data, LOCK_EX) : false;
	}

	public function getData ($id, $time_limit=0) {
		return (file_exists($id) AND ($string = @file_get_contents($id))) ? $this->extractObject ($string, $time_limit) : null;
	}

	public function delete ($id) {
		// ??
	}

	public function deleteAll () {
		// ??
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