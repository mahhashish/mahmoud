<?php
/**
*
* @copyright (C) 2005 - 2007 Martin Brampton (martin@remository.com)
* Web site: http://remository.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* This code is newly written by Martin Brampton, and it interfaces to Joomla
*
*/

class sefAdminControllers extends aliroComponentAdminControllers {
	protected $database = null;
	protected $underscore = 0;
	protected $enabled = 1;
	protected $strip_chars = '';
	protected $lower_case = '0';
	protected $unique_id = '0';
	protected $use_cache = '0';
	protected $cache_time = '600';
	protected $buffer_size = '100';
	protected $log_transform = '0';
	protected $default_robots = 'index, follow';
	protected $home_title = 'Home';
	protected $title_separator = '|';
	protected $custom_code = array();
	protected $cutom_name = array();
	protected $sef_content_task = array();
	protected $sef_name_chars = array();
	protected $sef_translate_chars = array();
	protected $component_details = array();
	protected $sef_substitutions_exact = array();
	protected $sef_substitutions_exact_name = array();
	protected $sef_substitutions_exact_mod = array();
	protected $sef_substitutions_in = array();
	protected $sef_substitutions_out = array();
	protected $filters = array();
	protected $limit = 20;
	protected $limitstart = 0;
	
	public $ignoreMagicQuotes = 1;
	
	protected function __construct ($manager) {
		parent::__construct($manager);
		$this->database = aliroDatabase::getInstance();
	}

}

class sefAdminSef extends sefAdminControllers {
	protected static $instance = __CLASS__;
	
