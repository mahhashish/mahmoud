<?php
/**
 * AddGroupAction class definition
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
 *	AddGroupAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class AddGroupAction extends Action
{
    /**
     *  add a group
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		User::ValidateAdmin('You must be an administrator to Add Groups');
		
		Groups::Add(
			 stripslashes($poActionForm->Get('group_name'))
			,stripslashes($poActionForm->Get('group_desc'))
			);
		
		$o_action_forward =& $poActionMapping->Get('edit');
		return $o_action_forward;
    }
}

?>
