<?php

// Useful HTML class for user side components

class aliroBasicHTML extends aliroFriendlyBase  {
	protected $controller = null;
	protected $translations = array();
	protected $pageNav = null;
	protected $option = '';
	protected $optionline = '';
	protected $optionurl = '';
	protected $formstamp;

	public function __construct ($controller) {
		$this->controller = $controller;
		$this->pageNav = $controller->pageNav;
		$this->option = $this->getOption();
		$this->optionline = "<input type='hidden' name='option' value='$this->option' />";
		$this->optionurl = 'index.php?option='.$this->option;
		$this->formstamp = $this->makeFormStamp();
	}

	protected function T_ ($string) {
		if (isset($this->translations[$string])) return $this->translations[$string];
		trigger_error(sprintf(T_('No translation %s for %s'),get_class($this),$string));
		return $string;
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
		$html = call_user_func(array('aliroHTML', 'getInstance'));
		return call_user_func_array(array($html, $method), $args);
	}

}