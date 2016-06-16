<?php

class extensionsAdminExtensions extends aliroComponentAdminControllers {

	private static $instance = __CLASS__;
	private $type;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	public function getRequestData () {
		$types = array ('component', 'module', 'mambot', 'template', 'language', 'include', 'parameter');
		$this->authoriser = aliroAuthoriser::getInstance();
		$this->type = $this->getParam($_REQUEST, 'type');
		if (!in_array($this->type, $types)) $this->type = '';
	}

	public function checkPermission () {
		return $this->authoriser->checkUserPermission('manage', 'anExtension', 0);
	}

	public function toolbar () {
		aliroAdminToolbar::getInstance()->deleteList();
	}

	public function listTask () {
		$handler = aliroExtensionHandler::getInstance();
		$rows = $handler->getExtensions($this->type);
		$total = count($rows);
		$this->makePageNav ($total);
		if ($total) $rows = array_slice($rows, $this->pageNav->limitstart, $this->pageNav->limit);
		else $rows = array();
		$htmlassist = aliroHTML::getInstance();
		$types = array ('all', 'component', 'module', 'mambot', 'template', 'language', 'include', 'parameter');
		foreach ($types as $type) $options[] = $htmlassist->makeOption($type);
		$typelist = $htmlassist->selectList($options, 'type', 'class="inputbox" onchange="submitbutton(\'list\')"', 'value', 'value', $this->type);
		// Create and activate a View object
		$view = new listExtensionsHTML ($this);
		$view->view($rows, $typelist);
	}

	public function removeTask () {
		$database = aliroCoreDatabase::getInstance();
		$cid = $this->getParam($_POST, 'cid', array());
		if (0 == count($cid)) {
			$this->errorSet(T_('Please select one or more items for deletion'));
			$this->listTask();
			return;
		}
		foreach ($cid as &$name) $name = $database->getEscaped($name);
		aliroExtensionHandler::getInstance()->removeExtensions($cid);
		$this->redirect('index.php?core=cor_extensions', 'Deletion completed');
	}

	private function remove_component ($extension, $basepath) {
		$dir = new aliroDirectory($basepath.'/components/'.$extension->formalname);
		$dir->deleteAll();
		$dir = new aliroDirectory($basepath.$this->getCfg('admin_dir').'/components/'.$extension->formalname);
		$dir->deleteAll();
		aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Uninstaller could not find XML file for component %s'), $extension->formalname), _ALIRO_ERROR_WARN);
	}

	private function remove_module ($extension, $basepath) {
		if ($extension->admin & 1) $dir = new aliroDirectory($basepath.'/modules/'.$extension->formalname);
		else $dir = new aliroDirectory($basepath.$this->getCfg('admin_dir').'/modules/'.$extension->formalname);
		$dir->deleteAll();
		aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Uninstaller could not find XML file for module %s'), $extension->formalname), _ALIRO_ERROR_WARN);
	}

	private function remove_mambot ($extension, $basepath) {
		$manager = aliroFileManager::getInstance();
		if (is_dir($basepath.'/mambots/'.$extension->formalname)) {
			$dir = new aliroDirectory ($basepath.'/mambots/'.$extension->formalname);
			$dir->deleteAll();
		}
		aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Uninstaller could not find XML file for mambot %s'), $extension->formalname), _ALIRO_ERROR_WARN);
	}

	private function remove_template ($extension, $basepath) {
		$dir = new aliroDirectory ($basepath.(($extension->admin & 2) ? $this->getCfg('admin_dir') : '' ).'/templates/'.$extension->formalname);
		$dir->deleteAll();
		aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Uninstaller could not find XML file for template %s'), $extension->formalname), _ALIRO_ERROR_WARN);
	}

	private function remove_language ($extension, $basepath) {
		aliroRequest::getInstance()->setErrorMessage (sprintf(T_('Uninstaller could not find XML file for language %s'), $extension->formalname), _ALIRO_ERROR_FATAL);
	}

}

?>