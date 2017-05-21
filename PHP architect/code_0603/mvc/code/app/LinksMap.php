<?php
/**
 *	links application map
 *
 * @author		Jason E. Sweat
 * @since		2003-04-27
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Controller
 */

/**
 *	LinksMap class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Controller
 */
class LinksMap extends MappingManager
{
	/**
 	 *	constructor function
 	 *	@return	void
 	 */
 	function LinksMap()
 	{
 		$this->_SetOptions();
 	
 		$this->_AddForm('links', 'ActionForm');
		$this->_AddForm('updgroup', 'GroupForm');
		$this->_AddForm('updlinks', 'LinkForm');
		/*AddMapping(mapname, mapclass, actiondefloc, formmap):
                 * the name of the mapping,
                 * the class that implements the mapping,
                 * the default location where the action is called and,
                 * lastly, the form mapping associated with this action.*/
                
		//default action to show views
		// no forwards are required becuase this action displays HTML pages
 		$this->_AddMapping('ShowView', 'ShowViewAction', APPL_ACTN, 'links');
		//admin login action
 		$this->_AddMapping('AdminLogin', 'LoginAction', APPL_BASE.'index', 'links');
 		$this->_AddForward('AdminLogin', 'index');
 		$this->_AddForward('AdminLogin', 'edit', APPL_BASE.'groupedit');
		//group edit actions
		$this->_AddMapping('AddGroup', 'AddGroupAction', APPL_BASE.'groupedit', 'links'); 
		$this->_AddForward('AddGroup', 'edit');
		$this->_AddMapping('UpdGroup', 'UpdGroupAction', APPL_BASE.'groupedit', 'updgroup');
		$this->_AddForward('UpdGroup', 'edit');
		$this->_AddMapping('OrdGroup', 'OrdGroupAction', APPL_BASE.'groupedit', 'links');
		$this->_AddForward('OrdGroup', 'edit');
		$this->_AddMapping('DelGroup', 'DelGroupAction', APPL_BASE.'groupedit', 'links');
		$this->_AddForward('DelGroup', 'edit');
		//link edit actions
		$this->_AddMapping('AddLink', 'AddLinkAction', APPL_BASE.'linkedit', 'links'); 
		$this->_AddForward('AddLink', 'edit');
		$this->_AddMapping('UpdLink', 'UpdLinkAction', APPL_BASE.'linkedit', 'updlinks');
		$this->_AddForward('UpdLink', 'edit');
		$this->_AddMapping('OrdLink', 'OrdLinkAction', APPL_BASE.'linkedit', 'links');
		$this->_AddForward('OrdLink', 'edit');
		$this->_AddMapping('DelLink', 'DelLinkAction', APPL_BASE.'linkedit', 'links');
		$this->_AddForward('DelLink', 'edit');
		
 	}
 }

?>
