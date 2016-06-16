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
 * Basic menu classes are here.  aliroAdminMenu is the simple class that provides
 * objects corresponding to rows in the admin menu table.
 *
 * aliroMenu likewise corresponds to rows in the user menu table.  It has a few
 * extra methods.
 *
 * aliroMenuLink is the simple data class that is used to communicate between the
 * aliroMenuCreator and menu modules.  This allows all the menu logic to remain in
 * the Aliro core, while all the presentation is handled by installable modules.
 *
 * aliroMenuHandler does most of the work for manipulating menu data.  It is a
 * cached singleton, so it only loads its data from the database periodically in
 * normal operation.
 *
 */

class aliroMenuItem extends aliroDatabaseRow {
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__menu';
	protected $rowKey = 'id';

	public function load( $oid=null ) {
		trigger_error ('Should not ->load() a menu - aliroMenuHandler can provide the whole menu using getMenuByID($id)');
		echo aliroRequest::trace();
    }

    public function getParams () {
    	if ($this->params) {
    		$params = new aliroParameters($this->params);
    	}
    	else {
    		$info = criticalInfo::getInstance();
    		$xmlfile = $info->absolute_path.$this->xmlfile;
    		$xmldata = file_get_contents($xmlfile);
    		if (false == $xmldata) {
    			trigger_error (T_('aliroMenuItem class could not find XML file ').$xmlfile);
    			return '';
    		}
    		$xmlparser = new aliroXMLParamsDefault;
    		$params = $xmlparser->paramsFromString($xmldata);
    	}
    	return $params;
    }

    public function linkComponentData ($component) {
    	$this->name = $component->name;
    	$this->link = 'index.php?option='.$component->option;
    	$this->type = 'component';
    	$this->componentid = $component->id;
    	$this->component = $component->option;
    	$this->xmlfile = $component->xmlfile;
    }

}

// Provided only for compatibility
class mosMenu extends aliroMenuItem {

}

class aliroMenuLink {
	public $id = 0;
	public $name = '';
	public $link = '';
	public $image = '';
	public $opener = '';
	public $image_last = 0;
	public $level = 0;
	public $active = false;
	public $subactive = false;
}

/**
* Menu handler
*/
class aliroMenuHandler extends cachedSingleton {
    private $menus = null;
    private $counts = null;
    private $byParentOrder = null;
    private $main_home = null;

    protected static $instance = __CLASS__;

    /**
	* Constructor - protected to enforce singleton
	*/
    protected function __construct () {
        $sql = "SELECT * FROM #__menu ORDER BY ordering";
        $this->menus = aliroCoreDatabase::getInstance()->doSQLget($sql, 'aliroMenuItem', 'id');
        foreach ($this->menus as $key=>$menu) {
        	if (is_null($this->main_home) AND 'mainmenu' == $menu->menutype AND $menu->published AND 0 == $menu->parent) $this->main_home = $menu;
             // Ensure that published is always 0 or 1
            $this->menus[$key]->published = $menu->published ? 1 : 0;
            $this->byParentOrder[$menu->menutype][$menu->parent][] = $key;
            if (isset($this->counts[$menu->menutype][$menu->published])) $this->counts[$menu->menutype][$menu->published]++;
            else $this->counts[$menu->menutype][$menu->published] = 1;
        }
    }

    /**
	* Singleton accessor with cache
	*/
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

    public function getMenuByID ($id) {
    	return isset($this->menus[$id]) ? $this->menus[$id] : null;
    }

    public function getMenuCount ($type, $published=1) {
        return isset($this->counts[$type][$published]) ? $this->counts[$type][$published] : 0;
    }

    public function getCountByTypeComponentID ($type, $sections) {
		$count = 0;
		foreach ($this->menus as $menu) {
			if ($menu->menutype == $type AND in_array($menu->componentid, $sections) AND 1 == $menu->published) $count++;
		}
		return $count;
    }

    public function getMenutypes () {
    	$types = array();
    	foreach ($this->menus as $menu) $types[$menu->menutype] = 1;
    	ksort($types);
    	if (0 == count($types)) return array('mainmenu');
    	return array_keys($types);
    }

