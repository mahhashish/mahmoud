<?php
/**
 * GroupForm class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-29
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Forms
 */

/**
 * GroupForm class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Forms
 */
class GroupForm extends ActionForm
{
	/**
	 * update list
	 */
	var $_moUpdList;
	
	/**
	 * override PutAll method for custom processing
	 *
	 * @param	array	$paIn	input array
	 * @return	void
	 */
	function PutAll($paIn)
	{
		Parent::PutAll($paIn);
		
		$a_list = array();
		$a_loop = $this->Get('groups');
		if (is_array($a_loop)) {
			for ($i=&new ArrayIterator($a_loop); $i->IsValid(); $i->Next()) {
				$i_upd_key = (int)$i->GetCurrent();
				$a_add = array(
					'link_group_id'	=> $i_upd_key
					,'group_name'	=> stripslashes($this->Get('group_name'.$i_upd_key))
					,'group_desc'	=> stripslashes($this->Get('group_desc'.$i_upd_key))
					);
				$a_list[] = $a_add;
			}
		}
		$this->_moUpdList =&new ArrayList($a_list);
	}
	
	/**
	 * return list iterator for updates
	 *
	 * @return	object	list iterator
	 */
	function &GetList()
	{
		return $this->_moUpdList->ListIterator();
	}
}

?>
