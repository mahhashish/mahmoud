<?php

/* Aliro Renderer classes
*/

class aliroRenderer {
	
	public static function getRenderer ($type='php') {
		if ('php' == $type) return new aliroPHPRenderer();
		else {
			$classname = $type.'Renderer';
			if (aliro::getInstance()->classExists($classname)) return new $classname();
        }
        trigger_error(T_('aliroRenderer called for invalid renderer type'), E_USER_ERROR);
	}
}

class aliroPHPRenderer extends basicAdminHTML implements ifTemplateRenderer  {
    private $dir;
    private $vars = array();
    protected $engine = 'php';
    private $template = '';
    private $debug = 0;
	protected $translations = array();
	public $act = '';
	public $pageNav = null;

    public function __construct () {
    	$this->dir = criticalInfo::getInstance()->class_base.'/views/templates/';
    }

    public function display ($template='') {
        return $this->checkTemplate($template) ? $this->loadTemplate($this->template) : false;
    }

    public function fetch ($template='') {
        if ($this->checkTemplate($template)) {
            ob_start();
			$this->loadTemplate($this->template);
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }
        return false;
    }
    
    private function loadTemplate ($template) {
    	extract($this->vars);
		if (!empty($act)) $this->act = $act;
    	include($this->template);
    	return true;
    }
    
    private function checkTemplate ($template) {
    	if (empty($template)) $template = $this->template;
        if ($this->debug) echo nl2br($this->template."\n");
        if (empty($template)) trigger_error(T_('A template has not been specified in a call to aliroRenderer'), E_USER_ERROR);
        elseif (!is_readable($this->dir.$template)) trigger_error(sprintf(T_('Specified template file %s is not readable in a call to aliroPHPRenderer'), $template), E_USER_ERROR);
    	else {
    		$this->template = $this->dir.$template;
    		return true;
    	}
    	return false;
    }

    public function getengine(){
        return $this->engine;
    }

    public function addvar($key, $value){
        $this->vars[$key] = $value;
    }

    public function addbyref ($key, &$value) {
        $this->vars[$key] = $value;
    }

    public function getvars ($name) {
        return isset($this->vars[$name]) ? $this->vars[$name] : '';
    }

    public function setdir ($dir) {
        $this->dir = (substr($dir, -1) == '/') ? $dir : $dir.'/';
    }

    public function settemplate ($template){
        $this->template = $template;
    }

    // Provides for aliroHTML methods to be used within heredoc as $this->html('method', ...)
	protected function html () {
		$args = func_get_args();
		$method = array_shift($args);
		$html = call_user_func(array('aliroHTML', 'getInstance'));
		return call_user_func_array(array($html, $method), $args);
	}
   
	protected function T_ ($string) {
		return T_($string);
	}
}