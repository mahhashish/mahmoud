<?php
/**
*
* Aliro module manager
*
*/

class modulesAdminModules extends aliroComponentAdminControllers {

	private static $instance = __CLASS__;

	private $client;
	private $order;
	private $moduleid;
	private $mid;
	private $codebase;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	public function getRequestData () {
		$this->authoriser = aliroAuthoriser::getInstance();
		$this->client = $this->getStickyParam($_REQUEST, 'client');
		$this->moduleid = $this->getParam($_REQUEST, 'moduleid');
		$this->order = $this->getParam($_REQUEST, 'order', array());
		if ($this->cid[0] == 0) {
			if (isset($this->moduleid)) $this->cid[0] = $this->moduleid;
			else $this->cid[0] = $this->getParam($_GET, 'id', 0);
		}

		$this->mid = $this->getParam($_POST, 'id');
		$this->codebase = $this->getParam($_POST, 'codebase');
	}

	public function checkPermission () {
		return $this->authoriser->checkUserPermission('manage', 'aliroModule', 0);
	}

	public function toolbar () {
		$toolbar = aliroAdminToolbar::getInstance();

		switch ($this->task) {

			case 'editA':
			case 'edit':
			case 'next':
				$published = 0;
				if ($this->mid) {
					$database = aliroCoreDatabase::getInstance();
					$query = "SELECT published FROM #__modules WHERE id='$mid'";
					$database->setQuery( $query );
					$published = $database->loadResult();
				}
				$mainframe = mosMainFrame::getInstance();
				$cur_template = $mainframe->getTemplate();
				$toolbar->save();
				$toolbar->apply();
				$toolbar->cancel();
				break;

			case 'new':
				// What is ref supposed to be???
				$ref = '';
				if ($this->codebase) echo 'Different toolbar';
        		$toolbar->cancel();
        		$toolbar->custom('next', 'next.png', 'next_f2.png', T_('Next'), false);
        		$toolbar->help( $ref.'new' );
				break;

			case 'newcust':
				// What is ref supposed to be???
				$ref = '';
				if ($this->codebase) echo 'Different toolbar';
        		$toolbar->preview( 'modulewindow' );
        		$toolbar->save();
        		$toolbar->apply();
        		$toolbar->cancel();
        		$toolbar->help( $ref.'new' );
				break;

			case 'newstd':
				// What is ref supposed to be???
				$ref = '';
				if ($this->codebase) echo 'Different toolbar';
        		$toolbar->save();
        		$toolbar->apply();
        		$toolbar->cancel();
        		$toolbar->help( $ref.'new' );
				break;

			default:
		        $toolbar->publishList();
		        $toolbar->unpublishList();
		        // New replaces copy - can build from any module code
		        // $toolbar->custom( 'copy', 'copy.png', 'copy_f2.png', T_('Copy'), true );
		        $toolbar->addNewX();
		        $toolbar->editListX();
		        $toolbar->deleteList();
		        $toolbar->custom('saveorder', 'save.png', 'save_f2.png', T_('Save order'), false);
		        $toolbar->help( 'admin.manager' );
				break;
		}
	}

