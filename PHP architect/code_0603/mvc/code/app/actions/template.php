<?php
/**
 * NAMEAction class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-29
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */

/**
 *	NAMEAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class NAMEAction extends Action
{
    /**
     *   purpose
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {

		$o_action_forward =& $poActionMapping->Get('edit');
		return $o_action_forward;
    }
}

?>
