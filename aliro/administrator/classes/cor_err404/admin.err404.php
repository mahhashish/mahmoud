<?php

class err404AdminErr404 extends aliroComponentAdminControllers {

	protected static $instance = null;

	protected $session_var = 'alirodoc_classid';
	protected $view_class = 'listErr404HTML';

	protected $errorid = 0;
	protected $cid = array();

	public static function getInstance ($manager) {
		if (self::$instance == null) self::$instance = new err404AdminErr404 ($manager);
		return self::$instance;
	}

	public function getRequestData () {
		$this->cid = $this->getParam($_POST, 'cid', array());
		$this->uri = $this->getParam($_REQUEST, 'uri');
		if (!$this->errorid) $this->errorid = isset($this->cid[0]) ? $this->cid[0] : $this->uri;
	}


	public function toolbar () {
	    $menubar = aliroAdminToolbar::getInstance();
	    if ('edit' == $this->task) $menubar->cancel();
	    else {
			$menubar->editList();
			$menubar->deleteList();
		}
	}

	public function listTask () {
	    $database = aliroCoreDatabase::getInstance();
	    $database->setQuery("SELECT COUNT(*) FROM #__error_404");
	    $total = $database->loadResult();
	    $this->makePageNav($total);
	    $link = $this->getCfg('admin_site').'/index.php?core=cor_err404&amp;task=edit&amp;uri=';
	    $database->setQuery("SELECT REPLACE(REPLACE(uri,'&amp;','&'),'&','&amp;') AS eluri, CONCAT('$link',REPLACE(REPLACE(uri,'&amp;','&'),'&','&amp;')) AS details, timestamp FROM #__error_404 ORDER BY timestamp DESC LIMIT {$this->pageNav->limitstart}, {$this->pageNav->limit}");
	    $errors = $database->loadObjectList();
		$view = new $this->view_class($this);
		if ($errors) $view->showErrors($errors);
		else $view->showErrors(array());
	}

	public function editTask () {
		if ($this->errorid) {
			$database = aliroCoreDatabase::getInstance();
			$database->setQuery("SELECT *, REPLACE(REPLACE(referer,'&amp;','&'),'&','&amp;') AS showreferer FROM #__error_404 WHERE uri = '$this->errorid'");
			$database->loadObject($error);
			$postdata = unserialize(base64_decode($error->post));
			$error->showpost = $this->displayArray($postdata);
			$view = new $this->view_class($this);
			$view->showDetailedError ($error);
		}
		else $this->redirect('index.php?core=cor_err404', T_('Please select an item for detailed display'), _ALIRO_ERROR_WARN);
	}

	private function displayArray ($arr, $depth=0) {
		$result = '';
		foreach ($arr as $key=>$value) {
			if (is_array($value)) $result .= "[$key] = ".$this->displayArray($value, $depth+1);
			else {
				for ($i = 0; $i < $depth; $i++) $result .= "\t";
				$result .= "[$key] = $value\n";
			}
		}
		return $result;
	}

	public function removeTask () {
		if (count($this->cid)) {
			$database = aliroCoreDatabase::getInstance();
			foreach ($this->cid as &$item) $item = $database->getEscaped($item);
			$idlist = implode("','", $this->cid);
			$database->doSQL("DELETE FROM #__error_404 WHERE uri IN ('$idlist')");
			$this->redirect('index.php?core=cor_err404');
		}
		else $this->redirect('index.php?core=cor_err404', T_('Please select an item for detailed display'), _ALIRO_ERROR_WARN);
	}

	public function cancelTask () {
		$this->redirect('index.php?core=cor_err404');
	}

}

?>