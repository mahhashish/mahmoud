<?php

require_once('class.MySmarty.php');

class MenuStyle 
{
	var $styleDir;	
	var $tmpl;

	function MenuStyle($style = '') 
	{
		if ( empty($style) ) {
			$style = 'default';
		}

		$this->styleDir = 'styles/'.$style;

		$this->tmpl = new MySmarty($this->styleDir);
	}

	function setHeading($heading) 
	{
		$this->tmpl->assign('heading', $heading);
	}

	function setXML($xml) 
	{
		$this->tmpl->assign('xml', $xml);
	}

	function setSectionURL($url) 
	{
		$this->tmpl->assign('click', $url);
	}

	function getMenu() 
	{
		return $this->tmpl->fetch('menu.tmp');
	}
}

?>
