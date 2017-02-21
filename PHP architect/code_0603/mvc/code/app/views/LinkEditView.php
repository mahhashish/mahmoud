<?php
/**
 * LinkEditView class description
 *
 * @author	Jason E. Sweat
 * @since	2003-04-28
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */

/**
 *	view template
 */
define('LINKEDIT_TEMPLATE', 'linkedit.tpl');

/**
 * Groups model
 */
require_once 'models/Groups.php';

/**
 * Links model
 */
require_once 'models/Links.php';

/** 
 *	ListView class description
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Views
 */
class LinkEditView extends View
{
	/**
	 *	template
	 */
	var $_msTemplate = LINKEDIT_TEMPLATE;
	
	/**
	 *	constructor function
	 *	@return	void
	 */
	function LinkEditView()
	{
	}

	/**
	 * assign data to Smarty in preperation for display
	 *
	 * @param	object	$poSmarty	the smarty instance
	 * @return	void
	 */
	function Prepare()
	{
		$i_gid = (int)$this->_moForm->Get('gid');
		$a_links = Links::GetByGroup($i_gid);;
	
		$this->_moTpl->Assign(array(
			 'title_extra'	=> 'Editing links in '.$a_links[0]['group_name']
			,'group_opt'	=> Groups::Options()
			,'link'			=> $a_links
			,'test'			=> 'x'//'<pre>'.var_export($this,true).'</pre>'
			));

		$this->_mbPrepared = true;
	}
	
}

?>
