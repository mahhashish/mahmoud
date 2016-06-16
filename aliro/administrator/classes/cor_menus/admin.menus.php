<?php

class menuInterface {
	public $stage = 0;
	public $html = '';
	public $save = false;
	public $finished = false;
	public $type = null;
	public $name = '';
	public $link = '';
	public $xmlfile = '';
}

class menusAdminMenus extends aliroComponentAdminControllers {

	private static $instance = __CLASS__;

	private $stage = 0;
	private $id = 0;
	private $componentid = 0;
	private $component = null;
	private $menutype = '';
	private $menuclass = '';
	private $save_ok = true;
	private $finished = false;
	private $mystuff = null;
	private $order = 0;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	public function getRequestData () {
		$this->task = $this->getParam($_REQUEST, 'task', 'list');
		$this->id = $this->getParam($_REQUEST, 'id', 0);
		$this->order = $this->getParam($_REQUEST, 'order', array());
		$this->componentid = $this->getStickyParam($_POST, 'menuselect', 0);
		if ($this->componentid) $this->component = aliroComponentHandler::getInstance()->getComponentById($this->componentid);
		if ($this->component) $this->menuclass = $this->component->menuclass;
		$this->menutype = $this->getStickyParam($_POST, 'menutype');
		if ($this->menuclass) {
			if ('next' == $this->task) {
				if (!isset($_SESSION['aliro_menu_component_stuff'])) $_SESSION['aliro_menu_component_stuff'] = new menuInterface;
				$this->mystuff =& $_SESSION['aliro_menu_component_stuff'];
				if (!$this->mystuff->finished) {
					$component_stuff = new $this->menuclass();
					$component_stuff->perform($this->mystuff, $this);
				}
			 	$this->save_ok = $this->mystuff->save;
			 	$this->finished = $this->mystuff->finished;
			}
			if ('save' == $this->task AND isset($_SESSION['aliro_menu_component_stuff'])) $this->mystuff =& $_SESSION['aliro_menu_component_stuff'];
		}
	}

	public function checkPermission () {
		return $this->authoriser->checkUserPermission('manage', 'aliroMenu', 0);
	}

	public function toolbar () {
		$toolbar = aliroAdminToolbar::getInstance();
		switch ($this->task) {
			case 'new':
				$toolbar->custom('next', 'next.png', 'next_f2.png', 'Next', false);
				$toolbar->cancel();
				break;

			case 'previous':
				$progress = isset($_SESSION['aliro_new_menu'][0]) ? $_SESSION['aliro_new_menu'][0] : null;
				$toolbar->custom('previous', 'back.png', 'back_f2.png', 'Previous', false);
				if ($progress AND $progress->finished) $toolbar->save();
				else $toolbar->custom('next', 'next.png', 'next_f2.png', 'Next', false);
				$toolbar->cancel();
				break;

			case 'next':
			case 'edit':
				if ($this->menuclass AND !$this->finished) {
					$toolbar->custom('next', 'next.png', 'next_f2.png', 'Next', false);
				}
				if ($this->save_ok) {
					$toolbar->save();
				}
				$toolbar->cancel();
				break;

			case 'list':
			default:
				$toolbar->deleteList();
				$toolbar->addNew();
				$toolbar->publish();
				$toolbar->unpublish();
		        $toolbar->custom('saveorder', 'save.png', 'save_f2.png', T_('Save order'), false);
				$toolbar->custom ('canceltype', 'cancel.png', 'cancel_f2.png', 'Cancel', false);
				break;
		}
	}

	public function listTask () {
		$this->unstick('menuselect');
		$menutype = $this->getStickyParam($_REQUEST, 'menutype');
		$handler = aliroMenuHandler::getInstance();
		if ($menutype) {
			$menus = $handler->getByParentOrder($menutype, 0);
			$total = count($menus);
			foreach ($menus as $key=>$menu) $bylevel[$menu->level][] = $key;
			if (isset($bylevel)) foreach ($bylevel as $level=>$items) {
				foreach ($items as $i=>$key) {
					$menus[$key]->upok = isset($items[$i-1]);
					$menus[$key]->downok = isset($items[$i+1]);
				}
			}
		}
		else $total = 0;
		$this->makePageNav ($total);
		if ($total) $rows = array_slice($menus, $this->pageNav->limitstart, $this->pageNav->limit);
		else $rows = array();
		// Create and activate a View object
		$view = new listMenusHTML ($this, $total);
		$view->view($rows, $menutype, aliroUser::getInstance()->id, $this->fulloptionurl);
	}

	public function removeTask () {
		$cid = $this->getParam($_POST, 'cid', array());
		$handler = aliroMenuHandler::getInstance();
		$handler->deleteMenus($cid);
		$this->unstick('menuselect');
		$this->redirect('index.php?core=cor_menus', 'Deletion completed');
	}

	public function cancelTypeTask () {
		$this->unstick('menutype');
		$this->unstick('menuselect');
		$this->redirect('index.php?core=cor_menutypes');
	}

	public function cancelTask () {
		$this->unstick('menuselect');
		$_SESSION['aliro_menu_component_stuff'] = null;
		$this->redirect('index.php?core=cor_menus');
	}

	public function newTask () {
		$_SESSION['aliro_menu_component_stuff'] = null;
		$components = array();
		$com_handler = aliroComponentHandler::getInstance();
		$rows = $com_handler->getAllComponents();
		foreach ($rows as $row) {
			$filename = substr($row->option,4);
			if ($row->menuclass OR $row->class OR file_exists($this->absolute_path."/components/$row->option/$filename.php")) {
				$components[] = $row;
			}
		}
		$view = new listMenusHTML ($this, 0);
		$view->selectorMenu(T_('URL or choice of component'), $components);
	}

