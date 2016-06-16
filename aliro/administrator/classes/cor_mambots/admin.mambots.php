<?php

class mambotsAdminMambots extends aliroDBUpdateController {

	protected $session_var = 'cor_mambots_classid';
	protected $table_name = '#__mambots';
	protected $DBname = 'aliroCoreDatabase';
	protected $view_class = 'listMambotsHTML';
	public $list_exclude = array ('params');
	protected $function_exclude = array ('new', 'remove', 'apply');

	public static function getInstance ($manager) {
		if (self::$instance == null) self::$instance = new mambotsAdminMambots ($manager);
		return self::$instance;
	}

	public function publishTask () {
		$this->setPublished(1);
	}

	public function unpublishTask () {
		$this->setPublished(0);
	}

	public function editTask () {
		if ($this->id) {
			$this->setID($this->id);
			$mambot = new aliroMambot;
			$mambot->load($this->id);
			$xmlfile = $this->absolute_path.aliroExtensionHandler::getInstance()->getXMLFileName($mambot->element);
			$params = new aliroParameters($mambot->params, $xmlfile, 'mambot');
			$view = new listMambotsHTML($this);
			$view->edit($this->id, $params, $mambot->published);
		}
		else $this->redirect('index.php?core=cor_mambots', T_('No plugin specified for editing'));
	}

	private function setPublished ($value) {
		$id = $this->getParam($_REQUEST, 'id', 0);
		$database = call_user_func(array($this->DBname, 'getInstance'));
		$database->doSQL ("UPDATE #__mambots SET published=$value WHERE id=$id");
		$handler = aliroMambotHandler::getInstance();
		$handler->clearCache();
		$this->redirect($this->optionurl, _ALIRO_ERROR_WARN);
	}

	public function saveTask () {
		if ($id = $this->getID()) {
			$mambot = new aliroMambot;
			$mambot->load($id);
			$mambot->published = '0';
			if (!$mambot->bindOnly($_POST, 'published, params')) {
				$view = new listMambotsHTML($this);
				$view->edit($this->id, $params, $mambot->published);
			}
			$mambot->published = $mambot->published ? 1 : 0;
			$mambot->store();
			$this->redirect('index.php?core=cor_mambots', T_('Plugin update saved'));
		}
		$this->redirect('index.php?core=cor_mambots', T_('Plugin update unexpected, no action taken'), _ALIRO_ERROR_WARN);
	}

}

?>