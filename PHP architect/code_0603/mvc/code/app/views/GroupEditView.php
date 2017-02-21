<?php
/**
 * GroupEditView class description
 *
 * @author	Jason E. Sweat
 * @since	2003-04-27
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */

/**
 *	view template
 */
define('GROUPEDIT_VIEW_TEMPLATE', 'groupedit.tpl');

/**
 * Groups model
 */
require_once 'models/Groups.php';
/**
 * Links model
 */
require_once 'models/Links.php';

/** 
 *	GroupEditView class description
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */
class GroupEditView extends View
{
	/**
	 *	template
	 */
	var $_msTemplate = GROUPEDIT_VIEW_TEMPLATE;
	
	/**
	 *	constructor function
	 *	@return	void
	 */
	function GroupEditView()
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
		$a_groups = Groups::GetInfo(true);
		$a_links = array();
		for($i=&new ArrayIterator($a_groups); $i->IsValid(); $i->Next()) {
			$a_group = $i->GetCurrent();
			$a_links[] = Links::GetByGroup($a_group['link_group_id']);
		}
	
		$this->_moTpl->Assign(array(
			 'title_extra'	=> 'Editing Groups'
			,'group'		=> $a_groups
			,'link'			=> $a_links
			,'group_opt'	=> Groups::Options()
			,'test'			=> var_export(Groups::GetInfo(true), true)
			));

		$this->_mbPrepared = true;
	}
	
}

?>
