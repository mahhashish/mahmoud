<?php
/**
 * IndexView class description
 *
 * @author	Jason E. Sweat
 * @since	2003-04-27
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */

/**
 *	view template
 */
define('INDEX_VIEW_TEMPLATE', 'index.tpl');

/**
 * Groups model
 */
require_once 'models/Groups.php';

/** 
 *	IndexView class description
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */
class IndexView extends View
{
	/**
	 *	template
	 */
	var $_msTemplate = INDEX_VIEW_TEMPLATE;
	
	/**
	 *	constructor function
	 *	@return	void
	 */
	function IndexView()
	{
	}

	/**
	 * assign data to Smarty in preperation for display
	 *
	 * @param	object	$poSmarty	the smarty instance
	 * @return	void
	 */
	function Prepare()
	{
	
		$this->_moTpl->Assign(array(
			 'title_extra'	=> ''
			,'view'			=> 'index'
			,'group'		=> Groups::GetInfo()
			,'test'			=> var_export(Groups::GetInfo(), true)
			));

		$this->_mbPrepared = true;
	}
	
}

?>
