<?php
/**
* @package Mambo Open Source
* @copyright (C) 2005 - 2006 Mambo Foundation Inc.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* Mambo was originally developed by Miro (www.miro.com.au) in 2000. Miro assigned the copyright in Mambo to The Mambo Foundation in 2005 to ensure
* that Mambo remained free Open Source software owned and managed by the community.
* Mambo is Free Software
*/ 

// Provided for backwards compatibility only

class MENU_Default {
	/**
	* Draws a default set of menu icons
	*/
	function MENU_Default() {
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::addNew();
		mosMenuBar::editList();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
}