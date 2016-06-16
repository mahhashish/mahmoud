<?php
/**
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class aliroFullAdminMenu {
	private $entries = array();
	private $base = '';
	private $links = null;
	
	public function __construct () {
	}
	
	/**
	* Show the menu
	*/
	public function show() {
		$request = mos;
		$this->base = aliroRequest::getInstance()->getCfg('admin_site').'/';
		$cache = aliroSingletonObjectCache::getInstance();
		if ($cachedata = $cache->retrieve('aliroFullAdminMenu')) $results = $cachedata->results;
		else {		
			$coredatabase = aliroCoreDatabase::getInstance();
        	$coredatabase->setQuery("SELECT * FROM #__admin_menu WHERE published = 1");
        	$results = $coredatabase->loadObjectList();
        	$cachedata = new stdClass();
        	$cachedata->results = $results;
        	$cache->store($cachedata, 'aliroFullAdminMenu');
		}
		$max = $chosen = 0;
        if ($results) foreach ($results as $result) {
        	$mcount = 0;
        	$this->entries[$result->id] = $result;
        	$query = substr($result->link, 10);
        	$elements = explode ('&', $query);
        	foreach ($elements as $element) {
        		$parts = explode ('=', $element);
        		if (isset($_REQUEST[$parts[0]]) AND isset($parts[1]) AND $_REQUEST[$parts[0]] == $parts[1]) $mcount++;
        	}
        	if ($mcount > $max) {
        		$max = $mcount;
        		$chosen = $result->id;
        	}
        }
        $numbers = array();
		while ($chosen) {
			$numbers[] = $chosen;
			$chosen = $this->entries[$chosen]->parent;
		}
        $this->showLevel(0, $numbers, "\n<ul id='nav'>");
        $html = '';
        if ($this->links) {
        	foreach ($this->links as $link) $html .= $link;
        }
        return $html;
	}
	
	private function showLevel ($parent, $numbers, $liststart) {
        $authoriser = aliroAuthoriser::getInstance();
        $trigger = false;
        foreach ($this->entries as $entry) {
        	if ($entry->parent != $parent) continue;
        	if (!$authoriser->checkUserPermission('see', 'aliroMenu', $entry->id)) continue;
        	if (!$trigger) {
        		$this->links[] = $liststart;
        		$trigger = true;
        	}
        	if ('placeholder' == $entry->type) $this->links[] = "\n\t<li>$entry->name";
        	elseif ('url' == $entry->type) $this->links[] = "\n\t<li><a href='{$entry->link}'>$entry->name</a>";
        	else $this->links[] = "\n\t<li><a href='{$this->base}{$entry->link}'>$entry->name</a>";
        	$this->showLevel($entry->id, $numbers, "\n<ul>");
        	$this->links[] = "</li>";
        }
        if ($trigger) $this->links[] = "\n</ul>";
	}
	
	public static function clearCache () {
		$cache = aliroSingletonObjectCache::getInstance();
		$cache->delete('aliroFullAdminMenu');
	}

}

?>