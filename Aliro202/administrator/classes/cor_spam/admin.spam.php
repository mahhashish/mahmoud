<?php

class spamAdminSpam extends aliroComponentAdminControllers {

	protected static $instance = null;

	// protected $session_var = 'alirodoc_classid';
	protected $view_class = 'listSpamHTML';

	protected $errorid = 0;
	protected $cid = array();

	public static function getInstance ($manager) {
		return self::$instance instanceof self ? self::$instance : self::$instance = new self ($manager);
	}

	public function getRequestData () {
	}

	public static function taskTranslator () {
		return array (
		'cancel' => T_('Cancel'),
		'edit' => T_('Show Details'),
		'remove' => T_('Delete'),
		'empty' => T_('Delete All'),
		'spam' => T_('Mark SPAM'),
		'ham' => T_('Mark HAM')
		);
	}

	public function toolbar () {
	    if ('edit' == $this->task) {
			$this->toolBarButton('cancel');
			$this->toolBarButton('spam');
			$this->toolBarButton('ham');
		}
	    else {
			$this->toolBarButton('edit', true);
			$this->toolBarButton('remove', true);
			$this->toolBarButton('empty', false);
			$this->toolBarButton('spam');
			$this->toolBarButton('ham');
		}
	}

	public function listTask () {
	    $database = aliroCoreDatabase::getInstance();
	    $database->setQuery("SELECT COUNT(*) FROM #__spam_log");
	    $total = $database->loadResult();
	    $this->makePageNav($total);
	    $link = $this->getCfg('admin_site').'/index.php?core=cor_spam&amp;task=edit&amp;id=';
	    $checks = $database->doSQLget("SELECT * FROM #__spam_log ORDER BY articledate DESC LIMIT {$this->pageNav->limitstart}, {$this->pageNav->limit}");
		foreach ($checks as $i=>$check) $checks[$i]->details = $link.$check->id;
		$view = new $this->view_class($this);
		$view->showSpamChecks($checks);
	}

	public function editTask () {
		if ($this->currid) {
			$database = aliroCoreDatabase::getInstance();
			$database->setQuery("SELECT * FROM #__spam_log WHERE id = $this->currid");
			$database->loadObject($check);
			$results = $database->doSQLget("SELECT * FROM #__spam_log_results WHERE spamid = $this->currid");
			$view = new $this->view_class($this);
			$view->showDetailedSpamLog ($check, $results);
		}
		else $this->redirect('index.php?core=cor_spam', T_('Please select an item for detailed display'), _ALIRO_ERROR_WARN);
	}

	public function spamTask () {
		foreach ($this->cid as $spamid) aliroSpamHandler::getInstance()->changeSpamStatus($spamid, 'spam');
		$this->listTask();
	}

	public function hamTask () {
		foreach ($this->cid as $spamid) aliroSpamHandler::getInstance()->changeSpamStatus($spamid, 'ham');
		$this->listTask();
	}

	public function removeTask () {
		if (count($this->cid)) {
			$database = aliroCoreDatabase::getInstance();
			foreach ($this->cid as &$item) $item = $database->getEscaped($item);
			$idlist = implode(",", $this->cid);
			$database->doSQL("DELETE FROM #__spam_log WHERE id IN ($idlist)");
			$this->redirect('index.php?core=cor_spam');
		}
		else $this->redirect('index.php?core=cor_spam', T_('Please select an item for detailed display'), _ALIRO_ERROR_WARN);
	}

	public function emptyTask () {
		$this->setCurrentSelection(array());
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL("TRUNCATE TABLE #__spam_log");
		$this->redirect('index.php?core=cor_spam');
	}

	public function cancelTask () {
		$this->redirect('index.php?core=cor_spam');
	}

}