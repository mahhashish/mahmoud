<?php

class extensionsAdminExtensions extends aliroComponentAdminControllers {

	private static $instance = null;
	private static $types = array();
	private $type = '';
	private $title = '';
	private $subtitle = '';
	private $isUpgrade = false;
	private $userurl = '';
	private $userfile = '';

	protected function __construct ($manager) {
		parent::__construct($manager);
		$maintitle = T_('New Extension');
		$this->subtitle = T_('(application, component, module, plugin, template, language, include, parameter)');
		$this->title = "$maintitle <small><small>$this->subtitle</small></small>";
	}

	protected function __clone () {}

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self($manager));
	}

	public function getRequestData () {
		if (empty(self::$types)) self::$types = array ('application', 'component', 'module', 'plugin', 'template', 'language', 'include', 'parameter');
		$this->authoriser = aliroAuthoriser::getInstance();
		$this->type = $this->getParam($_REQUEST, 'type');
		$this->isUpgrade = $this->getParam($_REQUEST, 'upgrade') ? true : false;
		$this->userfile = $this->getParam($_REQUEST, 'userfile', '');
		$this->userurl = $this->getParam( $_REQUEST, 'userurl', '' );
		if (!in_array($this->type, self::$types)) $this->type = '';
	}

	public function checkPermission () {
		return $this->authoriser->checkUserPermission('manage', 'anExtension', 0);
	}

	public function toolbar () {
		$toolbar = aliroAdminToolbar::getInstance();
		switch ($this->task){
			case 'new':
			case 'uploadfile':
			case 'installfromurl':
			case 'installfromAliro':
			case 'aliro':
				$toolbar->cancel();
				break;

			default:
				$toolbar->deleteList();
				$toolbar->addNew();
		}
	}

	public function listTask () {
		$handler = aliroExtensionHandler::getInstance();
		$type = 'plugin' == $this->type ? 'mambot' : $this->type;
		$rows = $handler->getExtensions($type);
		$total = count($rows);
		$this->makePageNav ($total);
		if ($total) $rows = array_slice($rows, $this->pageNav->limitstart, $this->pageNav->limit);
		else $rows = array();
		$htmlassist = aliroHTML::getInstance();
		$options[] = $htmlassist->makeOption(T_('All types'));
		foreach (self::$types as $type) $options[] = $htmlassist->makeOption($type);
		$typelist = $htmlassist->selectList($options, 'type', 'class="inputbox" onchange="document.adminForm.submit();"', 'value', 'value', $this->type);
		// Create and activate a View object
		$view = new listExtensionsHTML ($this);
		$view->view($rows, $typelist, $this->subtitle);
	}
	
	public function cancelTask () {
		$this->listTask();
	}

	public function removeTask () {
		$database = aliroCoreDatabase::getInstance();
		$cid = $this->getParam($_POST, 'cid', array());
		if (0 == count($cid)) {
			$this->setErrorMessage(T_('Please select one or more items for deletion'));
			$this->listTask();
			return;
		}
		$xhandler = aliroExtensionHandler::getInstance();
		foreach ($cid as $name) {
			$extension = $xhandler->getExtensionByName($name);
			if ($extension) $xhandler->removeApplications($extension->application);
		}
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

	public function newTask () {
		$viewer = new HTML_installer($this);
	 	$viewer->showInstallForm($this->title, '', dirname(__FILE__));
	}

	public function uploadfileTask () {
		$installer = new aliroInstaller();
		$installer->uploadfile($this->isUpgrade);
	 	$viewer = new HTML_installer($this)	;
	 	$viewer->showInstallForm($this->title, 'universal', '', dirname(__FILE__) );
	}

	public function installfromurlTask () {
		$installer = new aliroInstaller();
		$installer->installfromurl($this->userurl, $this->isUpgrade);
	 	$viewer = new HTML_installer($this)	;
	 	$viewer->showInstallForm($this->title, 'universal', '', dirname(__FILE__) );
	}

	public function installfromfileTask ($upgrade=false) {
		$installer = new aliroInstaller();
		$installer->installfromfile($this->userfile, $this->isUpgrade);
	 	$viewer = new HTML_installer($this)	;
	 	$viewer->showInstallForm($this->title, 'universal', '', dirname(__FILE__) );
	}

	public function installfromAliroTask () {
		$installer = new aliroInstaller();
		$installer->installfromurl('aliro', $this->isUpgrade);
	 	$viewer = new HTML_installer($this)	;
	 	$viewer->showInstallForm($this->title, 'universal', '', dirname(__FILE__) );
	}

	public function aliroTask () {
		$viewer = new HTML_installer($this)	;
	 	$viewer->aliroForm ($option, $element, $client);
	}
}