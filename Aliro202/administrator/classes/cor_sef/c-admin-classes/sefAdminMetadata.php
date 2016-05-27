<?php

/**
 * Part of Aliro SEF Manager - see root index.php for copyright etc.
 *
 */

class sefAdminMetadata extends sefAdminControllers {
	protected static $instance = __CLASS__;

	protected $session_var = 'alirodoc_classid';
	protected $view_class = 'listMetadataHTML';

	protected $cid = array();
	protected $uri = '';
	public $filters = null;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	public function getRequestData () {
		$this->cid = $this->getParam($_POST, 'cid', array());
		// $this->uri = $this->getParam($_REQUEST, 'uri');
	}

	public static function taskTranslator () {
		return array (
		'panel' => T_('SEF CP'),
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
		}
		$this->toolBarButton('panel');
	}

	public function cancelTask () {
		$this->redirect('index.php?core=cor_sef&act=metadata');
	}
	
	public function panelTask () {
		$this->redirect('index.php?core=cor_sef');
	}

	public function listTask () {
		$this->getListParams();
		$query = "SELECT COUNT(*) FROM #__remosef_metadata AS m LEFT JOIN #__remosef_uri AS u ON CRC32(m.uri) = u.uri_crc AND m.uri = u.uri AND m.type = 'listuri'"
		." LEFT JOIN #__remosef_config AS c ON c.type = 'substitutions' AND m.uri = c.name";
		if ($this->filters['origuri']) $where[] = "m.uri LIKE '%{$this->filters['origuri']}%'";
		if ($this->filters['sefuri']) $where[] = "u.sef LIKE '%{$this->filters['sefuri']}%' OR c.modified LIKE '%{$this->filters['sefuri']}%'";
		if (isset($where)) $query .= ' WHERE '.implode(' AND ', $where);
		$this->database->setQuery($query);
		$total = $this->database->loadResult();
	    $this->makePageNav($total);
		$query = "SELECT m.id, m.type, m.htmltitle, m.robots, m.keywords, m.description, u.sef, u.uri, c.modified FROM #__remosef_metadata AS m LEFT JOIN #__remosef_uri AS u ON m.id = u.id"
		." LEFT JOIN #__remosef_config AS c ON c.type = 'substitutions' AND m.uri = c.name";
		if (isset($where)) $query .= ' WHERE '.implode(' AND ', $where);
		$query .= " ORDER BY u.sef LIMIT {$this->pageNav->limitstart}, {$this->pageNav->limit}";
		$this->database->setQuery($query);
		$metas = $this->database->loadObjectList();
		$view = new sefAdminHTML($this);
		$view->listmeta($metas, $this->pageNav, $this);
	}

	private function getListParams () {
		$this->filters['sefuri'] = $this->database->getEscaped($this->getParam($_REQUEST, 'sefuri'));
		$this->filters['origuri'] = $this->database->getEscaped($this->getParam($_REQUEST, 'origuri'));
	}
	
	public function metadataTask () {
		if (0 == $this->currid) $this->redirect('index.php?core=cor_sef&act=metadata', T_('No Item was selected for updating metadata'));
		$metadata = new sefMetaDataItem();
		$metadata->load($this->currid);
		$this->database->setQuery("SELECT uri FROM #__remosef_uri WHERE id = $this->currid");
		$metadata->uri = $this->database->loadResult();
		$view = new sefAdminHTML ($this);
		$view->editMetaData('metadata', $this->currid, $metadata, $this);
	}
	
	public function saveTask () {
		$metadata = new sefMetaDataItem();
		$metadata->load($this->currid);
		$metadata->bind($_POST);
		$metadata->storeNonAuto();
		aliroSEF::getInstance()->clearCache();
		$this->redirect('index.php?core=cor_sef&act=metadata');
	}
}