<?php
/**
 * Errors model class definition
 *
 * enforces the session as the storage mechanism for this application
 * @author	Jason E. Sweat
 * @since	2003-04-29
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Models
 */

/**
 * Errors class defintion
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Models
 */
class Errors extends Stack
{
	/** 
	 * constructor
	 * @return void
	 */
	function Errors()
	{
		if (!array_key_exists(_ERRORS, $_SESSION)) {
			$_SESSION[_ERRORS] = array();
		}
		$this->_elements =& $_SESSION[_ERRORS];
	}
}
