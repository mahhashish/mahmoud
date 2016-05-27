<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the 
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more 
 * information on this, please see the index.php file or visit 
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * aliroAuthorisationAdmin complements aliroAuthoriser, which answers questions about
 * permissions through the Aliro Role Based Access Control (RBAC) system.  This class
 * is used to set the permissions and assignments that are involved.  It can be used from
 * either the user or admin sides, depending on how RBAC management is deployed in a
 * particular application.
 *
 */

define ('_ALIRO_ASSIGNMENTS_TABLE','#__assignments');
define ('_ALIRO_PERMISSIONS_TABLE','#__permissions');

class aliroAuthorisationAdmin {
	protected static $instance = null;
	
	protected $authoriserclass = 'aliroAuthoriser';
	protected $assign_table = _ALIRO_ASSIGNMENTS_TABLE;
	protected $permit_table = _ALIRO_PERMISSIONS_TABLE;

	protected $handler = null;
	protected $authoriser = null;
	protected $database = null;
	protected $myid = 0;
	protected $request = null;
	protected $html = null;
	protected $holders = array();

	protected function __construct () {
		$this->authoriser = call_user_func(array($this->authoriserclass, 'getInstance'));
		$this->handler = $this->authoriser->getCache();
		$this->database = aliroCoreDatabase::getInstance();
		$this->getUsefulObjects();
		$this->myid = $this->getUserID();
	}

	protected function __clone () {
		// Enforce singleton
	}

	public static function getInstance () {
	    return self::$instance instanceof self ? self::$instance : (self::$instance = new self());
	}

	// CMS Specific method that may need to be overriden in other environments
	protected function getUsefulObjects () {
		$this->request = call_user_func(array('aliroRequest', 'getInstance'));
		$this->html = call_user_func(array('aliroHTML', 'getInstance'));
	}

	// CMS Specific method
	protected function getUserID () {
		$user = call_user_func(array('aliroUser', 'getInstance'));
		return $user->id;
	}

	// CMS Specific method
	protected function getParam ($arr, $name, $def='', $mask=0) {
		return $this->request->getParam($arr, $name, $def, $mask);
	}

	protected function doSQL ($sql, $clear=false) {
		$this->database->doSQL($sql);
		if ($clear) $this->clearCache();
	}

	public function clearCache () {
		$this->handler->clearCache();
		$this->authoriser->clearCache();
	}

	public function getAllRoles ($addSpecial=false) {
		return $this->authoriser->getAllRoles($addSpecial);
	}

	public function getTranslatedRole ($role) {
		return $this->authoriser->getTranslatedRole($role);
	}

	public function getRoleProperties ($role, $formalname) {
		$rawdata = $this->database->doSQLget("SELECT property, value FROM #__role_properties WHERE role = '$role' AND formalname = '$formalname'");
		foreach ($rawdata as $raw) $result[$raw->property] = unserialize(base64_decode($raw->value));
		return isset($result) ? $result : array();
	}

	public function deleteRoleProperty ($role, $formalname, $property) {
		$rolelist = implode("', '", $this->getAllRoles());
		if (!$rolelist) $rolelist = "''";
		$this->database->doSQL("DELETE FROM #__role_properties WHERE (role = '$role' AND formalname = '$formalname' AND property = '$property')"
		." OR role NOT IN ($rolelist)");
	}

	public function setRoleProperty ($role, $formalname, $property, $value) {
		$encoded = base64_encode(serialize($value));
		$this->database->doSQL("INSERT INTO #__role_properties (role, formalname, property, value)"
		." VALUES ('$role', '$formalname', '$property', '$encoded') ON DUPLICATE KEY UPDATE value = '$encoded'");
	}

	protected function permissionHolders ($subject_type, $subject_id) {
		if (isset($this->holders[$subject_type][$subject_id])) return $this->holders[$subject_type][$subject_id];
		$sql = "SELECT DISTINCT role, action, control, subject_type, subject_id FROM $this->permit_table";
		if ($subject_type != '*') $where[] = "(subject_type='$subject_type' OR subject_type='*')";
		if ($subject_id != '*') $where[] = "(subject_id='$subject_id' OR subject_id='*')";
		if (isset($where)) $sql .= " WHERE ".implode(' AND ', $where);
		$this->holders[$subject_type][$subject_id] = $this->database->doSQLget($sql);
		return $this->holders[$subject_type][$subject_id];
	}

