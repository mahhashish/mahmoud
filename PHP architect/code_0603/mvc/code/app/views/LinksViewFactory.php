<?php
/**
 *	links application view factory
 *
 * @author		Jason E. Sweat
 * @since		2003-03-14
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */

/**
 *	View baseclass
 */
require_once LINKS_LIB.'phrame/View.php';
/**
 *	ViewFactory baseclass
 */
require_once LINKS_LIB.'phrame/ViewFactory.php';


/**
 *	LinksViewFactory class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */
class LinksViewFactory extends ViewFactory
{
 	/**
 	 *	constructor function
 	 *	@return	void
 	 */
 	function LinksViewFactory()
 	{
 	}
 	
	/**
	 *	return class based on view
	 *
	 *	Must be overridden in the application, should always return a valid class file.
	 *
	 *	@return string
	 */
	function _GetViewClass($psView)
	{
		switch(strtolower($psView)) {
		case 'list':
			$s_ret = 'ListView';
			break;
		case 'groupedit':
			$s_ret = 'GroupEditView';
			break;
		case 'linkedit':
			$s_ret = 'LinkEditView';
			break;
		case 'index':
		default:
			$s_ret = 'IndexView';
		}
		return $s_ret;
	}
}
?>
