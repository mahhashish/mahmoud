<?php
/**
 * ListView class description
 *
 * @author	Jason E. Sweat
 * @since	2003-04-28
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */

/**
 *	view template
 */
define('LIST_VIEW_TEMPLATE', 'list.tpl');

/**
 * Groups model
 */
require_once 'models/Groups.php';
/**
 * Links model
 */
require_once 'models/Links.php';

/** 
 *	ListView class description
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */
class ListView extends View
{
	/**
	 *	template
	 */
	var $_msTemplate = LIST_VIEW_TEMPLATE;
	
	/**
	 *	constructor function
	 *	@return	void
	 */
	function ListView()
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
		$a_groups = Groups::GetInfo();
		$a_links = array();
		for($i=&new ArrayIterator($a_groups); $i->IsValid(); $i->Next()) {
			$a_group = $i->GetCurrent();
			$a_links[] = Links::GetByGroup($a_group['link_group_id']);
		}
	
		$this->_moTpl->Assign(array(
			 'title_extra'	=> 'Listing'
			,'group'		=> $a_groups
			,'link'			=> $a_links
			,'test'			=> 'x'//var_export(Groups::GetInfo(), true)
			));

		$this->_mbPrepared = true;
	}
	
}

?>
