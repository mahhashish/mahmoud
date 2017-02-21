<?php
/**
 * OrdLinkAction class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-30
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */

/**
 * Links model
 */
require_once 'models/Links.php';

/**
 *	OrdLinkAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class OrdLinkAction extends Action
{
    /**
     *   reorder a link within a group
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		User::ValidateAdmin('You must be an administrator to Reorder Links');
		
		$o_link =& new Links;
		
		$i_id  = $poActionForm->Get('lid');
		$i_ord = $poActionForm->Get('ord');
		$i_gid = $poActionForm->Get('gid');
		
		$o_link->Order($i_id, $i_ord);

		$o_action_forward =& $poActionMapping->Get('edit');
		$o_action_forward->SetPath($o_action_forward->GetPath()."&gid=$i_gid");
		return $o_action_forward;
    }
}

?>
