<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Aliro is open source software, free to use, and licensed under GPL.
 * You can find the full licence at http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * The author freely draws attention to the fact that Aliro derives from Mambo,
 * software that is controlled by the Mambo Foundation.  However, this section
 * of code is totally new.  If it should contain any fragments that are similar
 * to Mambo, please bear in mind (1) there are only so many ways to do things
 * and (2) the author of Aliro is also the author and copyright owner for large
 * parts of Mambo 4.6.
 *
 * Tribute should be paid to all the developers who took Mambo to the stage
 * it had reached at the time Aliro was created.  It is a feature rich system
 * that contains a good deal of innovation.
 *
 * Your attention is also drawn to the fact that Aliro relies on other items of
 * open source software, which is very much in the spirit of open source.  Aliro
 * wishes to give credit to those items of code.  Please refer to
 * http://aliro.org/credits for details.  The credits are not included within
 * the Aliro package simply to avoid providing a marker that allows hackers to
 * identify the system.
 *
 * Copyright in this code is strictly reserved by its author, Martin Brampton.
 * If it seems appropriate, the copyright will be vested in the Aliro Organisation
 * at a suitable time.
 *
 * Copyright (c) 2007 Martin Brampton
 *
 * http://aliro.org
 *
 * counterpoint@aliro.org
 *
 * These classes provide basic management functions for the admin side of components
 *
 */

/**
* Component base logic for admin side
* Can be used on its own, or can be subclassed
*/

class aliroComponentAdminManager extends aliroComponentManager {
	public $act = '';
	public $task = '';
	protected $name = '';
	protected $controller = null;

	public function __construct ($component, $system, $version) {
		parent::__construct($component, $system, $version);
		$this->act = $this->getParam ($_REQUEST, 'act', $this->barename);
		$this->task = $this->getParam($_REQUEST, 'task');
		if ('undefined' == $this->task) $this->task = '';
		$this->name = $this->getAction();
		if (aliro::getInstance()->classExists($this->name) AND method_exists($this->name, 'getInstance')) {
			if (!$this->task) {
				$this->task = $this->getParam($_REQUEST, 'toolbarbutton');
				$this->task = $this->unTranslateTask($this->task);
			}
			if (!$this->task) $this->task = 'list';
			$this->setTask($this->task);
			$this->controller = call_user_func(array($this->name, 'getInstance'), $this);
		}
		else trigger_error(sprintf(T_('Aliro error in %s: class not found %s'), $this->formalname, $this->name));
	}

	protected function unTranslateTask ($translated) {
		if (method_exists($this->name, 'taskTranslator')) {
			$translator = call_user_func(array($this->name, 'taskTranslator'));
			$result = array_search ($translated, $translator);
			return $result ? $result : $translated;
		}
		return $translated;
	}
	
	private function getAction () {
		$actname = strtoupper(substr($this->act,0,1)).strtolower(substr($this->act,1));
		return strtolower($this->barename).'Admin'.$actname;
	}

	public function activate () {
		if (empty($this->controller->ignoreMagicQuotes)) $this->noMagicQuotes();
		$task = $this->task.'Task';
		if (method_exists($this->controller, 'getRequestData')) $this->controller->getRequestData();
		if (method_exists($this->controller, 'checkPermission')) {
			if (!$this->controller->checkPermission()) {
				$this->redirect('index.php', T_('You are not authorized to view this resource.'), _ALIRO_ERROR_FATAL);
			}
		}
		$task = $this->task.'Task';
		if (method_exists($this->controller,$task)) {
			$this->controller->$task();
		}
		else trigger_error(sprintf(T_('Aliro error in %s: method %s not found in class %s'), $this->formalname, $task, $this->name));
	}

	public function toolbar () {
		if (method_exists($this->controller,'toolbar')) $this->controller->toolbar();
		else trigger_error(sprintf(T_('Aliro error in %s: method %s not found in class %s'), $this->formalname, 'toolbar', $this->name));
	}

	public function mainHeading () {
		if (method_exists($this->controller,'mainHeading')) $this->controller->mainHeading();
	}
	
}
/**
* Component base class for admin side component controller logic
* Part of Aliro
*/
abstract class aliroComponentAdminControllers extends aliroComponentControllers {
	public $optionurl = '';
	public $fulloptionurl = '';
	public $act = '';
	public $task = '';
	protected $cid = array(0);
	protected $cidall = array(0);
	protected $currid = 0;
	protected $translator = array();