	private function viewProgress ($progress, $last=false, $parents=null) {
		$view = new listMenusHTML ($this, 0);
		$view->selectorMenu($progress, $last, $parents);
	}

	private function makeComponentMenuItem () {
		$menu = new aliroMenuItem ();
		$menu->menutype = $this->menutype;
		$menu->linkComponentData ($this->component);
		return $menu;
	}

	private function makeURLMenuItem ($url) {
		$menu = new aliroMenuItem ();
		$menu->type = 'url';
		$menu->menutype = $this->getStickyParam($_POST, 'menutype');
		$menu->link = $url;
		return $menu;
	}

	public function nextTask () {
		if (isset($this->mystuff)) {
			if ($this->mystuff->finished) {
				$menu = $this->makeComponentMenuItem ();
				$menu->link = $this->mystuff->link;
				$menu->xmlfile = $this->mystuff->xmlfile;
				$menu->name = $this->mystuff->name;
				$this->editTask($menu);
			}
			else {
				$view = new listMenusHTML ($this, 0);
				$view->outputForm ($this->mystuff->html);
			}
		}
		else {
			if ($this->componentid) {
				$this->component = aliroComponentHandler::getInstance()->getComponentByID($this->componentid);
				$menu = $this->makeComponentMenuItem();
			}
			else $menu = $this->makeURLMenuItem ($this->getParam($_POST, 'menuurl'));
			$this->editTask($menu);
		}
	}

	public function previousTask () {
		if (array_shift($_SESSION['aliro_new_menu']) AND count($_SESSION['aliro_new_menu'])) {
			$this->viewProgress($_SESSION['aliro_new_menu'][0]);
		}
		else $this->cancelTask();
	}

	public function editTask ($menu=null) {
		if (empty($menu)) {
			if (!$this->id) $this->id = isset($this->cid[0]) ? $this->cid[0] : 0;
			if ($this->id) $menu = aliroMenuHandler::getInstance()->getMenuByID ($this->id);
		}
		if ($menu) {
			$xhandler = aliroExtensionHandler::getInstance();
			$extension = $xhandler->getExtensionByName($menu->component);
			if ('component' == $menu->type) $informalname = $extension->name;
			else $informalname = '';
			$auth_admin = aliroAuthorisationAdmin::getInstance();
			$menu_roles = $auth_admin->permittedRoles('view', 'aliroMenuItem', $menu->id);
			$roles = $this->authoriser->getAllRoles();
			$role_option = array(mosHTML::makeOption(0, T_('No restriction')));
			foreach ($roles as $role=>$translated) {
				$role_option[] = mosHTML::makeOption($role, $translated);
			}
			if (0 == count($menu_roles)) $menu_roles = array(0);
			$permitted = mosHTML::selectList( $role_option, 'roles[]', 'multiple="multiple"', 'value', 'text', $menu_roles);
			$paramxml = $menu->xmlfile ? $this->absolute_path.$menu->xmlfile : (is_object($extension) ? $this->absolute_path.$extension->xmlfile : '');
			$params = new aliroParameters ($menu->params, $paramxml);
			$view = new listMenusHTML ($this);
			$view->edit($menu, $informalname, $permitted, $params);
		}
		else {
			$this->setErrorMessage(T_('Please select a menu to edit', _ALIRO_ERROR_WARN));
			$this->listTask();
		}
	}

	public function saveTask () {
		$handler = aliroMenuHandler::getInstance();
		if ($this->id) $menu = $handler->getMenuByID ($this->id);
		else {
			if ($this->componentid) $menu = $this->makeComponentMenuItem ();
			else $menu = $this->makeURLMenuItem ($this->getParam($_POST, 'menuurl'));
		}
		$menu->bind($_POST);
		if (isset($this->mystuff)) {
			$menu->xmlfile = $this->mystuff->xmlfile;
			$menu->link = $this->mystuff->link;
		}
		$handler->saveMenu ($menu);
		$handler->clearCache();
		$_SESSION['aliro_menu_component_stuff'] = null;
		$this->listTask();
	}

	public function saveorderTask () {
		if (is_array($this->order)) aliroMenuHandler::getInstance()->updateOrdering ($this->order, $this->menutype);
		$this->redirect( 'index.php?core='.$this->option, T_('Ordering updated') );
	}

	public function orderupTask () {
		if ($this->id) aliroMenuHandler::getInstance()->changeOrder ($this->id, 'up', $this->menutype);
		$this->redirect( 'index.php?core='.$this->option, T_('Ordering updated') );
	}

	public function orderdownTask () {
		if ($this->id) aliroMenuHandler::getInstance()->changeOrder ($this->id, 'down', $this->menutype);
		$this->redirect( 'index.php?core='.$this->option, T_('Ordering updated') );
	}

	private function flipPublishTag ($new_publish) {
		$handler = aliroMenuHandler::getInstance();
		$handler->publishMenus($this->cid, $new_publish);
	}

	public function publishTask () {
		$this->flipPublishTag(1);
		$this->redirect( 'index.php?core='.$this->option, T_('Selected menu items published') );
	}

	public function unpublishTask () {
		$this->flipPublishTag(0);
		$this->redirect( 'index.php?core='.$this->option, T_('Selected menu items unpublished') );
	}


}

?>
