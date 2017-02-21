<?php
/**
 * LoginAction class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-27
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */

/**
 * User model
 */
require_once 'models/User.php';

/**
 *	LoginAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class LoginAction extends Action
{
    /**
     *   test password for Admin rights
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		$s_password = $poActionForm->Get('pw');
		
		if (User::SetAdmin($s_password)) {
			$o_action_forward =& $poActionMapping->Get('edit');
		} else {
			$o_action_forward =& $poActionMapping->Get('index');
		}
		return $o_action_forward;
    }
}

?>