	protected $view_class = 'sefAdminHTML';
	
	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}
	
	public function getRequestData () {
	}

	public static function taskTranslator () {
		return array (
		'save' => T_('Save'),
		'listmeta' => T_('List Metadata'),
		'listuri' => T_('List URIs'),
		'list' => T_('Cancel'),
		'savecomponent' => T_('Save Codes')
		);
	}

	public function toolbar () {
		$task = $this->getParam($_REQUEST, 'task');
		if ('component' == $task) {
			$this->toolBarButton('savecomponent');
			$this->toolBarButton('list');
		}
		else {
			$this->toolBarButton('save');
			$this->toolBarButton('listuri');
			$this->toolBarButton('listmeta');
		}
	}
	
	public function listuriTask () {
		$this->redirect('index.php?core=cor_sef&act=uri');
	}
	
	public function listmetaTask () {
		$this->redirect('index.php?core=cor_sef&act=metadata');
	}

	public function listTask () {
		$view = new $this->view_class($this);
		echo $view->sefNotes();
		$this->getData();
		echo "\n<div id='remosefadmin'>";
		$this->showOptions();
		$this->showSubstitutions();
		$this->showSubstitutionsIn();
		$this->showSubstitutionsOut();
		$this->showCharacters();
		$this->showComponents();
		$this->showContentTasks();
		?>
		<input type="hidden" name="core" value="cor_sef" />
		<?php
		echo "\n</div>";
		echo "\n\t</form>";
		echo "\n<!-- End of code from Remosef -->";
	}

	private function getData () {
		$configs = $this->database->doSQLget ("SELECT * FROM #__remosef_config");
		$vars = get_object_vars($this);
		foreach ($configs as $item) {
			if ('options' == $item->type) {
				$name = $item->name;
				if (isset($vars[$name]) AND !is_array($this->$name)) $this->$name = $item->modified;
			}
			elseif ('components' == $item->type) {
				$this->custom_code[$item->name] = $item->modified;
			}
			elseif ('characters' == $item->type) {
				$this->sef_name_chars[] = $item->name;
				$this->sef_translate_chars[] = $item->modified;
			}
			elseif ('substitutions' == $item->type) {
				$this->sef_substitutions_exact_name[$item->id] = $item->name;
				$this->sef_substitutions_exact_mod[$item->id] = $item->modified;
			}
			elseif ('substitutions_in' == $item->type) {
				$this->sef_substitutions_in[$item->name] = $item->modified;
			}
			elseif ('substitutions_out' == $item->type) {
				$this->sef_substitutions_out[$item->name] = $item->modified;
			}
			elseif ('content' == $item->type) $this->sef_content_task[$item->name] = $item->modified;
			else $this->component_details[$item->type][$item->name] = $item->modified;
		}
		unset($configs);
	}

	private function showOptions () {
		$this->headingLine('Remosef Options');
		$this->yesnoBox('Enable SEF', 'enabled', $this->enabled);
		$this->inputBox('Count of URI buffer entries', 'buffer_size', $this->buffer_size);
		$this->yesnoBox('Use Joomla cache (if enabled)', 'use_cache', $this->use_cache);
		$this->inputBox('Cache time in seconds', 'cache_time', $this->cache_time);
		$this->inputBox('Characters to be removed, separated by |', 'strip_chars', htmlspecialchars($this->strip_chars), 60);
		$this->yesnoBox('Make all URIs lowercase', 'lower_case', $this->lower_case);
		$this->yesnoBox('Force unique ID number in URIs', 'unique_id', $this->unique_id);
		$this->yesnoBox('Redirect underscore URLs to hyphen', 'underscore', $this->underscore);
		$this->yesnoBox('Log incoming transformations', 'log_transform', $this->log_transform);
		$this->inputBox('Home page title', 'home_title', $this->home_title);
		$this->inputBox('Robots default meta data', 'default_robots', $this->default_robots);
		$this->inputBox('Title separator (from site name)', 'title_separator', $this->title_separator);
	}

	private function showSubstitutions () {
		$link = $this->getCfg('live_site').'/administrator/index2.php?option=com_sef&amp;act=config&amp;task=metadata&amp;cid=';
		$this->headingLine('URI Substitutions - Exact');
		echo "\n\t<p>The left box should be the exact standard CMS URI (e.g. /index.php?option=com_content&amp;task=view&amp;id=21&amp;Itemid=93), the right box what it is to be translated to (e.g. /about/john-smith/).  The translation will be exact and will be applied immediately a URI is received.";
		foreach ($this->sef_substitutions_exact_name as $id=>$name) {
			$linkmeta = <<<LINK_META

			<a href="$link$id">Edit metadata</a>

LINK_META;
			$this->translateLine('subst', $name, $this->sef_substitutions_exact_mod[$id], 50, $linkmeta);
		}
		for ($i = 0; $i < 5; $i++) $this->translateLine('subst', '', '',50);
	}

	private function showSubstitutionsIn () {
		$this->headingLine('URI Substitutions - Inbound');
		echo "\n\t<p>The left box must be a valid regular expression that is to be applied to an incoming URI.  The right box must be what it is to be translated to.  The substitution will be applied immediately on receipt of a URI unless the URI has an exact match in the table above.</p>";
		echo "\n\t<p>This is designed to handle transformation of old URIs and care must be taken to avoid disrupting the normal decoding process.";
		echo "\n\t<p>Remember that regular expressions use metacharacters such as * or ? or ].  To use them as ordinary characters, they must be escaped with backslash.";
		foreach ($this->sef_substitutions_in as $key=>$name) $this->translateLine('subst_in', $key, $name, 50);
		for ($i = 0; $i < 5; $i++) $this->translateLine('subst_in', '', '',50);
	}
	
	private function showSubstitutionsOut () {
		$this->headingLine('URI Substitutions - Outbound');
		echo "\n\t<p>The left box must be a valid regular expression.  The right hand box will be substituted for it.  The substitution will be applied after all other outgoing URI processing.";
		echo "\n\t<p>Remember that regular expressions use metacharacters such as * or ? or ].  To use them as ordinary characters, they must be escaped with backslash.";
		foreach ($this->sef_substitutions_out as $key=>$name) $this->translateLine('subst_out', $key, $name, 50);
		for ($i = 0; $i < 5; $i++) $this->translateLine('subst_out', '', '',50);
	}
	
	private function showComponents () {
		$link = 'index.php?core=cor_sef&amp;task=component&amp;component=';
		$this->headingLine('Component names');
		echo "\n\t<p>If a component is listed here, its sef_ext.php (if any) will be used; the name will be translated in any case.  Make sure the translated name does not conflict with content component tasks.  Editing details depends on the sef_ext file being present and supporting the extended Remosef interface.</p>";
		foreach ($this->custom_code as $key=>$name) {
			$linkhtml = <<<COMP_LINK

			<a href="$link$key">Edit details</a>

COMP_LINK;

			$this->translateLine('comp', $key, $name, 30, $linkhtml);
		}
		for ($i = 0; $i < 5; $i++) $this->translateLine('comp', '', '', 30, '<span>Edit details</span>');
	}

	private function showCharacters () {
		$this->headingLine('Character string translations');
		echo "\n\t<p>The box on the left must be a string of one or more characters; so must the box on the right.  Any occurrences of left hand strings will be substituted by the corresponding right string.  Character string translations are applied to names used to build the SEF URI.</p>";
		echo "\n\t<p>The dash (often created by MS Word) is always translated to hyphen, and single quote is always translated to hyphen</p>";
		echo "\n\t<p>Accents are automatically removed for accented characters in the Latin1 set; URL encoding is used if necessary</p>";
		foreach ($this->sef_name_chars as $key=>$name) $this->translateLine ('char', $name, $this->sef_translate_chars[$key]);
		for ($i = 0; $i < 5; $i++) $this->translateLine('char', '', '');
	}

	private function showContentTasks () {
		$content_tasks = array ('findkey',
		'view',
		'section',
		'category',
		'blogsection',
		'blogcategorymulti',
		'blogcategory',
		'archivesection',
		'archivecategory',
		'save',
		'cancel',
		'emailform',
		'emailsend',
		'vote',
		'showblogsection'
		);
		$this->headingLine('Content task translations');
		echo "\n\t<p>You can give alternative names for the content component task words.  Make sure that they do not conflict with component names.</p>";
		foreach ($this->sef_content_task as $key=>$name) $this->translateLine ('ctask', $key, $name);
		foreach ($content_tasks as $task) if (!isset($this->sef_content_task[$task])) $this->translateLine ('ctask', $task, '');
		for ($i = 0; $i < 3; $i++) $this->translateLine('ctask', '', '');
	}

	private function headingLine ($title) {
		echo <<<HEADING

		<div class="remosefhead">$title</div>

HEADING;

	}

	private function inputBox ($title, $name, $value, $width=25) {
		echo <<<INPUT_BOX

			<div class="remosefinput">
				<label for="$name">$title</label>
				<input class="inputbox" type="text" id="$name" name="$name" size="$width" value="$value" />
			</div>

INPUT_BOX;

	}

	private function yesnoBox ($title, $name, $value) {
		$no = $yes = '';
		if ($value) $yes = "selected='selected'";
		else $no = "selected='selected'";
		echo <<<YES_NO

			<div class="remosefinput">
				<label for="$name">$title</label>
				<select id="$name" name="$name">
					<option value="0" $no>No</option>
					<option value="1" $yes>Yes</option>
				</select>
			</div>

YES_NO;

	}

	private function translateLine ($type, $name, $modified, $size=30, $link='') {
		echo <<<TRANSLATE_LINE

		<div class="remosefboxes">
			<input name="{$type}[]" value="$name" class="inputbox" size="$size"" />
			<input name="{$type}mod[]" value="$modified" class="inputbox" size="$size" />
			$link
		</div>

TRANSLATE_LINE;

	}
	
	public function saveTask () {
		$this->database->doSQL("DELETE FROM #__remosef_config WHERE type IN ('components', 'options', 'characters', 'content', 'substitutions', 'substitutions_in', 'substitutions_out')");
		$names = array_keys(get_object_vars($this));
		foreach ($names as $name) {
			if (is_array($this->$name) OR 'database' == $name) continue;
			$value = $this->getParam($_POST, $name);
			if (get_magic_quotes_gpc()) $value = stripslashes($value);
			if (null !== $value) {
				if ('strip_chars' == $name) $value = htmlspecialchars_decode ($value);
				if (is_numeric($this->$name)) $value = intval($value);
				else $value = $this->database->getEscaped($value);
				$this->$name = $value;
			}
			$this->database->setQuery("INSERT INTO #__remosef_config VALUES (0, 'options', '$name', '{$this->$name}')");
			$this->database->query();
		}
		$this->custom_code = $this->custom_name = array();
		$this->storeDataGroup ('components','comp','compmod');
		$this->sef_name_chars = $this->sef_translate_chars = array();
		$this->storeDataGroup ('characters', 'char', 'charmod');
		$this->sef_content_task = array();
		$this->storeDataGroup ('content', 'ctask', 'ctaskmod');
		$this->sef_substitutions_exact = array();
		$this->storeDataGroup ('substitutions', 'subst', 'substmod');
		$this->sef_substitutions_in = array();
		$this->storeDataGroup ('substitutions_in', 'subst_in', 'subst_inmod');
		$this->sef_substitutions_out = array();
		$this->storeDataGroup ('substitutions_out', 'subst_out', 'subst_outmod');
		aliroSEF::getInstance()->clearCache();
		$this->redirect('index.php?core=cor_sef', T_('SEF configuration saved'));
	}
	
	private function storeDataGroup ($type, $namecode, $modcode) {
		$codes = $this->getParam($_POST, $namecode, array());
		$names = $this->getParam($_POST, $modcode, array());

		foreach ($codes as $key=>$code) {
			// Probably better not to strip magic quotes - also removes deliberate escaping of regex characters
			// Should not be quotes in a URI
			/*
			if (get_magic_quotes_gpc()) {
				$code = stripslashes($code);
				if (!empty($names[$key])) $names[$key] = stripslashes($names[$key]);
			}
			*/
			$code = $this->database->getEscaped($code);
			$name = !empty($names[$key]) ? $this->database->getEscaped($names[$key]) : '';
			if ($name AND $code) $this->database->doSQL("INSERT INTO #__remosef_config VALUES (0, '$type', '$code', '$name')");
		}
	}
	
	public function componentTask () {
		$this->getData();
		$component = $this->getParam($_REQUEST, 'component');
		if (false !== strpos($component, '..')) die ('Illegal component specified');
		echo "\n\t\t<h3>Function codes for $component</h3>";
		$sefext = _ALIRO_ABSOLUTE_PATH.'/components/'.$component.'/sef_ext.php';
		if (file_exists($sefext)) {
			require_once($sefext);
			$class_name = str_replace('com', 'sef', $task);
			if (class_exists($class_name, false)) {
				if (method_exists($class_name, 'getInstance')) {
					$sef = call_user_func(array($class_name, 'getInstance'));
					if (method_exists($sef,'tags')) $tags = $sef->tags();
				}
				else {
					if (method_exists($class_name, 'tags')) $tags = call_user_func(array($class_name, 'tags'));
				}
			}
		}
		echo "\n<div id='remosefadmin'>";
		if (isset($tags)) {
			foreach ($tags as $tag) {
				if (isset($this->component_details[$task][$tag])) $translated = $this->component_details[$task][$tag];
				else $translated = '';
				$this->translateLine ($task, $tag, $translated);
			}
		}
		else echo "<p>No function codes found</p>";
		echo <<<END_FORM
				<input type="hidden" name="core" value="cor_sef" />
				<input type="hidden" name="component" value="$component" />
			</div>
			</form>
			<!-- End of code from Remosef -->

END_FORM;

	}
	
}

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
		'config' => T_('Configure'),
		'metadata' => T_('Metadata'),
		'save' => T_('Save metadata'),
		'remove' => T_('Delete'),
		'list' => T_('Cancel')
		);
	}
	public function toolbar () {
	    if ('metadata' == $this->task) {
			$this->toolBarButton('save');
			$this->toolBarButton('list');
		}
	    else {
			$this->toolBarButton('metadata');
			$this->toolBarButton('remove', true);
		}
		$this->toolBarButton('config');
	}
	
	public function configTask () {
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
		$view = new sefAdminHTML();
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
		$this->redirect('index.php?core=cor_sef&act=uri', T_('Deletion completed'));
	}
	
	public function metadataTask () {
	}
	
	public function saveTask () {
	// save metadata
	}
	
}

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
		'cancel' => T_('Cancel'),
		'metadata' => T_('Metadata'),
		'save' => T_('Save metadata'),
		'remove' => T_('Delete')
		);
	}
	public function toolbar () {
	    if ('metadata' == $this->task) $this->toolBarButton('save');
	    else {
			$this->toolBarButton('metadata');
			$this->toolBarButton('remove', true);
		}
		$this->toolBarButton('cancel');
	}
	
	public function listTask () {
		$this->getListParams();
		$query = "SELECT COUNT(*) FROM #__remosef_metadata AS m LEFT JOIN #__remosef_uri AS u ON m.uri = u.uri AND m.type = 'listuri'"
		." LEFT JOIN #__remosef_config AS c ON c.type = 'substitutions' AND m.uri = c.name";
		if ($this->filters['origuri']) $where[] = "m.uri LIKE '%{$this->filters['origuri']}%'";
		if ($this->filters['sefuri']) $where[] = "u.sef LIKE '%{$this->filters['sefuri']}%' OR c.modified LIKE '%{$this->filters['sefuri']}%'";
		if (isset($where)) $query .= ' WHERE '.implode(' AND ', $where);
		$this->database->setQuery($query);
		$total = $this->database->loadResult();
	    $this->makePageNav($total);
		$query = "SELECT m.*, u.sef, c.modified FROM #__remosef_metadata AS m LEFT JOIN #__remosef_uri AS u ON m.uri = u.uri AND m.type = 'listuri'"
		." LEFT JOIN #__remosef_config AS c ON c.type = 'substitutions' AND m.uri = c.name";
		if (isset($where)) $query .= ' WHERE '.implode(' AND ', $where);
		$query .= " ORDER BY u.sef LIMIT {$this->pageNav->limitstart}, {$this->pageNav->limit}";
		$this->database->setQuery($query);
		$metas = $this->database->loadObjectList();
		$view = new sefAdminHTML();
		$view->listmeta($metas, $this->pageNav, $this);
	}

	private function getListParams () {
		$this->filters['sefuri'] = $this->database->getEscaped($this->getParam($_REQUEST, 'sefuri'));
		$this->filters['origuri'] = $this->database->getEscaped($this->getParam($_REQUEST, 'origuri'));
	}
}

