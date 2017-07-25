<?php

require_once('class.MenuXML.php');
require_once('class.MenuStyle.php');

class Menu 
{
	var $data;
	var $style;
	var $xml;

	function Menu($data, $style)
	{
		if ( empty($data) ) 
			return false;
		if ( empty($style) ) 
			$style = 'default';

		$this->xml = new MenuXML($data);

		$this->style = new MenuStyle($style);
		$this->setTarget('_top');
		return true;
	}

	// Set the target to use
	function setTarget($target) {
		$this->target = $target;
	}

	// Reset the menu array
	function reset() {
		reset($this->xml->menu);
	}
	
	// Build the menu
	function buildMenu() {
		$this->style->setHeading($this->xml->heading);

		$menu_href = '';
		$i = 0;
		$curr_click = $_GET['click'];
		foreach ( $this->xml->menu as $k => $v ) {
			$curr_action = $_GET["m_${i}"];
			$action = '';
			// Business logic
			if ( !isset($_GET['click']) ) {
				if ( $this->state == 'open' ) 
					$action = 'on';
				else 
					$action = 'off';
			} elseif ( $i == $curr_click ) {
				if ( $curr_action == 'off' ) {
					$action = 'on';
				} elseif ( $curr_action == 'on' ){
					$action = 'off';
				}
			}

			if ( empty($action) ) 
				$action = $curr_action; 

			$menu_href[$i] =  "m_${i}=$action";
			
			// Business logic
			foreach ($v as $item_key => $item_value) {
				if ( $action == 'on' ) {
					$this->xml->menu[$k][$item_key]['display'] = true;
				} else {
					$this->xml->menu[$k][$item_key]['display'] = false;
				}
			}
			$i++;
		}

		$click = implode("&", $menu_href);

		$this->style->setSectionURL($click);
		$this->style->setXML($this->xml->menu);
		$this->menu = $this->style->getMenu();
	}

	function displayMenu() 
	{
		echo $this->menu;
	}

	function sub($var, $replace, $content) 
	{
		 return str_replace("{". $var ."}", "$replace", "$content");
	}
}

?>
