<?php
/**
 * ShowViewAction class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-26
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */

/**
 *	Links application View Factory
 */
require_once 'views/LinksViewFactory.php';
/**
 * User model
 */
require_once 'models/User.php';

/**
 *	ShowViewAction class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Actions
 */
class ShowViewAction extends Action
{
    /**
     *    perform the action of showing a view
     *
     *    @param    object	$poActionMapping	the action mapping object
     *    @param    object	$poActionForm		the form object
     *    @return	object	ActionForward
     */
    function &Perform(&$poActionMapping, &$poActionForm)
    {
		global $gb_debug;
		
		$o_view_factory =& new LinksViewFactory;
		$o_smarty =& new Smarty;
		$o_smarty->autoload_filters = array(//'pre' => array('trim', 'stamp'),
                                  		'output' => array('trimwhitespace')); 
        
		$s_view = strtolower($poActionForm->Get('view'));
 		$o_view =& $o_view_factory->Build($s_view);
		$o_view->Init($o_smarty, $poActionForm);
		
		//security check
		switch (get_class($o_view)) {
		case 'indexview':
		case 'listview':
			$b_restricted = false;
			break;
		default:
			$b_restricted = true;
		}
		if ($b_restricted) {
			User::ValidateAdmin('You must be an administrator view this portion of the application');
		}
	
		//any default assignments
		$o_smarty->Assign(array(
			 'view'			=> $s_view
			,'view_link'	=> APPL_BASE
			,'action_link'	=> APPL_ACTN
			,'action'		=> _ACTION
			,'admin'		=> User::IsAdmin()
			,'debug'		=> ($gb_debug && User::IsAdmin()) ? true : false
			));
	
		//render the template
		$o_view->Render();
	
		exit;
    }
}

?>
