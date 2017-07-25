<?php

require_once('class.Sql.php');
require_once('class.Menu.php');

class SqlMenu extends Menu
{
	var $sql;
	var $menu = array();
	var $xml;
	var $heading;

	function SqlMenu($heading, $style, $host, $database, $username, $password) 
	{
		$this->heading = $heading;
		$this->sql = new Sql($host, $database, $username, $password);
		$this->_buildSqlMenu();
		$this->_buildXML();
		Menu::Menu($this->xml, $style);
	}

	function _buildSqlMenu()
	{
		$this->sql->query('select menu_section.name as section, menu_item.name as item, menu_item.href, menu_item.image from menu_section, menu_item where menu_section.section_id=menu_item.section_id order by menu_section.name, menu_item.sort');
		while ( $this->sql->nextRow() ) {
			$this->_addItem($this->sql->getField('section'), $this->sql->getField('item'), $this->sql->getField('href'), $this->sql->getField('image'));
		}
	}

	function _addItem($section, $item, $href, $image) 
	{
		$this->menu[$section][] = array (
			'name' => $item,
			'href' => $href,
			'image' => $image );
	}

	function _buildXML() 
	{
		$this->xml = "<menu>\n";
		$this->xml .= "<heading>". $this->heading ."</heading>\n";
		$this->xml .= "<state>closed</state>\n";
		foreach ( $this->menu as $section => $items ) {
			$this->xml .= "<section id='$section'>\n";
			foreach ( $items as $item ) {
				$this->xml .= "<item>\n";
				$this->xml .= "<name>". $item['name'] ."</name>\n";
				$this->xml .= "<href>". $item['href'] ."</href>\n";
				$this->xml .= "<image>". $item['image'] ."</image>\n";
				$this->xml .= "</item>\n";
			}
			$this->xml .= "</section>\n";
		}
		$this->xml .= "</menu>\n";
	}
}

?>
