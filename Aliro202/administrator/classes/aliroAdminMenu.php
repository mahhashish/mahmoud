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
 * aliroAdminMenu is the data class for an admin side menu row
 *
 * aliroAdminMenuLink is used to construct actual menus
 *
 * aliroAdminMenuHandler is the singleton handler for all admin side menu rows
 *
 */

class aliroAdminMenu extends aliroDatabaseRow {
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__admin_menu';
	protected $rowKey = 'id';

	public function store ($updateNulls=false) {
		$ret = parent::store($updateNulls);
		aliroAdminMenuHandler::getInstance()->clearCache();
		return $ret;
	}
}

class aliroAdminMenuLink {
	public $start = false;
	public $active = false;
	public $node = false;
	public $level = 0;
	public $name = '';
	public $link = '';
}

class aliroAdminMenuHandler extends cachedSingleton  {
	protected static $instance = __CLASS__;

	protected $invisible = array();
	protected $visible = array();
	protected $byparent = array();
	protected $links = array();
	protected $componentBase = 0;

	protected function __construct () {
		// Making private enforces singleton
		$database = aliroCoreDatabase::getInstance();
		$this->invisible = $database->doSQLget("SELECT * FROM #__admin_menu WHERE published = 0", 'aliroAdminMenu', 'id');
		$this->visible = $database->doSQLget("SELECT * FROM #__admin_menu WHERE published != 0", 'aliroAdminMenu', 'id');
        foreach ($this->invisible as $key=>$result) $this->invisible[$key]->link = str_replace('&', '&amp;', str_replace('&amp;', '&', $result->link));
        foreach ($this->visible as $key=>$result) $this->visible[$key]->link = str_replace('&', '&amp;', str_replace('&amp;', '&', $result->link));
        foreach ($this->visible as $key=>$result) {
        	$this->byparent[$result->parent][] = $key;
        	if ('placeholder' == $result->type AND 'components' == $result->component) $this->componentBase = $result->id;
        }
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	public function baseComponentMenu () {
		return $this->componentBase;
	}

	public function makeMenu () {
		$numbers = $this->findActive();
        return isset($this->byparent[0]) ? $this->showLevel(0, $numbers, 0) : array();
	}
	
	private function findActive () {
		$max = $chosen = 0;
		foreach ($this->visible as $result) {
			$mcount = 0;
        	$elements = explode ('&', substr($result->link, 10));
        	foreach ($elements as $element) {
        		$parts = explode ('=', $element);
        		if (isset($_REQUEST[$parts[0]]) AND isset($parts[1]) AND $_REQUEST[$parts[0]] == $parts[1]) $mcount++;
        	}
        	if ($mcount > $max) {
        		$max = $mcount;
        		$chosen = $result->id;
        	}
		}
		while ($chosen) {
			$numbers[] = $chosen;
			$chosen = $this->visible[$chosen]->parent;
		}
		return isset($numbers) ? $numbers : array();
	}

	private function showLevel ($parent, $numbers, $level) {
        $authoriser = aliroAuthoriser::getInstance();
        $trigger = false;
		$basepath = aliroCore::getInstance()->getCfg('admin_site').'/';
        foreach ($this->byparent[$parent] as $id) {
        	if (!$authoriser->checkUserPermission('see', 'aliroMenu', $id)) continue;
        	if (isset($this->visible[$id])) $entry = $this->visible[$id];
        	else continue;
        	$menuitem = new aliroAdminMenuLink();
        	if (!$trigger) $menuitem->start = $trigger = true;
        	$menuitem->name = $entry->name;
        	if ('placeholder' == $entry->type) $menuitem->link = '';
        	elseif ('url' == $entry->type) $menuitem->link = $entry->link;
        	else $menuitem->link = $basepath.$entry->link;
        	if (in_array($entry->id, $numbers)) $menuitem->active = true;
        	$menuitem->level = $level;
        	if (isset($this->byparent[$entry->id])) {
	        	$sublevel = $this->showLevel($entry->id, $numbers, $level+1);
	        	if (!empty($sublevel)) $menuitem->node = true;
        	}
        	else $sublevel = null;
        	$results[] = $menuitem;
        	if (!empty($sublevel)) $results = array_merge($results, $sublevel);
        }
        return isset($results) ? $results : array();
	}
}