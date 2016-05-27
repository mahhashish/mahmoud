<?php
/**
*
* @copyright (C) 2005 - 2007 Martin Brampton (martin@remository.com)
* Web site: http://remository.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* This code is newly written by Martin Brampton, and is part of Aliro
*
*/

class sefMetaDataItem extends aliroDatabaseRow {
	protected $DBclass = 'aliroDatabase';
	protected $tableName = '#__remosef_metadata';
	protected $rowKey = 'id';
}

class sefAdminControllers extends aliroComponentAdminControllers {
	protected $database = null;
	
	protected $underscore = 0;
	protected $enabled = 1;
	protected $strip_chars = '';
	protected $lower_case = '0';
	protected $unique_id = '0';
	protected $use_cache = '0';
	protected $apache_rewrite = '0';
	protected $cache_time = '600';
	protected $buffer_size = '100';
	protected $log_transform = '0';
	protected $default_robots = 'index, follow';
	protected $home_title = 'Home';
	protected $title_separator = '|';
	
	protected $custom_code = array();
	protected $custom_name = array();
	protected $custom_retrieve = array();
	protected $sef_content_task = array();
	protected $sef_name_chars = array();
	protected $sef_translate_chars = array();
	protected $component_details = array();
	protected $component_dbuse = array();
	protected $sef_substitutions_exact = array();
	protected $sef_substitutions_exact_name = array();
	protected $sef_substitutions_exact_mod = array();
	protected $sef_substitutions_in = array();
	protected $sef_substitutions_out = array();
	protected $filters = array();
	protected $limit = 20;
	protected $limitstart = 0;
	
	public $ignoreMagicQuotes = 1;
	
	protected function __construct () {
		parent::__construct();
		$this->database = aliroDatabase::getInstance();
	}

	public static function taskTranslator () {
		return array (
		'save' => T_('Save'),
		'config' => T_('Basic Config'),
		'transform' => T_('Transformations'),
		'listmeta' => T_('List Metadata'),
		'listuri' => T_('List URIs'),
		'list' => T_('Cancel'),
		'savecomponent' => T_('Save Codes')
		);
	}

	public function configTask () {
		$this->redirect('index.php?core=cor_sef&act=config');
	}
	
	public function transformTask () {
		$this->redirect('index.php?core=cor_sef&act=transform');
	}
	
	public function listuriTask () {
		$this->redirect('index.php?core=cor_sef&act=uri');
	}
	
	public function listmetaTask () {
		$this->redirect('index.php?core=cor_sef&act=metadata');
	}
}

class sefAdminSef extends sefAdminControllers {
	protected static $instance = null;
	
	protected $view_class = 'sefAdminHTML';
	
	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self($manager));
	}
	
	public function getRequestData () {
	}

	public function toolbar () {
		$task = $this->getParam($_REQUEST, 'task');
		$this->toolBarButton('config');
		$this->toolBarButton('transform');
		$this->toolBarButton('listuri');
		$this->toolBarButton('listmeta');
	}
	
	public function listTask () {
		$view = new $this->view_class($this);
		echo <<<END_FORM
			<div>
				{$view->sefNotes()}
				<input type="hidden" name="core" value="cor_sef" />
				<input type="hidden" id="task" name="task" value="" />
			</div>
			<!-- End of code from Aliro SEF admin -->

END_FORM;

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
		require_once(_ALIRO_ADMIN_CLASS_BASE.'/components/com_sef/admin.sef.html.php');
		$view = new $this->viewClass($this);
		$view->editMetaData($type, $id, $metadata, $this);
	}

	// Internal function
	function getMetaData ($type, $id) {
		if ('config' == $type) {
			$sql = "SELECT c.name AS uri, c.modified AS sef, m.id, m.htmltitle, m.robots, m.description, m.keywords FROM #__remosef_config AS c LEFT JOIN #__remosef_metadata AS m ON m.uri = c.name WHERE c.id = $id";
		}
		elseif ('listuri' == $type) {
			$sql = "SELECT u.uri, u.sef, m.id, m.htmltitle, m.robots, m.description, m.keywords FROM #__remosef_uri AS u LEFT JOIN #__remosef_metadata AS m ON CRC32(m.uri) = u.uri_crc AND m.uri = u.uri WHERE u.id = $id";
		}
		// Remaining alternative is 'listmeta'
		else $sql = "SELECT m.*, u.sef, c.modified FROM #__remosef_metadata AS m LEFT JOIN #__remosef_uri AS u ON CRC32(m.uri) = u.uri_crc AND m.uri = u.uri AND m.type = 'listuri'"
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
					$this->database->setQuery("INSERT INTO #__remosef_config (type, name, modified) VALUES ('$component', '$tag', '$mod')");
					$this->database->query();
					$this->component_details[$component][$tag] = $mod;
				}
			}
			$this->one_component($component);
		}
	}

}