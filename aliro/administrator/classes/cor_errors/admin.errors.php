<?php

class errorsAdminErrors extends aliroComponentAdminControllers {
	protected static $instance = __CLASS__;
	
	protected $errorid = 0;
	protected $cid = array();
	protected $view_class = 'listErrorsHTML';
	
	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}
	
	public function getRequestData () {
		$this->cid = $this->getParam($_POST, 'cid', array());
		$this->errorid = $this->getParam($_REQUEST, 'id', 0);
		if (!$this->errorid) $this->errorid = isset($this->cid[0]) ? intval($this->cid[0]) : 0;
	}

	public static function taskTranslator () {
		return array (
		'cancel' => T_('Cancel'),
		'edit' => T_('Show Details'),
		'remove' => T_('Delete')
		);
	}

	public function toolbar () {
	    if ('edit' == $this->task) $this->toolBarButton('cancel');
	    else {
			$this->toolBarButton('edit', true);
			$this->toolBarButton('remove', true);
		}
	}

	public function listTask () {
	    $database = aliroCoreDatabase::getInstance();
	    $database->setQuery("SELECT COUNT(id) FROM #__error_log");
	    $total = $database->loadResult();
	    $this->makePageNav($total);
	    $database->setQuery("SELECT id, timestamp, smessage FROM #__error_log ORDER BY timestamp DESC LIMIT {$this->pageNav->limitstart}, {$this->pageNav->limit}");
	    $errors = $database->loadObjectList();
		$view = new $this->view_class($this);
		if ($errors) $view->showErrors($errors);
		else $view->showErrors(array());
	}

	public function editTask () {
		if ($this->errorid) {
			$database = aliroCoreDatabase::getInstance();
			$database->setQuery("SELECT * FROM #__error_log WHERE id = $this->errorid");
			$database->loadObject($error);
			$view = new $this->view_class($this);
			$view->showDetailedError ($error);
		}
		else $this->redirect('index.php?core=cor_errors', T_('Please select an item for detailed display'), _ALIRO_ERROR_WARN);
	}

	public function removeTask () {
		if (count($this->cid)) {
			foreach ($this->cid as &$item) $item = intval($item);
			$idlist = implode(',', $this->cid);
			$database = aliroCoreDatabase::getInstance();
			$database->doSQL("DELETE FROM #__error_log WHERE id IN ($idlist)");
			$this->redirect('index.php?core=cor_errors');
		}
		else $this->redirect('index.php?core=cor_errors', T_('Please select an item for deletion'), _ALIRO_ERROR_WARN);
	}
	
	public function cancelTask () {
		$this->redirect('index.php?core=cor_errors');
	}

}
