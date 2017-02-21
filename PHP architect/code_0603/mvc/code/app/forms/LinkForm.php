<?php
/**
 * LinkForm class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-29
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Forms
 */

/**
 * LinkForm class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Forms
 */
class LinkForm extends ActionForm
{
	/**
	 * update list
	 */
	var $_moUpdList;
	/**
	 * change group list
	 */
	var $_moGrpList;
	
	/**
	 * override PutAll method for custom processing
	 *
	 * @param	array	$paIn	input array
	 * @return	void
	 */
	function PutAll($paIn)
	{
		Parent::PutAll($paIn);
		
		$a_upd = $a_grp = array();
		$a_loop = $this->Get('links');
		if (is_array($a_loop)) {
			for ($i=&new ArrayIterator($a_loop); $i->IsValid(); $i->Next()) {
				$i_upd_key = (int)$i->GetCurrent();
				// add current array for update list
				$a_add = array(
					 'link_id'		=> $i_upd_key
					,'name'			=> stripslashes($this->Get('name'.$i_upd_key))
					,'url'			=> stripslashes($this->Get('url'.$i_upd_key))
					,'link_desc'	=> stripslashes($this->Get('link_desc'.$i_upd_key))
					);
				$a_upd[] = $a_add;
				// add current array for group if changed
				$i_group = (int)$this->Get('link_group'.$i_upd_key);
				$i_old_grp = (int)$this->Get('old_link_group'.$i_upd_key);
				if ($i_group != $i_old_grp) {
					$a_add = array(
						 'link_id'			=> $i_upd_key
						,'link_group_id'	=> $i_group
						);
					$a_grp[] = $a_add;
				}
			}
		}
		$this->_moUpdList =&new ArrayList($a_upd);
		$this->_moGrpList =&new ArrayList($a_grp);
	}
	
	/**
	 * return list iterator for updates
	 *
	 * @return	object	list iterator
	 */
	function &GetUpdList()
	{
		return $this->_moUpdList->ListIterator();
	}
	
	/**
	 * return list iterator for group changes
	 *
	 * @return	object	list iterator
	 */
	function &GetGrpList()
	{
		return $this->_moGrpList->ListIterator();
	}
	

}

?>
