<?php
/**
 * Links model class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-28
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Models
 */

/**
 * get links for a single link group
 */
define('LINKS_BY_GROUP_SQL', <<<EOS
SELECT *
FROM links
WHERE link_group_id = ?
EOS
);

/**
 * retrieve a single link
 */
define('LINKS_SEL_ONE_SQL', <<<EOS
SELECT *
FROM links
WHERE link_id = ?
EOS
);

/**
 * link add sql
 */
define('LINKS_ADD_SQL', 'SELECT add_link(?, ?, ?, ?)');

/**
 * link update sql
 */
define('LINKS_UPD_SQL', 'SELECT upd_link(?, ?, ?, ?)');

/**
 * link change order sql
 */
define('LINKS_ORD_SQL', 'SELECT ord_link(?, ?)');

/**
 * link delete sql
 */
define('LINKS_DEL_SQL', 'SELECT del_link(?)');

/**
 * link change group sql
 */
define('LINKS_CHGRP_SQL', 'SELECT chgrp_link(?, ?)');


/**
 * Links class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Models
 */
class Links
{
	/**
	 * has this object changed
	 */
	var $_mbChanged;

	/**
	 * constructor
	 * @return	void
	 */
	function Links()
	{
		$this->_mbChanged = false;
	}
	
	/**
	 * return links for a single group id
	 */
	function GetByGroup($piGroupId)
	{
		global $go_conn;
		
		$a_bind = array((int)$piGroupId);
		$o_rs = $go_conn->Execute(LINKS_BY_GROUP_SQL, $a_bind);
		if ($o_rs) {
			return $o_rs->GetArray();
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}
	

	/**
	 * add a new link
	 */
	function Add($piGrp, $psName, $psUrl, $psDesc)
	{
		global $go_conn;
		
		$a_bind = array((int)$piGrp, $psName, $psUrl, $psDesc);
		$o_rs = $go_conn->Execute(LINKS_ADD_SQL, $a_bind);
		if ($o_rs) {
			return true;
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}

	
	/** 
	 * update link
	 */
	function Update($paVals)
	{
		global $go_conn;
		
		if (!(array_key_exists('link_id', $paVals) &&
			array_key_exists('name', $paVals) &&
			array_key_exists('url', $paVals) &&
			array_key_exists('link_desc', $paVals))) {
			appl_error('Invalid input array to Links::Update'."<pre>\n".var_export($paVals,true).'</pre>');
			return false;
		}

		$o_rs = $go_conn->Execute(LINKS_SEL_ONE_SQL, array((int) $paVals['link_id']));
		if ($o_rs) {
			if (!$o_rs->EOF) {
				$a_row = $o_rs->FetchRow();
				if ($paVals['name'] == $a_row['name'] &&
					$paVals['url'] == $a_row['url'] &&
					$paVals['link_desc'] == $a_row['link_desc']) {
					//nothing to change
					return true;
				}
				$a_bind = array(
					(int) $paVals['link_id']
					,$paVals['name']
					,$paVals['url']
					,$paVals['link_desc']
					);
				$o_rs = $go_conn->Execute(LINKS_UPD_SQL, $a_bind);
				if ($o_rs) {
					$this->_mbChanged = true;
					return true;
				} else {
					trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
					return false;
				}
			} else {
				appl_error('No link with id '.$paVals['link_id'].' exists to update.');
				return false;
			}
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}
	
	/**
	 * change the order of a link
	 */
	function Order($piId, $piOrd)
	{
		global $go_conn;
		
		$a_bind = array((int) $piId, (int) $piOrd);
		$o_rs = $go_conn->Execute(LINKS_ORD_SQL, $a_bind);
		if ($o_rs) {
			$this->_mbChanged = true;
			return true;
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}

	/**
	 * change the group of a link
	 */
	function ChGrp($paVals)
	{
		global $go_conn;
		
		if (!(array_key_exists('link_id', $paVals) &&
			array_key_exists('link_group_id', $paVals))) {
			appl_error('Invalid input array to Links::ChGrp'."<pre>\n".var_export($paVals,true).'</pre>');
			return false;
		}

		$a_bind = array((int) $paVals['link_id'], (int) $paVals['link_group_id']);
		$o_rs = $go_conn->Execute(LINKS_CHGRP_SQL, $a_bind);
		if ($o_rs) {
			$this->_mbChanged = true;
			return true;
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}


	/**
	 * remove a link
	 */
	function Delete($piId)
	{
		global $go_conn;
		
		$a_bind = array((int) $piId);
		$o_rs = $go_conn->Execute(LINKS_DEL_SQL, $a_bind);
		if ($o_rs) {
			return true;
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}

	/**
	 * has this object changed
	 */
	function IsChanged()
	{
		return $this->_mbChanged;
	}
}	