	public function listTask () {

		$filter_position = $this->getUserStateFromRequest( "filter_position{$this->option}{$this->client}", 'filter_position');
		$filter_type = $this->getUserStateFromRequest( "filter_type{$this->option}{$this->client}", 'filter_type');
		$search = $this->getUserStateFromRequest( "search{$this->option}{$this->client}", 'search', '' );

		$handler = aliroModuleHandler::getInstance();
		$rows = $handler->getSelectedModules ($filter_position, $filter_type, $search, ($this->client == 'admin'));
		$total = count($rows);
		$this->makePageNav ($total);
		// get list of Positions for dropdown filter
		$lists['position']	= $this->buildPositions('filter_position', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', $filter_position, true);
		// get list of module code names for dropdown filter
		$mtypes = $this->client == 'user' ? $handler->getDistinctNames() : array();
		$htmlhandler = aliroHTML::getInstance();
		$types[] = $htmlhandler->makeOption( '0', T_('- All Types -') );
		foreach ($mtypes as $m) $types[] = $htmlhandler->makeOption($m, $m);
		$lists['type']	= $htmlhandler->selectList( $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_type );
		$rowslice = array_slice($rows, $this->pageNav->limitstart, $this->pageNav->limit);
		$view = new HTML_modules($this);
		$view->showModules($rowslice, aliroUser::getInstance()->id, $this->client, $lists, $search, $this->fulloptionurl);
	}

	private function buildPositions ($name, $style, $value, $allowAll=false) {
		$htmlhandler = aliroHTML::getInstance();
		$thandler = aliroTemplateHandler::getInstance();
		$positions = array();
		$available = ('admin' == $this->client) ? $thandler->getAllAdminPositions() : $thandler->getAllUserPositions();
		foreach ($available as $position=>$names) $positions[$position] = $htmlhandler->makeOption($position, $position.' - '.$names);
		ksort($positions);
		if ($allowAll) array_unshift ($positions, $htmlhandler->makeOption( '0', T_('- All Positions -') ));
		return $htmlhandler->selectList( $positions, $name, $style, 'value', 'text', $value );
	}

	public function newTask () {
		$xhandler = aliroExtensionHandler::getInstance();
		$modulecodes = $xhandler->getExtensions('module');
		foreach ($modulecodes as $code) {
			if (strlen($code->description) > 50) $code->description = substr($code->description,0,50).'...';
			$options[] = mosHTML::makeOption($code->id, $code->formalname.' - '.$code->description);
		}
		if (count($options)) {
			$view = new HTML_modules($this);
			$view->showNew ($options);
		}
		else $this->setErrorMessage(T_('No module extensions have been installed yet'), _ALIRO_ERROR_FATAL);
	}

	public function nextTask () {
		if ($this->codebase) {
			$database = aliroCoreDatabase::getInstance();
			$database->setQuery("SELECT * FROM #__extensions WHERE id=$this->codebase");
			$extension = null;
			$database->loadObject($extension);
			$handler = aliroModuleHandler::getInstance();
			$module = $handler->makeModuleFromExtension ($extension);
			$this->editTask($module);
		}
		else die ('Need code for custom module');
	}

	public function editATask () {
		$this->editTask();
	}

	public function editTask ($row=null) {
		$my = aliroUser::getInstance();
		$handler = aliroModuleHandler::getInstance();
		if (!$row) {
			$row = $handler->getModuleByID($this->cid[0]);
			$auth_admin = aliroAuthorisationAdmin::getInstance();
			$module_roles = $auth_admin->permittedRoles('view', 'aliroModule', $this->cid[0]);
			$menus = $handler->getMenus($row->id);
		}
		else {
			$module_roles = array();
			$menus = array(0);
		}
		// fail if checked out not by 'me'
		if ( $row->checked_out AND $row->checked_out != $my->id ) {
			$this->setErrorMessage(T_('The module %s is currently being edited by another administrator'));
			$this->listTask();
			return;
		}
		$row->customcontent = htmlspecialchars(str_replace('&amp;', '&', $row->customcontent));
		$row->checkout($my->id);

		$orders = $handler->getModulesByPosition (($this->client == 'admin'));
		foreach ($orders as $position=>$modules) {
			foreach ($modules as $subscript=>$module) {
				$ord = $subscript + 1;
				$orders2[$position][] = mosHTML::makeOption($ord, $ord.'::'.addslashes( $module->title));
			}
		}

		// build the html select list
		$active = $row->position ? $row->position : 'left';
		$lists['position'] = $this->buildPositions('position', 'class="inputbox" size="1" ', $active, false );

		$roles = $this->authoriser->getAllRoles();
		$role_option = array(mosHTML::makeOption(0, T_('No restriction')));
		foreach ($roles as $role=>$translated) {
			$role_option[] = mosHTML::makeOption($role, $translated);
		}
		if (0 == count($module_roles)) $module_roles = array(0);
		$lists['access'] = mosHTML::selectList( $role_option, 'roles[]', 'multiple="multiple"', 'value', 'text', $module_roles);

		$lists['selections'] = aliroSelectors::getInstance()->menuLinks ($menus, 1, 1);
		$lists['showtitle'] = aliroHTML::getInstance()->yesnoRadioList( 'showtitle', 'class="inputbox"', $row->showtitle );
		$lists['client_id'] = (bool) ($this->client == 'admin');

		// build the html select list for published
		$lists['published'] = aliroHTML::getInstance()->yesnoRadioList( 'published', 'class="inputbox"', $row->published );

		// xml file for module
		$xhandler = aliroExtensionHandler::getInstance();
		$extension = $xhandler->getExtensionByName ($row->module);
		if ($extension) {
			$row->description = $extension->description;
			// get params definitions
			$params = new aliroParameters( $row->params, $this->absolute_path.$extension->xmlfile, 'module' );
		}
		else {
			$row->description = '';
			$params = new aliroParameters('');
		}
		$handler->clearCache();

		$view = new HTML_modules($this);
		$view->editModule( $row, $lists, $params, $this->option );
	}

	private function storeModule ($row) {
		if (!$row->check()) {
			$this->setErrorMessage($row->getError(), _ALIRO_ERROR_FATAL);
			die($row->getError());
		}
		if (!$row->store()) {
			$this->setErrorMessage(T_('Store of module failed'), _ALIRO_ERROR_FATAL);
			die(T_('Store of module failed'));
		}
		$row->checkin();
		$where = ('admin' == $this->client) ? "(admin & 2)" : "(admin & 1)";
		if (is_array($this->order)) aliroModuleHandler::getInstance()->updateOrdering ($this->order);
		// $row->updateOrder( "position='$row->position' AND ($where)" );
	}

	private function commonSave () {
		$row = new aliroModule();
		$row->id = $this->mid;
		if ($row->id) $row->load();
		if (!$row->bind( $_POST, 'selections' )) return null;
		$row->admin = 'admin' == $this->client ? 2 : 1;
		$xhandler = aliroExtensionHandler::getInstance();
		$extension = $xhandler->getExtensionByName($row->module);
		$row->class = $extension->class;
		$row->adminclass = $extension->adminclass;
		$this->storeModule($row);
		$roles = $this->getParam($_POST, 'roles', array());
		$roles = $this->authoriser->minimizeRoleSet ($roles);
		$auth_admin = aliroAuthorisationAdmin::getInstance();
		$auth_admin->dropPermissions ('view', 'aliroModule', $row->id);
		foreach ($roles as $role) if ($role) $auth_admin->permit($role, 2, 'view', 'aliroModule', $row->id);
		$menus = $this->getParam($_POST, 'selections', array());
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL("DELETE FROM `#__modules_menu` WHERE `moduleid` = $row->id");
		foreach ($menus as $menu) {
			$menu = intval($menu);
			if ($menu >= 0) $database->doSQL("INSERT INTO `#__modules_menu` VALUES ($row->id, $menu)");
		}
		aliroModuleHandler::getInstance()->clearCache();
		return $row;
	}

	public function saveTask () {
		if ($row = $this->commonSave()) $msg = sprintf(T_('Successfully Saved Module: %s'), $row->title);
		else $msg = '';
		$this->redirect ( 'index.php?core='.$this->option.'&client='.$this->client, $msg );
	}

	public function applyTask () {
		if ($row = $this->commonSave()) $msg = sprintf(T_('Successfully Saved changes to Module: %s'), $row->title) ;
		else $msg = '';
		$this->redirect ( 'index.php?core='.$this->option.'&client='.$this->client .'&task=editA&hidemainmenu=1&id='.$row->id, $msg );
	}

	public function copyTask () {
		$my = aliroUser::getInstance();
		$handler = aliroModuleHandler::getInstance();
		$row = $handler->getModuleByID($this->cid[0]);
		$row->title = T_('Copy of ').$row->title;
		$row->id = 0;
		$row->published = 0;
		$this->storeModule($row);
		$handler = aliroModuleHandler::getInstance();
		$menus = $handler->getMenus($this->cid[0]);
		$database = aliroCoreDatabase::getInstance();
		foreach ($menus as $menuid) {
			$database->setQuery("INSERT INTO #__modules_menu SET moduleid='$row->id', menuid='$menuid'");
			$database->query();
		}
		$handler->clearCache();
	}

	public function removeTask () {
		if (count($this->cid) == 1 AND $this->cid[0] == 0) {
			$msg = T_('Your chosen action requires the selection of a module');
			$this->redirect ('index.php?core='.$this->option.'&client='.$this->client, $msg );
		}
		$handler = aliroModuleHandler::getInstance();
		$handler->deleteModules($this->cid);
		$mod = new aliroModule();
		$mod->ordering = 0;
		$mod->updateOrder( "position='left'" );
		$mod->updateOrder( "position='right'" );
		$handler->clearCache();
		$this->redirect( 'index.php?core='.$this->option.'&client='.$this->client, T_('Module deletions completed') );
	}

	public function saveorderTask () {
		if (is_array($this->order)) aliroModuleHandler::getInstance()->updateOrdering ($this->order);
		$this->redirect( 'index.php?core='.$this->option.'&client='.$this->client, T_('Ordering updated') );
	}

	public function orderupTask () {
		if (isset($this->cid[0])) aliroModuleHandler::getInstance()->changeOrder ($this->cid[0], 'up');
		$this->redirect( 'index.php?core='.$this->option.'&client='.$this->client, T_('Ordering updated') );
	}

	public function orderdownTask () {
		if (isset($this->cid[0])) aliroModuleHandler::getInstance()->changeOrder ($this->cid[0], 'down');
		$this->redirect( 'index.php?core='.$this->option.'&client='.$this->client, T_('Ordering updated') );
	}

	public function cancelTask () {
		$row = new aliroModule();
		// ignore array elements
		$row->bind( $_POST, 'selections params' );
		$row->checkin();
		$handler = aliroModuleHandler::getInstance();
		$handler->clearCache();
		$this->redirect ('index.php?core='.$this->option.'&client='.$this->client);
		$msg = sprintf(T_('Module Copied [%s]'), $row->title);
		$this->redirect( 'index.php?core='.$this->option.'&client='.$this->client, $msg );
	}

	private function flipPublishTag ($new_publish) {
		$handler = aliroModuleHandler::getInstance();
		$handler->publishModules($this->cid, $new_publish);
	}

	public function publishTask () {
		$this->flipPublishTag(1);
		$this->redirect( 'index.php?core='.$this->option.'&client='.$this->client, T_('Selected modules published') );
	}

	public function unpublishTask () {
		$this->flipPublishTag(0);
		$this->redirect( 'index.php?core='.$this->option.'&client='.$this->client, T_('Selected modules unpublished') );
	}

}