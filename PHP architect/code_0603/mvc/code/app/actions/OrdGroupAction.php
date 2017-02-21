<?php
/**
 * OrdGroupAction class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-29
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */

/**
 * Groups model
 */
require_once 'models/Groups.php';

/**
 *	OrdGroupAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class OrdGroupAction extends Action
{
    /**
     *   reorder a group
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		User::ValidateAdmin('You must be an administrator to Reorder Groups');
		
		$o_group =& new Groups;
		
		$i_id  = $poActionForm->Get('gid');
		$i_ord = $poActionForm->Get('ord');
		
		$o_group->Order($i_id, $i_ord);

		$o_action_forward =& $poActionMapping->Get('edit');
		return $o_action_forward;
    }
}

?>
