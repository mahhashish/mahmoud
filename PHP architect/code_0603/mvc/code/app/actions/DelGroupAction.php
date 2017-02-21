<?php
/**
 * DelGroupAction class definition
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
 *	DelGroupAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class DelGroupAction extends Action
{
    /**
     *   delete a group
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		User::ValidateAdmin('You must be an administrator to Remove Groups');
		
		$i_id = (int) $poActionForm->Get('gid');
		Groups::Delete($i_id);

		$o_action_forward =& $poActionMapping->Get('edit');
		return $o_action_forward;
    }
}

?>