    public function getIDByMenutypeQuery ($type, $query) {
    	$query = str_replace('&amp;', '&', $query);
        foreach ($this->menus as $menu) {
            if ($menu->published == 1 AND ($type == '*' OR $menu->menutype == $type) AND $menu->link == 'index.php?'.$query) return $menu->id;
        }
        return null;
    }

    public function getAllMenusByMenutype ($type) {
    	$menus = array();
    	foreach ($this->menus as $menu) if ($menu->menutype == $type) $menus[] = $menu;
    	return $menus;
    }

    public function getAllMenusByType ($type) {
    	$menus = array();
    	foreach ($this->menus as $menu) if ($menu->type == $type) $menus[] = $menu;
    	return $menus;
    }

    public function getMenusByIDTypes ($componentid, $types) {
    	$menus = array();
    	foreach ($this->menus as $menu) {
    		if ($componentid != $menu->componentid) continue;
    		foreach ($types as $type) if (false !== strpos($menu->link, $type)) {
    			$menus = $menu;
    			continue;
    		}
    	}
    	return $menus;
    }

    private function getIDLikeQuery ($query_items, $published=false) {
    	$min = 999;
    	$result = 0;
        foreach ($this->menus as $menu) {
        	if (substr($menu->link,0,10) != 'index.php?' OR ($published AND !$menu->published)) continue;
        	$link = str_replace('&amp;', '&', substr($menu->link,10));
        	$link_items = explode('&', $link);
        	$diff = count(array_diff($link_items, $query_items));
        	if ($diff < $min) {
        		$min = $diff;
        		$result = $menu->id;
        	}
        	elseif ($diff == $min AND $menu->menutype == 'mainmenu') $result = $menu->id;
        }
        if ($min AND isset($_SESSION['aliro_Itemid'])) $result = $_SESSION['aliro_Itemid'];
        return $result;
    }

    public function matchURL ($published=true) {
    	if (!isset($_REQUEST['option'])) {
    		$this->setHome();
    		$result = $this->getHome();
    	}
    	else {
	    	if ($_SERVER['QUERY_STRING']) $query_items = explode('&', $_SERVER['QUERY_STRING']);
		   	else $query_items = array();
   			foreach ($_POST as $name=>$value) $query_items[] = $name.'='.$value;
	    	$link = $this->getIDLikeQuery($query_items, $published);
    		if ($link) $result = $this->menus[$link];
    		else $result = null;
    	}
        if ($result) {
            $optionstring = 'option='.aliroRequest::getInstance()->getOption();
            if (false === strpos($result->link, $optionstring)) return null;
            $_SESSION['aliro_Itemid'] = $result->id;
        }
        return $result;
   }

    public function getIDByTypeCid ($type, $componentid, $unpublished=false) {
        foreach ($this->menus as $menu) {
            if (($unpublished OR $menu->published == 1) AND ('*' == $type OR $menu->type == $type) AND $menu->componentid == $componentid) return $menu->id;
        }
        return null;
    }

    public function getGlobalBlogSectionCount () {
        $count = 0;
        foreach ($this->menus as $menu) {
            if ($menu->type == 'content_blog_section' AND $menu->published == 1 AND $menu->componentid == 0) $count++;
        }
        return $count;
    }

    private function addMenus ($menutype, &$result, $menukeys, $published, $level) {
        $authoriser = aliroAuthoriser::getInstance();
    	foreach ($menukeys as $key) {
    		$menu = $this->menus[$key];
    		$menu->level = $level;
    		if ($published AND !$menu->published) continue;
            if (!$authoriser->checkUserPermission ('view', 'mosMenuEntry', $menu->id)) continue;
            $result[] = $menu;
    		if (isset($this->byParentOrder[$menutype][$menu->id])) $this->addMenus($menutype, $result, $this->byParentOrder[$menutype][$menu->id], $published, $level+1);
    	}
    }

    public function &getByParentOrder ($menutype, $published=1) {
        $result = array();
        if (isset($this->byParentOrder[$menutype][0])) {
        	$this->addMenus($menutype, $result, $this->byParentOrder[$menutype][0], $published, 0);
        }
        return $result;
    }

