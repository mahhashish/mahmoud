<?php

/** Folder component - problem domain classes
/*  Author: Martin Brampton
/*  Date: December 2006
/*  Copyright (c) Martin Brampton 2006
/*  */

class aliroFolder extends aliroDatabaseRow {
	protected $DBclass = 'aliroDatabase';
	protected $tableName = '#__folders';
	protected $rowKey = 'id';
	protected $loaded = 0;
	protected $children = array();
	protected $isTrash = false;

	public function load ($key=null) {
		$database = aliroDatabase::getInstance();
		if ($this->loaded == 0 AND $this->id) {
			$database->setQuery("SELECT * FROM #__folders WHERE id=$this->id");
			$database->loadObject($this);
			$this->loaded = 1;
		}
	}

	public function trash () {
		$this->isTrash = true;
	}

	public function isTrash () {
		return $this->isTrash;
	}

	public function addChild ($id) {
		$this->children[] = $id;
	}

	public function deleteAll () {
		$folders = $this->getChildren(false);
		foreach ($folders as $folder) $folder->deleteAll ();
//      Need to delete things that are registered with this
		$this->trash();
	}

	public function setMetaData () {
		$mainframe = mosMainFrame::getInstance();
		$mainframe->prependMetaTag('description', strip_tags($this->name));
		if ($this->keywords) $mainframe->prependMetaTag('keywords', $this->keywords);
		else $mainframe->prependMetaTag('keywords', $this->name);
	}

	public function isCategory () {
		if ($this->parentid == 0) return true;
		else return false;
	}

	public function getCategoryName ($showself=false) {
		$category = $this->getCategory();
		if ($this->parentid OR $showself) return $category->name;
		return '*';
    }

    public function getCategory () {
		$folder = $this;
		while ($folder->parentid) $folder = $folder->getParent();
		return $folder;
	}

    public function getFamilyNames ($include=false) {
    	$names = $include ? '/'.$this->name : '';
    	$generation = 1;
    	$ancestor = $this;
    	while ($ancestor->parentid AND $generation < 3) {
    		$ancestor = $ancestor->getParent();
    		$generation++;
    		$names = '/'.$ancestor->name.$names;
    	}
    	if ($ancestor->parentid) $names = '..'.$names;
    	if ($names) return $names;
    	return '-';
    }

	public function addChildren (&$descendants, $published=true, $search='', $recurse=false) {
		$children = array();
		$handler = aliroFolderHandler::getInstance();
		foreach ($this->children as $i) {
			$folder = $handler->getBasicFolder($i);
			if ($published AND $folder->published == 0) continue;
			if ($search AND strpos(strtolower($folder->name), strtolower($search)) === false) continue;
			$children[] = $folder;
			$descendants[] = $folder;
		}
		if ($recurse) foreach ($children as $child) $child->addChildren ($descendants, $published, $search, $recurse);
		return $children;
	}

	public function getChildren ($published=true, $search='') {
		$children = array();
		$this->addChildren($children, $published, $search);
		return $children;
	}

	public function getDescendants ($search='') {
		$descendants = array();
		$this->addChildren ($descendants, false, $search, true);
		return $descendants;
	}

	public function getParent () {
		$handler = aliroFolderHandler::getInstance();
		$parent = $handler->getBasicFolder($this->parentid);
		return $parent;
	}

	public function getSelectList ($type, $parm, $published, $notThis=0) {
	    $alirohtml = aliroHTML::getInstance();
		$selector[] = $alirohtml->makeOption(0,T_('No parent'));
		$handler = aliroFolderHandler::getInstance();
		foreach ($handler->getCategories() as $category) $category->addSelectList('',$selector,$notThis,$published);
		return $alirohtml->selectList( $selector, $type, $parm, 'value', 'text', $this->id );
	}

	public function addSelectList ($prefix, &$selector, $notThis, $published) {
		if (($notThis == 0) OR ($this->id != $notThis)) $selector[] = aliroHTML::getInstance()->makeOption($this->id, $prefix.htmlspecialchars($this->name));
		foreach ($this->getChildren($published) as $folder) $folder->addSelectList($prefix.$this->name.'/',$selector,$notThis,$published);
	}

	function getURL () {
	}

	function setPathway () {
	}

	public static function getIcons () {
		$iconList = '';
		$live_site = aliroCore::get('mosConfig_live_site');
		$iconDir = new aliroDirectory (aliroCore::get('mosConfig_admin_absolute_path').'/components/com_folders/images/folder_icons');
		$files = $iconDir->listAll();
		$ss = 0;
		foreach ($files as $file) {
			$iconList.="\n<a href=\"JavaScript:paste_strinL('{$file}')\" onmouseover=\"window.status='{$file}'; return true\"><img src=\"{$live_site}/administrator/components/com_folders/images/folder_icons/{$file}\" width=\"32\" height=\"32\" border=\"0\" alt=\"{$file}\" /></a>&nbsp;&nbsp;";
	        $ss++;
			if ($ss % 10 == 0) $iconList.="<br/>\n";
		}
		return $iconList;
	}


	public function togglePublished ($idlist, $value) {
		$cids = implode( ',', $idlist );
		$sql = "UPDATE #__folders SET published=$value". "\nWHERE id IN ($cids)";
		remositoryRepository::doSQL ($sql);
	}

