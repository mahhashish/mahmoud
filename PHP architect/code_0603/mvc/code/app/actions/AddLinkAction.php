<?php
/**
 * AddLinkAction class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-05-01
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */

/**
 * Links model
 */
require_once 'models/Links.php';

/**
 *	AddLinkAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class AddLinkAction extends Action
{
    /**
     *   add a link
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		User::ValidateAdmin('You must be an administrator to Add Links');
		
		$i_gid = (int)$poActionForm->Get('link_group');
		Links::Add(
			 $i_gid
			,stripslashes($poActionForm->Get('name'))
			,stripslashes($poActionForm->Get('url'))
			,stripslashes($poActionForm->Get('link_desc'))
			);

		$o_action_forward =& $poActionMapping->Get('edit');
		$o_action_forward->SetPath($o_action_forward->GetPath().'&gid='.$i_gid);
		return $o_action_forward;
    }
}

?>
