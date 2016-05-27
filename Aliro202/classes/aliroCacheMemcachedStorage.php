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

class aliroCacheMemcachedStorage extends aliroCacheStorage {
	protected static $memcache = null;
	protected static $basepath = '';

	public function __construct ($sizelimit, $timeout) {
		if (!self::$memcache) {
			if (!class_exists('Memcache')) trigger_error('Class Memcache not found');
			self::$memcache = new Memcache();
			$config = aliroCore::getInstance();
			$server = $config->getCfg('cache_server');
			if (!self::$memcache->addServer($server, 11211)) trigger_error('Memcache could not connect');
			self::$basepath = $config->getCfg('live_site').'/';
		}
		parent::__construct($sizelimit, $timeout);
	}

	public function getBasePath () {
		return self::$basepath;
	}

	public function storeData ($id, $data, $reportSizeError=true) {
		return $this->checkSize($data, $id, $reportSizeError) AND self::$memcache->set($id, $data, false, $this->timeout);
	}

	public function getData ($id, $time_limit=0) {
		if ($string = self::$memcache->get($id)) return $this->extractObject ($string, $time_limit);
		return null;
	}

	public function delete ($id) {
		self::$memcache->delete($id);
	}

	public function deleteAll () {
		self::$memcache->flush();
	}
}
