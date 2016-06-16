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
 * Everything here is to do with database management.
 *
 * aliroInstallXML is the class that handles the XML that defines the packaginf
 * of any extension.  It is an extension of aliroCommonInstallXML - the base
 * class for both install and uninstall XML handling.
 *
 * aliroInstaller does the real work of installing extensions
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

class aliroAdminMenuHandler extends cachedSingleton  {
	protected static $instance = __CLASS__;

	private $all_entries = array();
	private $visible = array();
	private $byname = array();
	private $basepath = '';
	private $links = null;
	private $componentBase = 0;

	protected function __construct () {
		// Making private enforces singleton
		$this->basepath = aliroRequest::getInstance()->getCfg('admin_site').'/';
		$database = aliroCoreDatabase::getInstance();
		$this->all_entries = $database->doSQLget("SELECT * FROM #__admin_menu", 'aliroAdminMenu');
        foreach ($this->all_entries as $key=>&$result) {
        	$result->link = str_replace('&', '&amp;', str_replace('&amp;', '&', $result->link));
        	$this->byname[$result->component] = $key;
        	if ('placeholder' == $result->type AND 'components' == $result->component) $this->componentBase = $result->id;
        	if (!$result->published) continue;
        	$mcount = 0;
        	$this->visible[$result->id] = $result;
        }
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	public function getByName ($name) {
		if (isset($this->byname[$name])) return $this->all_entries[$this->byname[$name]];
		else return null;
	}

	public function baseComponentMenu () {
		return $this->componentBase;
	}

	public function createChild ($entry) {
		$item = new aliroAdminMenu();
		$item->name = $entry->name;
		$item->link = $entry->link;
		$item->type = $entry->type;
		$item->parent = $entry->id;
		$item->component = $entry->component;
		$item->sublevel = $entry->sublevel + 1;
		$item->xmlfile = $entry->xmlfile;
		return $item;
	}

	public function makeMenu () {
		$max = $chosen = 0;
		foreach ($this->visible as $result) {
			$mcount = 0;
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
			$chosen = $this->visible[$chosen]->parent;
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
        foreach ($this->visible as $entry) {
        	if ($entry->parent != $parent) continue;
        	if (!$authoriser->checkUserPermission('see', 'aliroMenu', $entry->id)) continue;
        	if (!$trigger) {
        		$this->links[] = $liststart;
        		$trigger = true;
        	}
        	if ('placeholder' == $entry->type) $this->links[] = "\n\t<li>$entry->name";
        	elseif ('url' == $entry->type) $this->links[] = "\n\t<li><a href='{$entry->link}'>$entry->name</a>";
        	else $this->links[] = "\n\t<li><a href='{$this->basepath}{$entry->link}'>$entry->name</a>";
        	$this->showLevel($entry->id, $numbers, "\n<ul>");
        	$this->links[] = "</li>";
        }
        if ($trigger) $this->links[] = "\n</ul>";
	}

}