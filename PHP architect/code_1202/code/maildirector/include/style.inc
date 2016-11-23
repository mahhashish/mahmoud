<?php /* $Id: style.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once( dirname(__FILE__) . '/smarty/Smarty.class.php');

class style extends smarty
{
	function style()
	{
		$root = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), '/') );
		
		$this->template_dir = $root . '/theme/';
		$this->config_dir 	= $root . '/theme/config/';
		$this->compile_dir	= $root . '/tmp/compile/';
		$this->cache_dir	= '';
		$this->caching		= false;
	}
}

?>
