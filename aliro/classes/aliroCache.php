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
 * aliroCache emulates the PEAR light cache, but is much less code
 *
 */

class aliroSimpleCache extends aliroBasicCache {
	protected $group = '';
	protected $idencoded = '';
	protected $caching = true;
	protected $timeout = _ALIRO_HTML_CACHE_TIME_LIMIT;
	protected $sizelimit = _ALIRO_HTML_CACHE_SIZE_LIMIT;
	protected $livesite = '';
	
	public function __construct ($group) {
		if ($group) $this->group = $group;
        else trigger_error ('Cannot create cache without specifying group name');
		parent::__construct();
	}

	protected function getGroupPath () {
		$grouppath = $this->basepath."html/$this->group/";
		if (!file_exists($grouppath)) aliroFileManager::getInstance()->createDirectory ($grouppath);
		return $grouppath;
	}

	protected function getCachePath ($name) {
		return $this->getGroupPath().$name;
	}

	public function clean () {
		$path = $this->getGroupPath();
		$dir = new aliroDirectory($path);
		$dir->deleteFiles();
	}

	public function get ($id) {
		$this->idencoded = $this->encodeID($id);
		return $this->retrieve ($this->idencoded);
	}

	public function save ($data, $id=null) {
		if ($id) $this->idencoded = $this->encodeID($id);
		return $this->store ($data, $this->idencoded);
	}
	
	protected function encodeID ($id) {
		return md5(serialize($id).$this->livesite);
	}
}

class aliroCache extends aliroSimpleCache {

	public function __construct ($group) {
		$this->caching = aliroCore::getInstance()->getCfg('caching') ? true : false;
		$this->livesite = aliroCore::getInstance()->getCfg('live_site');
		parent::__construct($group);
	}

	public function call () {
		$arguments = func_get_args();
		$cached = $this->caching ? $this->get($arguments) : null;
		if (!$cached) {
			ob_start();
			ob_implicit_flush(false);
			$function = array_shift($arguments);
			call_user_func_array($function, $arguments);
			$html = ob_get_contents();
			ob_end_clean();
			$cached = new stdClass();
			$cached->html = $html;
			if ($this->caching) {
			    aliroRequest::getInstance()->setMetadataInCache($cached);
				$cached->pathway = aliroPathway::getInstance()->getPathway();
				$this->save($cached);
			}
		}
		else {
		    aliroRequest::getInstance()->setMetadataFromCache($cached);
		    aliroPathway::getInstance()->setPathway($cached->pathway);
		}
		echo $cached->html;
	}

}

/**
* Class to support function caching
* with backwards compatibility for Mambo and Joomla
*/
class mosCache {
    /**
	* @return object A function cache object
	*/
    static function getCache ($group) {
        return new aliroCache ($group);
    }
    /**
	* Cleans the cache
	*/
    public static function cleanCache ($group=false) {
        if (aliroCore::get('mosConfig_caching')) {
            $cache = mosCache::getCache( $group );
            $cache->clean ($group);
        }
    }
}
