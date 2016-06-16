<?php

class menutypesAdminMenutypes extends aliroComponentAdminControllers {
	
	private static $instance = __CLASS__;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	function getRequestData () {
		$this->authoriser = aliroAuthoriser::getInstance();
	}
	
	function checkPermission () {
		return $this->authoriser->checkUserPermission('manage', 'aliroMenu', 0);
	}
	
	function toolbar () {
		mosMenuBar::startTable();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::addNew();
		mosMenuBar::spacer();
		mosMenuBar::editList();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();		
	}
	
	function listTask () {
		$handler = aliroMenuHandler::getInstance();
		$types = $handler->getMenutypes();
		$total = count($types);
		$this->makePageNav ($total);

		// Create and activate a View object
		$view = new listMenutypesHTML ($this, $total);
		$view->view($types);
	}
	
	function removeTask () {
		$database = aliroCoreDatabase::getInstance();
		$cid = $this->getParam($_POST, 'cid', array());
		foreach ($cid as &$name) $name = $database->getEscaped($name);
		$extlist = "'".implode ("', '", $cid)."'";
		$this->removeMenus($extlist, $database);
		$this->redirect('index.php?core=cor_menutypes', 'Deletion completed');
	}
	
	function removeMenus ($extlist, $database) {
		$database->setQuery("DELETE FROM `#__menu` WHERE `menutype` IN ($extlist)");
		$database->query();
	}
	
	function editTask () {
		$cid = $this->getParam($_POST, 'cid', array());
		$this->redirect("index.php?core=cor_menus&task=list&menutype={$cid[0]}");
	}
	
	function newTask () {
		$this->redirect("index.php?core=cor_menus&task=list");
	}
	
	function cancelTask () {
		$this->redirect("index.php?core=cor_menutypes&task=list");
	}
	
}

?>