<?php
/**
 * DelLinkAction class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-29
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */


/**
 * Links model
 */
require_once 'models/Links.php';

/**
 *	DelLinkAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class DelLinkAction extends Action
{
    /**
     *   delete a link
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		User::ValidateAdmin('You must be an administrator to Remove Links');
		
		$i_id = (int) $poActionForm->Get('lid');
		$i_gid = $poActionForm->Get('gid');

		Links::Delete($i_id);

		$o_action_forward =& $poActionMapping->Get('edit');
		$o_action_forward->SetPath($o_action_forward->GetPath()."&gid=$i_gid");
		return $o_action_forward;
    }
}

?>
