<?php

require_once('smarty/Smarty.class.php');

class MySmarty extends Smarty {
	var $template_dir 	= '';
	var $config_dir		= '';
	var $compile_dir	= '';
	
	function MySmarty($templates) {
		$this->template_dir = $templates;
		$this->config_dir = $templates.'/smarty_config';
		$this->compile_dir = $templates.'/smarty_compiled';
		$this->plugins_dir = array('my-plugins', 'plugins');
		if ( !is_dir($this->compile_dir) ) {
			mkdir($this->compile_dir);
			chmod($this->compile_dir, 0750);
		}
	}
}

?>
