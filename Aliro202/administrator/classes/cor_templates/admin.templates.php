<?php

class templatesAdminTemplates extends aliroDBUpdateController {
	public $act = 'templates';

	protected $session_var = 'alirodoc_classid';
	protected $table_name = '#__extensions';
	protected $DBname = 'aliroCoreDatabase';
	protected $view_class = 'listTemplatesHTML';
	protected $limit_list = "type = 'template'";
	public $list_exclude = array ('type', 'formalname', 'author', 'date', 'authoremail',
	'authorurl', 'class', 'adminclass', 'menuclass', 'xml');
	public $column_names = array ('default_template' => 'Default');
	protected $function_exclude = array ('new', 'remove', 'edit', 'save', 'apply');

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self($manager));
	}
	
	public function makedefTask () {
		$id = $this->getParam($_REQUEST, 'id', 0);
		$database = call_user_func(array($this->DBname, 'getInstance'));
		$database->setQuery ("SELECT admin FROM #__extensions WHERE type='template' AND id=$id");
		$admin = $database->loadResult();
		if (null != $admin) {
			$database->doSQL ("UPDATE #__extensions SET default_template=IF(id=$id,1,0) WHERE type='template' AND admin=$admin");
		}
		aliroTemplateHandler::getInstance()->clearCache();
		$this->redirect($this->optionurl);
	}
}