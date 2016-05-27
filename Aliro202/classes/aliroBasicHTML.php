<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the 
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more 
 * information on this, please see the index.php file or visit 
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * aliroBasicHTML provides useful methods and is designed as a parent class
 * for viewer classes in applications.
 *
 */

abstract class aliroBasicHTML extends aliroFriendlyBase  {
	protected $controller = null;
	protected $translations = array();
	protected $pageNav = null;
	protected $option = '';
	protected $optionline = '';
	protected $optionurl = '';
	protected $formstamp;
	protected $live_site = '';
	protected $admin_site = '';
	protected $template = null;

	public function __construct ($controller=null) {
		$this->controller = $controller;
		if (isset($controller->pageNav)) $this->pageNav = $controller->pageNav;
		$this->option = $this->getOption();
		$this->optionline = "<input type='hidden' name='option' value='$this->option' />";
		$this->optionurl = 'index.php?option='.$this->option;
		$this->live_site = $this->getCfg('live_site');
		// NOTE: if we are running on user side, this will be the same as live site
		// It will NOT give location of admin site when on user side
		$this->admin_site = $this->getCfg('admin_site');
		$this->template = $this->getTemplateObject();
	}

	protected function getFormStamp () {
		return $this->formstamp ? $this->formstamp : $this->formstamp = $this->makeFormStamp();
	}

	protected function T_ ($string) {
		return function_exists('T_') ? T_($string) : $string;
	}

	protected function show ($string) {
		return $string;
	}
	
	protected function checkedIfTrue ($bool) {
		return $bool ? 'checked="checked"' : '';
	}

	protected function html () {
		$args = func_get_args();
		$method = array_shift($args);
		$html = aliroHTML::getInstance();
		return call_user_func_array(array($html, $method), $args);
	}

}