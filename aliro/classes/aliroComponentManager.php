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
 * These classes are the optional basis for component (or to some extent
 * other add-ons) construction.  They are used extensively by Aliro itself.
 *
 * aliroFriendlyBase simply provides for any class that uses it as a base
 * to have properties and methods of general utility, such as access to
 * configuration data.
 *
 * aliroComponentManager is the abstract base class for manager classes
 * for components.
 *
 * aliroComponentUserManager is the class that provides initial logic to
 * handle the startup of a component on the user side.  It decides what
 * class and method to call.
 *
 * aliroComponentControllers is the base class for component logic, to be
 * called under the control of the manager class.  So the manager decides
 * what function has been requested, and from that uses a simple scheme
 * to derive the name of the controller class and method to handle the
 * request.  The actual code of the component inherits from this class.
 *
 */

abstract class aliroFriendlyBase {

	protected function getTableInfo ($tablename) {
		$database = call_user_func(array($this->DBname, 'getInstance'));
		return $database->getAllFieldInfo($tablename);
	}

	protected function __call ($method, $args) {
		return call_user_func_array(array(aliroRequest::getInstance(), $method), $args);
	}

	protected function __get ($property) {
		if ('option' == $property) return aliroRequest::getInstance()->getOption();
		$info = criticalInfo::getInstance();
		if (isset($info->$property)) return $info->$property;
		trigger_error(sprintf(T_('Invalid criticalInfo property %s requested through aliroFriendlyBase'), $property));
	}

	protected final function getCfg ($property) {
		return aliroCore::getInstance()->getCfg($property);
	}

	protected final function getParam ($array, $key, $default=null, $mask=0) {
		return aliroRequest::getInstance()->getParam ($array, $key, $default, $mask);
	}

	protected final function getStickyParam ($array, $key, $default=null, $mask=0) {
		return aliroRequest::getInstance()->getStickyParam ($array, $key, $default, $mask);
	}

	protected final function redirect ($url, $message='', $severity=_ALIRO_ERROR_INFORM) {
		aliroRequest::getInstance()->redirect($url, $message, $severity);
	}

	protected final function getUser () {
		$user = aliroUser::getInstance();
		return $user;
	}

	protected function formatDate ($time=null, $format=null) {
		return aliroLanguage::getInstance()->formatDate($time, $format);
	}

}

/**
* Component common base class for both user and admin sides
*/

abstract class aliroComponentManager extends aliroFriendlyBase {
	protected $name = '';
	protected $formalname = '';
	protected $barename = '';
	protected $system = '';
	protected $system_version = '';

	protected function __construct ($component, $system, $version) {
		$this->name = $component->name;
		$this->formalname = $component->option;
		$parts = explode('_', $this->formalname);
		$this->barename = isset($parts[1]) ? $parts[1] : $this->formalname;
		$this->system = $system;
		$this->system_version = $version;
		if(file_exists($this->absolute_path."/components/$this->formalname/language/".$this->getCfg('lang').'.php')) {
			require_once($this->absolute_path."/components/$this->formalname/language/".$this->getCfg('lang').'.php');
		}
		else if (file_exists($this->absolute_path."/components/$this->formalname/language/english.php")) {
			require_once($this->absolute_path."/components/$this->formalname/language/english.php");
		}
	}

	protected function __clone () {
		// Enforce singleton
	}

	protected function noMagicQuotes () {
		// Is magic quotes on?
		if (get_magic_quotes_gpc()) {
			// Yes? Strip the added slashes
			$_REQUEST = $this->remove_magic_quotes($_REQUEST);
			$_GET = $this->remove_magic_quotes($_GET);
			$_POST = $this->remove_magic_quotes($_POST);
			$_FILES = $this->remove_magic_quotes($_FILES, 'tmp_name');
		}
	}

	private function &remove_magic_quotes ($array, $exclude='') {
		foreach ($array as $k => &$v) {
			if (is_array($v)) $v = $this->remove_magic_quotes($v, $exclude);
			// Did apply stripslashes twice, why?  Removed to see what happens
			elseif ($k != $exclude) $v = stripslashes($v);
		}
		return $array;
	}

}

/**
* Component base logic for user side
*/

abstract class aliroComponentUserManager extends aliroComponentManager {
	private $func;
	private $method;
	private $classname;
	private $controller;
	public $menu = null;
	public $limit = 10;
	public $limitstart = 0;

	public function __construct ($component, $control_name, $alternatives, $default, $title, $system, $version, $menu) {
		parent::__construct($component, $system, $version);
		$this->menu = $menu;
		if ($title) $this->SetPageTitle($title);
		$this->func = $this->getParam ($_REQUEST, $control_name, $default);
		if (isset($alternatives[$this->func])) $this->method = $alternatives[$this->func];
		else $this->method = $this->func;
		$this->classname = $this->barename.'_'.$this->method.'_Controller';

		if (class_exists($this->classname)) $this->controller = call_user_func(array($this->classname, 'getInstance'), $this);
		else new aliroPage404();
	}

	public function activate() {
		$this->noMagicQuotes();
		$cmethod = $this->method;
		if (method_exists($this->controller,$cmethod)) $this->controller->$cmethod($this->func);
		else new aliroPage404();
	}

}

abstract class aliroComponentControllers extends aliroFriendlyBase {
	protected $authoriser = null;
	protected $user;
	protected $menu;
	protected $params;
	protected $manager;
	protected $idparm;
	public $pageNav;

	protected function __construct ($manager) {
		$this->manager = $manager;
		$this->authoriser = aliroAuthoriser::getInstance();
		$this->menu = isset($manager->menu) ? $manager->menu : null;
		if ($this->menu) $this->params = new aliroParameters($this->menu->params, $this->menu->name);
		else $this->params = new aliroParameters();
		$this->user = aliroUser::getInstance();
		$this->idparm = $this->getParam($_REQUEST, 'id', 0);
	}

	protected function __clone () {
		// Restricted to enforce singleton
	}

	public function makePageNav ($total) {
		$limit = $this->getUserStateFromRequest($this->option.'_page_limit', 'limit', intval($this->getCfg('list_limit')));
		$limitstart = $this->getUserStateFromRequest($this->option.'_page_limitstart', 'limitstart', 0 );
		$this->pageNav = new aliroPageNav($total, $limitstart, $limit );
	}

}