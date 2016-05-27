<?php

/**
 * Part of Aliro SEF Manager - see root index.php for copyright etc.
 *
 */

class sefAdminUri extends sefAdminControllers {
	protected static $instance = __CLASS__;

	protected $session_var = 'alirodoc_classid';
	protected $view_class = 'listUriHTML';

	protected $cid = array();
	protected $uri = '';
	public $filters = null;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	public function getRequestData () {
		$this->cid = $this->getParam($_POST, 'cid', array());
		$this->uri = $this->getParam($_REQUEST, 'uri');
	}

	public static function taskTranslator () {
		return array (
		'panel' => T_('SEF CP'),
		'cancel' => T_('Cancel'),
		'metadata' => T_('Metadata'),
		'save' => T_('Save metadata'),
		'remove' => T_('Delete')
		);
	}
	
	public function toolbar () {
	    if ('metadata' == $this->task) {
			$this->toolBarButton('save');
			$this->toolBarButton('cancel');
		}
	    else {
			$this->toolBarButton('metadata', true);
			$this->toolBarButton('remove', true);
			$this->toolBarButton('panel');
		}
	}
	
	public function cancelTask () {
		$this->redirect('index.php?core=cor_sef&act=uri');
	}

	public function panelTask () {
		$this->redirect('index.php?core=cor_sef');
	}

	public function listTask () {
		$this->getListParams();
		$query = "SELECT COUNT(*) FROM #__remosef_uri";
		if ($this->filters['origuri']) $where[] = "uri LIKE '%{$this->filters['origuri']}%'";
		if ($this->filters['sefuri']) $where[] = "sef LIKE '%{$this->filters['sefuri']}%'";
		if (isset($where)) $query .= ' WHERE '.implode(' AND ', $where);
		$this->database->setQuery($query);
		$total = $this->database->loadResult();
	    $this->makePageNav($total);
		$query = "SELECT * FROM #__remosef_uri";
		if (isset($where)) $query .= ' WHERE '.implode(' AND ', $where);
		$query .= " ORDER BY refreshed DESC LIMIT {$this->pageNav->limitstart}, {$this->pageNav->limit}";
		$uris = $this->database->doSQLget($query);
		$view = new sefAdminHTML($this);
		$view->listuris($uris, $this->pageNav, $this);
	}
	
	private function getListParams () {
		$this->filters['sefuri'] = $this->database->getEscaped($this->getParam($_REQUEST, 'sefuri'));
		$this->filters['origuri'] = $this->database->getEscaped($this->getParam($_REQUEST, 'origuri'));
	}
	
	public function removeTask () {
		foreach ($this->cid as &$selected) $selected = intval($selected);
		$slist = implode(',', $this->cid);
		if ($slist) $this->database->doSQL("DELETE FROM #__remosef_uri WHERE id IN ($slist)");
		aliroSEF::getInstance()->clearCache();
		$this->redirect('index.php?core=cor_sef&act=uri', T_('Deletion completed'));
	}
	
	public function metadataTask () {
		if (0 == $this->currid) $this->redirect('index.php?core=cor_sef&act=uri', T_('No URI was selected for setting metadata'));
		$metadata = new sefMetaDataItem();
		$metadata->load($this->currid);
		$this->database->setQuery("SELECT uri FROM #__remosef_uri WHERE id = $this->currid");
		$metadata->uri = $this->database->loadResult();
		$view = new sefAdminHTML ($this);
		$view->editMetaData('uri', $this->currid, $metadata, $this);
	}
	
	public function saveTask () {
		// save metadata
		$metadata = new sefMetaDataItem();
		$metadata->load($this->idparm);
		$metadata->bind($_POST);
		$metadata->storeNonAuto();
		aliroSEF::getInstance()->clearCache();
		$this->redirect('index.php?core=cor_sef&act=uri', T_('Metadata saved'));
	}
	
}