class dummy {

	public function editTask () {
		if ($this->errorid) {
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
		$this->redirect('index.php?core=cor_sef');
	}

	function sefAdminOldStuff () {
		global $database, $sefnotes;
		$this->database = $database;
		$act = mosGetParam($_REQUEST, 'act');
		$task = mosGetParam($_REQUEST, 'task');
		$this->cid = (array) mosGetParam($_REQUEST, 'cid', array());
		if ('metasave' == $task) $this->saveMetaData($act);
		if ('page404' == $task OR ('page404' == $act AND 'cancel' != $task)) {
			if ('remove' == $task) $this->delete404();
			$this->list404();
			return;
		}
		elseif ('listuri' == $task OR ('listuri' == $act AND 'cancel' != $task)) {
			if ('metadata' == $task AND 1 == count($this->cid)) {
				$this->editMetaData('listuri');
				return;
			}
			if ('remove' == $task) $this->deleteuri();
			$this->listuri();
			return;
		}
		elseif ('listmeta' == $task OR ('listmeta' == $act AND 'cancel' != $task)) {
			if ('metadata' == $task AND 1 == count($this->cid)) {
				$this->editMetaData('listmeta');
				return;
			}
			if ('remove' == $task) $this->deletemeta();
			$this->listmeta();
			return;
		}
		elseif ('components' == $act AND 'cancel' != $task) {
			$this->getData();
			if ($task) {
				if ('save' == $task) $this->saveComponentFuncs ();
				else $this->one_component ($task);
			}
			return;
		}
		if ('metadata' == $task AND 1 == count($this->cid)) {
			$type = mosGetParam($_REQUEST, 'type', 'config');
			$this->editMetaData($type);
			return;
		}
		if ('save' ==  $task) $this->storeData();
	}

	function addCustomHeadTag ($tag) {
		global $mainframe;
		$mainframe->addCustomHeadTag ($tag);
	}

	function getUserStateFromRequest ($array, $name, $default) {
		global $mainframe;
		return $mainframe->getUserStateFromRequest ($array, $name, $default);
	}

	function getData () {
		$configs = $this->database->doSQLget ("SELECT * FROM #__remosef_config");
		$vars = get_object_vars($this);
		foreach ($configs as $item) {
			if ('options' == $item->type) {
				$name = $item->name;
				if (isset($vars[$name]) AND !is_array($this->$name)) $this->$name = $item->modified;
			}
			elseif ('components' == $item->type) {
				$this->custom_code[$item->name] = $item->modified;
			}
			elseif ('characters' == $item->type) {
				$this->sef_name_chars[] = $item->name;
				$this->sef_translate_chars[] = $item->modified;
			}
			elseif ('substitutions' == $item->type) {
				$this->sef_substitutions_exact_name[$item->id] = $item->name;
				$this->sef_substitutions_exact_mod[$item->id] = $item->modified;
			}
			elseif ('substitutions_in' == $item->type) {
				$this->sef_substitutions_in[$item->name] = $item->modified;
			}
			elseif ('substitutions_out' == $item->type) {
				$this->sef_substitutions_out[$item->name] = $item->modified;
			}
			elseif ('content' == $item->type) $this->sef_content_task[$item->name] = $item->modified;
			else $this->component_details[$item->type][$item->name] = $item->modified;
		}
		unset($configs);
	}


	function saveMetaData ($act) {
		$type = ('listmeta' == $act) ? mosGetParam ($_REQUEST, 'metatype', 'config') : $act;
		$id = intval(mosGetParam ($_POST, 'id', 0));
		$metadata = $this->getMetaData($act, $id);
		$setters[] = "type = '$type'";
		$values[] = $metadata->uri;
		$values[] = $type;
		$inames = 'uri, type';
		foreach (array('htmltitle','robots','description','keywords') as $fieldname) {
			$setters[] = $fieldname." = '".$this->database->getEscaped(mosGetParam($_POST, $fieldname))."'";
			$values[] = $this->database->getEscaped(mosGetParam($_POST, $fieldname));
			$inames .= ', '.$fieldname;
		}
		if ($metadata->id) $sql = "UPDATE #__remosef_metadata SET ".implode(', ', $setters)." WHERE id = $metadata->id";
		else $sql = "INSERT INTO #__remosef_metadata ($inames) VALUES('".implode("', '", $values)."')";
		$this->database->setQuery($sql);
		$this->database->query();
	}


	function translateLine ($type, $name, $modified, $size=30, $link='') {
		echo <<<TRANSLATE_LINE

		<div class="remosefboxes">
			<input name="{$type}[]" value="$name" class="inputbox" size="$size"" />
			<input name="{$type}mod[]" value="$modified" class="inputbox" size="$size" />
			$link
		</div>

TRANSLATE_LINE;

	}

	function remosefCSS () {
		$css = <<<REMOSEF_CSS

<style type="text/css" media="all">
.remosefhead {
	margin: 15px 0;
	padding: 6px 4px 2px 4px;
	height: 24px;
	background: url(templates/joomla_admin/images/background.jpg);
	background-repeat: repeat;
	font-size: 14px;
	font-weight: bold;
	color: #000;
}
.remosefinput {
	height: 24px;
	padding-top: 4px;
	border-bottom: 1px solid #DDD;
}
.remosefinput label {
	display: box;
	float: left;
	clear: left;
	text-align: right;
	width: 35%;
}
.remosefinput select, .remosefinput input {
	display: box;
	float: left;
	margin-left: 10px;
}
.remosefboxes{
	height: 24px;
}
</style>

REMOSEF_CSS;

		$this->addCustomHeadTag($css);
	}


	// private function
	function htmlspecialchars_decode_php4 ($str, $quote_style = ENT_COMPAT) {
	   return strtr($str, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
	}



	function deleteuri () {
		$sql = $sql = "DELETE FROM #__remosef_uri WHERE id IN (%s)";
		$this->deleteByCid ($sql);
	}

	function deletemeta () {
		$sql = $sql = "DELETE FROM #__remosef_metadata WHERE id IN (%s)";
		$this->deleteByCid ($sql);
	}

	function deleteByCid ($sql) {
		if (count($this->cid)) {
			foreach ($this->cid as $i=>$id) $this->cid[$i] = intval($id);
			$sql = sprintf($sql, implode(',', $this->cid));
			$this->database->setQuery($sql);
			$this->database->query();
		}
	}

	function editMetaData ($type) {
		$id = intval($this->cid[0]);
		$metadata = $this->getMetaData($type, $id);
		require_once($this->getCfg('absolute_path').'/administrator/components/com_sef/admin.sef.html.php');
		$view = new sefAdminHTML();
		$view->editMetaData($type, $id, $metadata, $this);
	}

	// Internal function
	function getMetaData ($type, $id) {
		if ('config' == $type) {
			$sql = "SELECT c.name AS uri, c.modified AS sef, m.id, m.htmltitle, m.robots, m.description, m.keywords FROM #__remosef_config AS c LEFT JOIN #__remosef_metadata AS m ON m.uri = c.name WHERE c.id = $id";
		}
		elseif ('listuri' == $type) {
			$sql = "SELECT u.uri, u.sef, m.id, m.htmltitle, m.robots, m.description, m.keywords FROM #__remosef_uri AS u LEFT JOIN #__remosef_metadata AS m ON m.uri = u.uri WHERE u.id = $id";
		}
		// Remaining alternative is 'listmeta'
		else $sql = "SELECT m.*, u.sef, c.modified FROM #__remosef_metadata AS m LEFT JOIN #__remosef_uri AS u ON m.uri = u.uri AND m.type = 'listuri'"
		." LEFT JOIN #__remosef_config AS c ON c.type = 'substitutions' AND m.uri = c.name WHERE m.id = $id";
		$this->database->setQuery($sql);
		$metadata = null;
		$this->database->loadObject($metadata);
		return $metadata;
	}

	function one_component ($task) {
	}

	function saveComponentFuncs () {
		mosCache::cleanCache('aliroSEF');
		$component = mosGetParam($_POST, 'component');
		if (0 === strpos($component,'com_')) {
			$this->database->setQuery("DELETE FROM #__remosef_config WHERE type = '$component'");
			$this->database->query();
			$this->component_details = array();
			$component = $this->database->getEscaped($component);
			$tags = mosGetParam($_POST, $component, array());
			$modified = mosGetParam($_POST, $component.'mod', array());
			foreach ($tags as $key=>$tag) {
				if (!empty($modified[$key])) {
					$tag = $this->database->getEscaped($tag);
					$mod = $this->database->getEscaped($modified[$key]);
					$this->database->setQuery("INSERT INTO #__remosef_config VALUES (0, '$component', '$tag', '$mod')");
					$this->database->query();
					$this->component_details[$component][$tag] = $mod;
				}
			}
			$this->one_component($component);
		}
	}

}