	public function copyPermissions ($from_type, $from_id, $to_type, $to_id, $system=false) {
		if ($from_type != $to_type OR $from_id != $to_id) {
			$this->doSQL("DELETE FROM $this->permit_table WHERE subject_type = '$to_type' AND subject_id = '$to_id'");
			$system = $system ? $system : '0';
			$this->doSQL("INSERT INTO $this->permit_table (role, control, action, subject_type, subject_id, system) 
			(SELECT role, control, action, '$to_type' AS subject_type, '$to_id' AS subject_id, $system AS system
			 FROM $this->permit_table WHERE subject_type = '$from_type' AND subject_id = '$from_id')", true);
		}
	}

	public function permissionsToSubjectType ($subject_type, $excludeBarred=true) {
		if ($excludeBarred) {
			$barred = $this->authoriser->getBarredRoles();
			$barlist = implode("','", $barred);
			$exclude = "AND role NOT IN ('$barlist')";
		}
		else $exclude = '';	
		$sql = "SELECT role, action, subject_id FROM $this->permit_table"
		."\n WHERE 0 != (control & 2) AND subject_type = '$subject_type' $exclude"
		."\n ORDER BY role, action, subject_id";
		return $this->database->doSQLget($sql);
	}

	public function permittedRoles ($actions, $subject_type, $subject_id, $excluding=null) {
		// $nonspecific = true;
		foreach ($this->permissionHolders ($subject_type, $subject_id) as $possible) {
			if ('*' == $possible->action OR in_array($possible->action, (array) $actions)) {
				$result[$possible->role] = $this->getTranslatedRole($possible->role);
				// if ('*' != $possible->subject_type AND '*' != $possible->subject_id) $nonspecific = false;
			}
		}
		// Non specific is false if there is at least one role that has a specific permission (no asterisks)
		// Not sure why this should have been used to permit visitor with "AND $nonspecific"?
		if (!isset($result)) $result['Public'] = $this->getTranslatedRole('Public');
		foreach ((array) $excluding as $exclude) if (isset($result[$exclude])) unset($result[$exclude]);
		return $result;
	}

	protected function nonLocalPermissionHolders ($subject_type, $subject_id) {
		$sql = "SELECT role, action, control FROM $this->permit_table WHERE (action='*' OR subject_type='*' OR subject_id='*') AND ((subject_type='$subject_type' OR subject_type='*') AND (subject_id='$subject_id' OR subject_id='*'))";
		return $this->database->doSQLget($sql);
	}

	protected function permitSQL ($role, $control, $action, $subject_type, $subject_id) {
		$this->database->setQuery("SELECT id FROM $this->permit_table WHERE role='$role' AND action='$action' AND subject_type='$subject_type' AND subject_id='$subject_id'");
		$id = $this->database->loadResult();
		if ($id) return "UPDATE $this->permit_table SET control=$control WHERE id=$id";
		else return "INSERT INTO $this->permit_table (role, control, action, subject_type, subject_id) VALUES ('$role', '$control', '$action', '$subject_type', '$subject_id')";
	}

	public function permit ($role, $control, $action, $subject_type, $subject_id) {
		$sql = $this->permitSQL($role, $control, $action, $subject_type, $subject_id);
		$this->doSQL($sql, true);
	}

	public function assign ($role, $access_type, $access_id, $clear=true) {
		if ($this->handler->barredRole($role)) return false;
		$this->database->setQuery("SELECT id FROM $this->assign_table WHERE role='$role' AND access_type='$access_type' AND access_id='$access_id'");
		if ($this->database->loadResult()) return true;
		$sql = "INSERT INTO $this->assign_table (role, access_type, access_id) VALUES ('$role', '$access_type', '$access_id')";
		$this->doSQL($sql, $clear);
		return true;
	}

	public function unassign ($role, $access_type, $access_id) {
		$this->database->doSQL("DELETE FROM $this->assign_table WHERE role='$role' AND access_type='$access_type' AND access_id='$access_id'", true);
		return true;
	}

	public function assignRoleSet ($roleset, $access_type, $access_id) {
		$this->dropAccess ($access_type, $access_id);
		$roleset = $this->authoriser->minimizeRoleSet($roleset);
		foreach ($roleset as $role) $this->assign ($role, $access_type, $access_id, false);
		$this->clearCache();
	}

	public function dropAccess ($access_type, $access_id) {
		$sql = "DELETE FROM $this->assign_table WHERE access_type='$access_type' AND access_id='$access_id'";
		$this->doSQL($sql, true);
	}

	public function &getMyControllingRoles ($action, $subject_type, $subject_id) {
		$sql = "SELECT a.role FROM $this->permit_table AS p INNER JOIN $this->assign_table AS a ON a.role=p.role"
		." WHERE a.access_type='aUser'"
		." AND a.access_id='$this->myid' AND (p.control&1)"
		." AND p.action='$action' AND p.subject_type='$subject_type' AND p.subject_id='$subject_id'";
		$this->doSQL($sql);
		$roles = $this->database->loadResultArray();
		return $roles;
	}

	public function &getMyPermissions () {
		$sql = "SELECT p.action, p.subject_type, p.subject_id, control
			FROM $this->permit_table AS p INNER JOIN $this->assign_table AS a ON p.role=a.role
			WHERE a.access_type='aUser' AND (a.access_id='$this->myid' OR a.access_id='*')
			AND (p.control&1)";
		$this->doSQL($sql);
		$permissions = $this->database->loadObjectList();
		return $permissions;
	}

	public function getMyJointPermissions ($role) {
		$sql = "SELECT p2.control AS hiscontrol, p1.control AS mycontrol, p1.action, p1.subject_type, p1.subject_id"
		." FROM `$this->assign_table` AS a INNER JOIN `$this->permit_table` AS p1 ON p1.role=a.role "
		." LEFT JOIN `$this->permit_table` AS p2"
		." ON (p2.role='$role' AND p1.action=p2.action AND p1.subject_type=p2.subject_type AND p1.subject_id=p2.subject_id)"
		." WHERE  (p1.control&1) AND a.access_type='aUser' AND (a.access_id='$this->myid' OR a.access_id='*')";
		$this->doSQL($sql);
		$permissions = $this->database->loadObjectList();
		return $permissions;
	}

	public function getAccessLists ($access_type, $access_id, $action, $subject_type, $subject_id) {
		if ($this->authoriser->checkControl($access_type, $access_id, $action, $subject_type, $subject_id)) {
			$cangrant = $this->authoriser->checkGrant($access_type, $access_id, $action, $subject_type, $subject_id);
			$permissions = $this->permissionHolders($subject_type, $subject_id);
			$allroles = $this->getAllRoles();
			foreach ($allroles as $role) {
				$itemc[] = $optionc = $this->html->makeOption($role, $role);
				$itema[] = $optiona = $this->html->makeOption($role, $role);
				if ($cangrant) $itemg[] = $optiong = $this->html->makeOption($role, $role);
				foreach ($permissions as $permission) {
					if (($permission->action == '*' OR $permission->action == $action) AND $permission->role == $role) {
						if ($permission->control & 1) $cselected[] = $optionc;
						if ($permission->control & 2) $aselected[] = $optiona;
						if ($cangrant AND $permission->control & 4) $gselected[] = $optiong;
					}
				}
			}
			$results[] = $this->html->selectList($itema, $action.'_arole[]', 'multiple="multiple"', 'value', 'text', $aselected);
			$results[] = $this->html->selectList($itemc, $action.'_crole[]', 'multiple="multiple"', 'value', 'text', $cselected);
			if ($cangrant) $results[] = $this->html->selectList($itemg, $action.'_grole[]', 'multiple="multiple"', 'value', 'text', $gselected);
		}
		else $results = array();
		return $results;
	}

	public function resetPermissions ($action, $subject_type, $subject_id) {
		$control_types = array ('crole', 'arole', 'grole');
		$control_values = array (1,2,4);
		$permissions = $this->nonLocalPermissionHolders($subject_type, $subject_id);
		$this->dropPermissions($action, $subject_type, $subject_id);
		foreach ($control_types as $i=>$type) {
			$key = $action.'_'.$type;
			if (isset($_POST[$key])) {
				foreach ($_POST[$key] as $role) {
					$value = isset($newpermits[$role]) ? $newpermits[$role] : 0;
					$newpermits[$role] = $value | $control_values[$i];
				}
			}
		}
		$sql = '';
		foreach ($newpermits as $role=>$value) {
			$needed = true;
			foreach ($permissions as $permission) {
				if (($permission->action == '*' OR $permission->action == $action) AND $permission->role == $role) {
					if (($value & $permission->control) === $value) {
						$needed = false;
						break;
					}
				}
			}
			if ($needed) $sql .= $this->permitSQL ($role, $value, $action, $subject_type, $subject_id);
		}
		if ($sql) $this->doSQL($sql, true);
	}

	public function roleExists ($role) {
		return in_array($role, $this->getAllRoles());
	}

	public function dropRole ($role) {
		$sql = "DELETE FROM $this->permit_table WHERE action='administer' AND subject_type='$role' AND system=0";
		$this->doSQL($sql);
		$sql = "DELETE a FROM $this->assign_table AS a LEFT JOIN $this->permit_table AS p ON a.role=p.role WHERE a.role='$role' AND (p.system=0 OR p.system IS NULL)";
		$this->doSQL($sql);
		$sql = "DELETE FROM $this->permit_table WHERE role='$role' AND system=0";
		$this->doSQL($sql, true);
	}

	public function dropPermissions ($action, $subject_type, $subject_id) {
		$sids = $this->conditionOnID($subject_id, 'subject_id');
		if ($sids) {
			$where[] = $sids;
			$where[] = 'system = 0';
			$where[] = "subject_type = '$subject_type'";
			if ($action) $where[] = "action = '$action'";
			$condition = implode(' AND ', $where);
			$this->doSQL("DELETE FROM $this->permit_table WHERE $condition", true);
		}
	}
	
	public function dropAllPermissions ($subject_type, $subject_id) {
		$this->dropPermissions('', $subject_type, $subject_id);
	}
	
	protected function conditionOnID ($id, $field) {
		foreach ((array) $id as $one) $idset[] = $this->database->getEscaped($one);
		if (isset($idset)) {
			$idlist = implode("','", $idset);
			return "$field IN ('$idlist')";
		}
		else return '';
	}

	public function getRoleSelect ($subject, $subject_id, $action, $defaults=array()) {
		$defaults = (array) $defaults;
		$roles = $this->getAllRoles(true);
		if ($subject_id) $selected = $this->permittedRoles ($action, $subject, $subject_id);
		elseif (empty($defaults)) $selected = array();
		else foreach ($defaults as $default) $selected[$default] = 1;
		foreach ($roles as $role=>$translated) $selector[] = $this->html->makeOption($role, $translated);
		if (isset($selector)) {
			$selector = $this->html->selectList ($selector, 'permit_'.$action.'[]', 'multiple="multiple"', null, null, array_keys($selected));
			$newrole = T_('New role:');
			return <<<ROLE_SELECT
			
			<div>
				$selector
			</div>
			<div>
				<label for="new_role_$action">$newrole</label>
				<input class="inputbox" type="text" name="new_role_$action" id="new_role_$action" />
			</div>
			
ROLE_SELECT;

		}
		else {
			$noroles = T_('No roles are available in this context');
			return <<<NO_ROLES
		
			<div>
				$noroles
			</div>
		
NO_ROLES;

		}
	}

	// The following helper method assumes that post data comes from a form constructed using the above getRoleSelect method
	public function savePermissions ($actions, $subject, $subject_ids) {
		$actions = (array) $actions;
		$subject_ids = (array) $subject_ids;
		foreach ($actions as $action) {
			$this->dropPermissions($action, $subject, $subject_ids);
			$roles = $this->getParam($_POST, 'permit_'.$action, array());
			if (in_array('Public', $roles)) continue;
			if (in_array('Registered', $roles)) {
				$this->saveGrantPermissions('Registered', $action, $subject, $subject_ids);
				continue;
			}
			$extra = $this->getParam($_POST, 'new_role_'.$action);
			if ($extra) $roles[] = $extra;
			foreach ($roles as $role) {
				$role = $this->database->getEscaped($role);
				if ('none' != $role) $this->saveGrantPermissions($role, $action, $subject, $subject_ids);
			}
		}
	}
	
	protected function saveGrantPermissions ($role, $action, $subject, $subject_ids) {
		foreach ($subject_ids as $subject_id) {
			$this->permit ($role, 2, $action, $subject, $subject_id);
		}
	}
	
	public static function assignSuperAdmin ($id) {
		$assign_table = _ALIRO_ASSIGNMENTS_TABLE;
		aliroCoreDatabase::getInstance()->doSQL ("INSERT INTO $assign_table VALUES (0, 'aUser', $id, 'Super Administrator')");
	}
}

abstract class cmsapiAuthorisationAdmin extends aliroAuthorisationAdmin {}

abstract class jaliroAuthorisationAdmin extends aliroAuthorisationAdmin {

	protected $assign_table = '#__jaliro_assignments';
	protected $permit_tabe = '#__jaliro_permissions';
	
}