	public function imageURL($imageName, $width=32, $height=32) {
		$live_site = aliroCore::get('mosConfig_live_site');
		$element = '<img src="';
		$element .= $live_site.'/administrator/components/com_folders/images/'.$imageName;
		$element .= '" width="';
		$element .= $width;
		$element .= '" height="';
		$element .= $height;
		$element .= '" border="0" align="middle" alt="';
		$element .= $imageName;
		$element .= '"/>';
		return $element;
	}

}

class aliroFolderHandler extends cachedSingleton  {
	protected static $instance = __CLASS__;
	private $rows = array();
	private $byancestor = array();
	private $anchor = '';

	protected function __construct () {
		$database = aliroDatabase::getInstance();
		$this->anchor = new aliroFolder();
		$sql = 'SELECT id, parentid, name, published, ordering FROM #__folders ORDER BY ordering, name';
		$this->rows = $database->doSQLget($sql, 'aliroFolder', 'id');
		foreach ($this->rows as $row) {
			$folder = $row;
			do $this->byancestor[$folder->parentid][$row->id] = $folder->id;
			while ($folder->parentid AND $folder = $this->rows[$folder->parentid]);
			if ($row->parentid) {
				$parent = $this->rows[$row->parentid];
				$parent->addChild($row->id);
			}
			else $this->anchor->addChild($row->id);
		}
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	public function getBasicFolder ($id) {
		if ($id == 0) return $this->anchor;
		return isset($this->rows[$id]) ? $this->rows[$id] : null;
	}

	public function getFolder ($id) {
		global $database;
		$result = $this->getBasicFolder($id);
		if ($result) $result->load();
		return $result;
	}

	public function getCategories ($published = false, $search = null) {
		$categories = array();
		foreach ($this->anchor->getChildren() as $category) {
			if ($published AND $category->published == 0) continue;
			if ($search AND strpos(strtolower($category->name), strtolower($search)) === false) continue;
			$categories[] = $category;
		}
		return $categories;
	}

	public function getDescendantIDList ($id, $search='') {
		$top = $this->getBasicFolder ($id);
		$descendants = $top->getDescendants ($search);
		$list = $id;
		foreach ($descendants as $descendant) $list .= ','.$descendant->id;
		return $list;
	}

	public function getSelectList ($allowTop, $default, $type, $parm, &$user) {
	    $alirohtml = aliroHTML::getInstance();
		if ($allowTop) $selector[] = $alirohtml->makeOption(0,_DOWN_NO_PARENT);
		foreach ($this->getCategories() as $category) $category->addSelectList('', $selector, null, $user);
		if (isset($selector)) return $alirohtml->selectList( $selector, $type, $parm, 'value', 'text', $default );
		else return '';
	}

	// Only needed for testing
	public function displayChildren (&$folder) {
		echo '<br />'.$folder->name.' has children:<br />';
		foreach ($folder->children as $child) echo $child->name.' whose parent is '.$child->parent->name.'<br />';
		foreach ($folder->children as $child) displayChildren ($child);
	}

	public function delete ($deletelist) {
		$database = aliroDatabase::getInstance();
		$sql = "DELETE FROM #__folders WHERE id IN ($deletelist)";
		$database->doSQL($sql);
		$this->clearCache();
		self::$instance = __CLASS__;
	}

    public function changeOrder ($id, $direction) {
		$folder = $this->getFolder($id);
		$movement = 'down' == $direction ? 15 : -15;
		$this->updateOrdering (array($id => $folder->ordering + $movement), $folder->parentid);
    }

	public function updateOrdering ($orders, $parentid) {
		foreach ($orders as $id=>$order) {
			$folder =  $this->getFolder($id);
			if ($folder->ordering != $order) $changes[$id] = $order;
		}
		$parent = $this->getFolder($parentid);
		foreach ($parent->getChildren(false) as $folder) {
			$ordering = isset($changes[$folder->id]) ? $changes[$folder->id] : $folder->ordering;
			$allfolders[$ordering] = $folder->id;
		}
		$changed = false;
		$query = "UPDATE #__folders SET ordering = CASE ";
		$order = 10;
		ksort($allfolders);
		foreach ($allfolders as $ordering=>$id) {
			$folder = $this->getFolder($id);
			if ($order != $folder->ordering) {
				$query .= "WHEN id = $id THEN $order ";
				$changed = true;
			}
			$order += 10;
		}
		if ($changed) {
			$query .= 'ELSE ordering END';
			aliroDatabase::getInstance()->doSQL ($query);
			$this->clearCache();
		}
	}

	public function ancestorsWithParent ($parentid, $folderIDs) {
		$ancestors = array();
		if (!is_array($folderIDs)) return $ancestors;
		foreach ($folderIDs as $folderID) if (isset($this->byancestor[$parentid][$folderID])) $results[$this->byancestor[$parentid][$folderID]] = 1;
		if (isset($results)) foreach (array_keys($results) as $id) $ancestors[] = $this->getFolder($id);
		return $ancestors;
	}

	public function setFolderPathway ($folder, $baseurl) {
		if ($folder->parentid) $this->setFolderPathway ($folder->getParent(), $baseurl);
		$link = aliroSEF::getInstance()->sefRelToAbs($baseurl.$folder->id);
		aliroPathway::getInstance()->addItem ($folder->name, $link);
	}

}