<?php
/**
 * UpdGroupAction class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-29
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */

/**
 * Group model
 */
require_once 'models/Groups.php';

/**
 *	UpdGroupAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class UpdGroupAction extends Action
{
    /**
     *   update group information from the form list
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		User::ValidateAdmin('You must be an administrator to Update Groups');
		
		$o_group =& new Groups;
		$o_list = $poActionForm->GetList();

		while ($o_list->HasNext()) {
			$a_vals = $o_list->Next();
			$o_group->Update($a_vals);
		}
		
		if (!$o_group->IsChanged()) {
			appl_error('Please change a value before updating.');
		}

		$o_action_forward =& $poActionMapping->Get('edit');
		return $o_action_forward;
    }
}

?>
