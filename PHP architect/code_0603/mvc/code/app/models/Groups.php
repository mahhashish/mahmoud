<?php
/**
 * Groups model class definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-28
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Models
 */
 
/**
 * link group summary info
 */
define('GROUPS_INFO_SQL', <<<EOS
SELECT *
FROM groups
WHERE link_cnt > 0
EOS
);

/**
 * link group details (includes empty groups)
 */
define('GROUPS_DETAIL_SQL', <<<EOS
SELECT *
FROM groups
EOS
);

/**
 * single link group info
 */
define('GROUPS_SEL_ONE_SQL', <<<EOS
SELECT *
FROM groups
WHERE link_group_id = ?
EOS
);

/**
 * link group options list
 */
define('GROUPS_SEL_OPTS_SQL', <<<EOS
SELECT 
	 link_group_id
	,group_name
FROM groups
EOS
);

/**
 * link group add sql
 */
define('GROUPS_ADD_SQL', 'SELECT add_link_group(?, ?)');

/**
 * link group update sql
 */
define('GROUPS_UPD_SQL', 'SELECT upd_link_group(?, ?, ?)');

/**
 * link group change order sql
 */
define('GROUPS_ORD_SQL', 'SELECT ord_link_group(?, ?)');

/**
 * link group delete sql
 */
define('GROUPS_DEL_SQL', 'SELECT del_link_group(?)');

/**
 * Groups class definition
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Models
 */
class Groups
{
	/**
	 * has this object changed
	 */
	var $_mbChanged;
	
	/**
	 * constructor
	 * @return	void
	 */
	function Groups()
	{
		$this->_mbChanged = false;
	}
	
	/**
	 * get group info
	 */
	function GetInfo($pbEmpty=false)
	{
		global $go_conn;
		
		$s_sql = ($pbEmpty) ? GROUPS_DETAIL_SQL : GROUPS_INFO_SQL;
		$o_rs = $go_conn->Execute($s_sql);
		if ($o_rs) {
			return $o_rs->GetArray();
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}

	/**
	 * add a new link group
	 */
	function Add($psName, $psDesc)
	{
		global $go_conn;
		
		$a_bind = array($psName, $psDesc);
		$o_rs = $go_conn->Execute(GROUPS_ADD_SQL, $a_bind);
		if ($o_rs) {
			return true;
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}

	
	/** 
	 * update link group
	 */
	function Update($paVals)
	{
		global $go_conn;
		
		if (!(array_key_exists('link_group_id', $paVals) &&
			array_key_exists('group_name', $paVals) &&
			array_key_exists('group_desc', $paVals))) {
			appl_error('Invalid input array to Groups::Update');
			return false;
		}
		
		$o_rs = $go_conn->Execute(GROUPS_SEL_ONE_SQL, array((int) $paVals['link_group_id']));
		if ($o_rs) {
			if (!$o_rs->EOF) {
				$a_row = $o_rs->FetchRow();
				if ($paVals['group_name'] == $a_row['group_name'] &&
					$paVals['group_desc'] == $a_row['group_desc']) {
					//nothing to change
					return true;
				}
				$a_bind = array(
					(int) $paVals['link_group_id']
					,$paVals['group_name']
					,$paVals['group_desc']
					);
				$o_rs = $go_conn->Execute(GROUPS_UPD_SQL, $a_bind);
				if ($o_rs) {
					$this->_mbChanged = true;
					return true;
				} else {
					trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
					return false;
				}
			} else {
				appl_error('No link group with id '.$paVals['link_group_id'].' exists to update.');
				return false;
			}
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}
	
	/**
	 * change the order of a link group
	 */
	function Order($piId, $piOrd)
	{
		global $go_conn;
		
		$a_bind = array((int) $piId, (int) $piOrd);
		$o_rs = $go_conn->Execute(GROUPS_ORD_SQL, $a_bind);
		if ($o_rs) {
			$this->_mbChanged = true;
			return true;
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}
	
	/**
	 * remove a link group
	 */
	function Delete($piId)
	{
		global $go_conn;
		
		$a_bind = array((int) $piId);
		$o_rs = $go_conn->Execute(GROUPS_DEL_SQL, $a_bind);
		if ($o_rs) {
			return true;
		} else {
			trigger_error(DB_OOPS."\n".$go_conn->ErrorMsg());
			return false;
		}
	}

	/**
	 * get options array
	 * @return	array
	 */
	function Options()
	{
		global $go_conn;
		
		$o_rs = $go_conn->Execute(GROUPS_SEL_OPTS_SQL);
		if ($o_rs) {
			return $o_rs->GetAssoc();
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
