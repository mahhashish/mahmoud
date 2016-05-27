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

class aliroCacheDatabaseStorage extends aliroCacheStorage {
	protected $database = null;

	public function __construct ($sizelimit, $timeout) {
		parent::__construct($sizelimit, $timeout);
		$credentials = aliroCore::getConfigData('corecredentials.php');
		$this->database = new aliroBasicDatabase ($credentials['dbhost'], $credentials['dbusername'], $credentials['dbpassword'], $credentials['dbname'], $credentials['dbprefix']);

	}

	public function getBasePath () {
		return '/cache/';
	}

	public function storeData ($id, $data, $reportSizeError=true) {
		if ($this->checkSize($data, $id, $reportSizeError)) try {
			$id = $this->database->getEscaped($id);
			$data = $this->database->getEscaped($data);
			$this->database->doSQL("INSERT INTO #__cache (id, data) VALUES ('$id', '$data') ON DUPLICATE KEY UPDATE data = '$data'");
			return true;
		}
		catch (databaseException $d) {}
		return false;
	}

	public function getData ($id, $time_limit=0) {
		$id = $this->database->getEscaped($id);
		$this->database->setQuery("SELECT data FROM #__cache WHERE id = '$id'");
		$string = $this->database->loadResult();
		return $string ? $this->extractObject ($string, $time_limit) : null;
	}

	public function delete ($id) {
		$this->database->doSQL("DELETE FROM #__cache WHERE id = '$id'");
	}

	public function deleteAll () {
		$this->database->doSQL("TRUNCATE TABLE #__cache");
	}
}