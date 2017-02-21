<?php
/**
 * UpdLinkAction class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-30
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */

/**
 * Link model
 */
require_once 'models/Links.php';

/**
 *	UpdLinkAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class UpdLinkAction extends Action
{
    /**
     *   proces the links update form
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		User::ValidateAdmin('You must be an administrator to Update Links');
		
		$o_link =& new Links;
		$o_upd = $poActionForm->GetUpdList();
		$o_grp = $poActionForm->GetGrpList();
		$i_gid = $poActionForm->Get('gid');

		while ($o_upd->HasNext()) {
			$a_vals = $o_upd->Next();
			$o_link->Update($a_vals);
		}
		
		while ($o_grp->HasNext()) {
			$a_vals = $o_grp->Next();
			$o_link->ChGrp($a_vals);
		}
		
		if (!$o_link->IsChanged()) {
			appl_error('Please change a value before updating.');
		}

		$o_action_forward =& $poActionMapping->Get('edit');
		$o_action_forward->SetPath($o_action_forward->GetPath()."&gid=$i_gid");
		return $o_action_forward;
    }
}

?>