    public function getHome () {
    	return $this->main_home;
    }

    public function setHome () {
    	if ($this->main_home) {
	        $requests = explode ('&', substr($this->main_home->link, 10));
    	    foreach ($requests as $request) {
        	    $parts = explode ('=', $request);
            	if (count($parts == 2)) $_REQUEST[$parts[0]] = $_POST[$parts[0]] = $parts[1];
        	}
    	}
        return isset($_REQUEST['option']) ? $_REQUEST['option'] : null;
    }

    public function updateNames ($oldname, $newname, $type) {
    	$database = aliroCoreDatabase::getInstance();
    	$database->doSQL("UPDATE #__menu SET name='$newname' WHERE name='$oldname' AND type='$type'");
    	$this->clearCache();
    }

    public function publishMenus ($ids, $new_publish, $type=null) {
		foreach ($ids as &$id) $id = intval($id);
		$new_publish = intval($new_publish);
		$idlist = implode (',', $ids);
		$database = aliroCoreDatabase::getInstance();
		$sql = "UPDATE #__menu SET published = $new_publish WHERE id IN ($idlist)";
		if ($type) $sql .= " AND type='$type'";
		$database->doSQL ($sql);
		$this->clearCache();
    }

    public function changeOrder ($id, $direction, $menutype) {
		$menu = $this->getMenuByID($id);
		$movement = 'down' == $direction ? 15 : -15;
		$this->updateOrdering (array($id => $menu->ordering + $movement), $menutype);
    }

	public function updateOrdering ($orders, $menutype) {
		foreach ($orders as $id=>$order) {
			$menu =  $this->getMenuByID($id);
			if ($menu->ordering != $order) $changes[$id] = $order;
		}
		foreach ($this->getByParentOrder($menutype, 0) as $menu) {
			$ordering = isset($changes[$menu->id]) ? $changes[$menu->id] : $menu->ordering;
			$allmenus[$menu->level][$ordering] = $menu->id;
		}
		$changed = false;
		$query = "UPDATE #__menu SET ordering = CASE ";
		foreach ($allmenus as $level=>$orderings) {
			$order = 10;
			ksort($orderings);
			foreach ($orderings as $ordering=>$id) {
				$menu = $this->getMenuByID($id);
				if ($order != $menu->ordering) {
					$query .= "WHEN id = $id THEN $order ";
					$changed = true;
				}
				$order += 10;
			}
		}
		if ($changed) {
			$query .= 'ELSE ordering END';
			aliroCoreDatabase::getInstance()->doSQL ($query);
			$this->clearCache();
		}
	}

	public function setPathway ($Itemid) {
        if ($Itemid) {
            $menu = $this->getMenuByID($Itemid);
            if ($menu->parent) $this->setPathway($menu->parent);
            $pathway = aliroPathway::getInstance();
            $pathway->addItem($menu->name, $menu->link."&Itemid=$Itemid");
        }
    }

    public function deleteMenus ($cid) {
    	if (is_array($cid)) {
    		foreach ($cid as &$id) $id = intval($id);
    		$idlist = implode(',', $cid);
    	}
    	else $idlist = intval($cid);
    	$database = aliroCoreDatabase::getInstance();
    	$database->doSQL ("DELETE FROM #__menu WHERE id IN($idlist)");
    	$this->clearCache();
    	self::$instance = __CLASS__;
    }

    public function saveMenu ($menu) {
    	if ($menu instanceof aliroMenuItem) {
    		$database = aliroCoreDatabase::getInstance();
    		if ($menu->id == 0) {
	    		$menu->parent = intval($menu->parent);
	    		$database->setQuery ("SELECT MAX(ordering) FROM `#__menu` WHERE `parent` = $menu->parent GROUP BY `parent`");
	    		$menu->ordering = $database->loadResult() + 1;
    		}
    		if (!$menu->store()) die ('Store menu object failed');
    		$this->clearCache();
    		self::$instance = __CLASS__;
    	}
    	else die ('Asked to store something not a menu object');
    }

}