	protected function __construct () {
		parent::__construct();
		if (!$this->option) {
			$this->option = $this->getParam ($_REQUEST, 'core');
			$this->optionurl = 'index.php?core=';
		}
		else $this->optionurl = 'index.php?option=';
		$this->task = $this->getTask();
		// Problem - e.g. change number of items per page, null task returned - so no good using getParam default alone
		$this->cidall = $this->getParam($_REQUEST, 'cidall', array(0));
		$this->cid = (array) $this->getParam($_REQUEST, 'cid', array(0));
		$this->cid = array_map('intval', $this->cid);
		$id = $this->getParam($_REQUEST, 'id', 0);
		$this->currid = $id ? $id : $this->cid[0];
		$this->optionurl .= $this->option;
		if (!empty($this->act)) $this->optionurl .= '&amp;act='.$this->act;
		$this->fulloptionurl = $this->getCfg('admin_site').'/'.$this->optionurl;
		$classname = get_class($this);
		if (method_exists($classname, 'taskTranslator')) {
			$this->translator = call_user_func(array($classname, 'taskTranslator'));
		}
	}

	protected function __clone () {
		// Protected to enforce singleton
	}

	protected function checkExclusion ($task, $showError=true) {
		if (isset($this->function_exclude) AND in_array($task, $this->function_exclude)) {
			if ($showError) $this->setErrorMessage(T_('Invalid operation attempted'), _ALIRO_ERROR_FATAL);
			return true;
		}
		return false;
	}

	// This is the basic default - may be overridden
	public function toolbar () {
		$toolbar = aliroAdminToolbar::getInstance();
		switch ($this->task) {
			case 'new':
			case 'edit':
				$toolbar->save();
				$toolbar->apply();
				$toolbar->cancel();
				break;

			case 'list':
			default:
				if (!$this->checkExclusion('new', false)) {
					$toolbar->addNew();
				}
				if (!$this->checkExclusion('remove', false)) {
					$toolbar->deleteList();
				}
				if (!$this->checkExclusion('edit', false)) $toolbar->editList();
				break;
		}
	}

	protected function toolBarButton ($task, $requireSelect=false) {
	    $template = $this->getTemplateObject();
		$translated = isset($this->translator[$task]) ? $this->translator[$task] : $task;
		$template->toolBarButton($translated, $requireSelect);
	}

	protected function check_selection ($text) {
		if (!is_array($this->cid) OR count( $this->cid ) < 1) {
			$this->setErrorMessage($text);
			return false;
		}
		return true;
	}

	protected function basicInsert ($tablename) {
		$database = aliroDatabase::getInstance();
		$query = "INSERT INTO $tablename (";
		$fields = $this->getTableInfo($tablename);
		foreach ($fields as $field) {
			$fieldname = $field->Field;
			if ($value = $this->handleField($fieldname, $field->Type)) {
				$fieldset[] = "`$fieldname`";
				$valueset[] = "'$value'";
			}
		}
		if (isset($fieldset)) {
			$query .= implode(',', $fieldset).') VALUES ('.implode(',', $valueset).')';
			$database->doSQL($query);
			$newid = $database->insertid();
		}
		else $newid = 0;
		return $newid;
	}

	protected function basicUpdate ($tablename, $keyname, $id) {
		$database = aliroDatabase::getInstance();
		$query = "UPDATE $tablename SET ";
		$fields = $this->getTableInfo($tablename);
		foreach ($fields as $field) {
			$fieldname = $field->Field;
			if ($fieldname == $keyname) continue;
			$value = $this->handleField($fieldname, $field->Type);
			$setters[] = "`$fieldname` = '$value'";
		}
		if (isset($setters)) {
			$query .= implode (',', $setters)." WHERE `$keyname` = $id";
			$database->doSQL($query);
		}
	}

	private function handleField ($fieldname, $type) {
		$fieldname[0] = strtoupper($fieldname[0]);
		if (false === strpos($type, 'text')) $mask = 0;
		else $mask = _MOS_ALLOWHTML;
		$value = $this->getParam($_POST, $fieldname, null, $mask);
		$database = aliroDatabase::getInstance();
		$value = $database->getEscaped($value);
		$method = 'validate'.$fieldname;
		if (method_exists($this, $method)) $this->$method($value);
		return $value;
	}

}