<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Aliro is open source software, free to use, and licensed under GPL.
 * You can find the full licence at http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * The author freely draws attention to the fact that Aliro derives from Mambo,
 * software that is controlled by the Mambo Foundation.  However, this section
 * of code is totally new.  If it should contain any fragments that are similar
 * to Mambo, please bear in mind (1) there are only so many ways to do things
 * and (2) the author of Aliro is also the author and copyright owner for large
 * parts of Mambo 4.6.
 *
 * Tribute should be paid to all the developers who took Mambo to the stage
 * it had reached at the time Aliro was created.  It is a feature rich system
 * that contains a good deal of innovation.
 *
 * Your attention is also drawn to the fact that Aliro relies on other items of
 * open source software, which is very much in the spirit of open source.  Aliro
 * wishes to give credit to those items of code.  Please refer to
 * http://aliro.org/credits for details.  The credits are not included within
 * the Aliro package simply to avoid providing a marker that allows hackers to
 * identify the system.
 *
 * Copyright in this code is strictly reserved by its author, Martin Brampton.
 * If it seems appropriate, the copyright will be vested in the Aliro Organisation
 * at a suitable time.
 *
 * Copyright (c) 2007 Martin Brampton
 *
 * http://aliro.org
 *
 * counterpoint@aliro.org
 *
 * aliroAuthorisationAdmin complements aliroAuthoriser, which answers questions about
 * permissions through the Aliro Role Based Access Control (RBAC) system.  This class
 * is used to set the permissions and assignments that are involved.  It can be used from
 * either the user or admin sides, depending on how RBAC management is deployed in a
 * particular application.
 *
 */

class aliroAuthorisationAdmin {
	private static $instance = __CLASS__;

	private $handler = null;
	private $authoriser = null;
	private $database = null;

	private function __construct () {
		$this->handler = aliroAuthoriserCache::getInstance();
		$this->authoriser = aliroAuthoriser::getInstance();
		$this->database = aliroCoreDatabase::getInstance();
	}

	private function __clone () {
		// Enforce singleton
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	private function doSQL ($sql, $clear=false) {
		$this->database->doSQL($sql);
		if ($clear) $this->clearCache();
	}

	private function clearCache () {
		$this->handler->clearCache();
		$this->authoriser->clearCache();
	}

	public function getAllRoles ($addSpecial=false) {
		return $this->authoriser->getAllRoles($addSpecial);
	}

	public function getTranslatedRole ($role) {
		return $this->authoriser->getTranslatedRole($role);
	}

	private function &permissionHolders ($subject_type, $subject_id) {
		$sql = "SELECT DISTINCT role, action, control, subject_type, subject_id FROM #__permissions";
		if ($subject_type != '*') $where[] = "(subject_type='$subject_type' OR subject_type='*')";
		if ($subject_id != '*') $where[] = "(subject_id='$subject_id' OR subject_id='*')";
		if (isset($where)) $sql .= " WHERE ".implode(' AND ', $where);
		$result = $this->database->doSQLget($sql);
		return $result;
	}

	public function permittedRoles ($actions, $subject_type, $subject_id, $excluding=null) {
		$nonspecific = true;
		foreach ($this->permissionHolders ($subject_type, $subject_id) as $possible) {
			if ('*' == $possible->action OR in_array($possible->action, (array) $actions)) {
				$result[$possible->role] = $this->getTranslatedRole($possible->role);
				if ('*' != $possible->subject_type AND '*' != $possible->subject_id) $nonspecific = false;
			}
		}
		if (!isset($result) OR $nonspecific) $result['Visitor'] = $this->getTranslatedRole('Visitor');
		foreach ((array) $excluding as $exclude) if (isset($result[$exclude])) unset($result[$exclude]);
		return $result;
	}

	private function &nonLocalPermissionHolders ($subject_type, $subject_id) {
		$sql = "SELECT role, action, control FROM #__permissions WHERE (action='*' OR subject_type='*' OR subject_id='*') AND ((subject_type='$subject_type' OR subject_type='*') AND (subject_id='$subject_id' OR subject_id='*'))";
		return $this->database->doSQLget($sql);
	}

	private function permitSQL ($role, $control, $action, $subject_type, $subject_id) {
		$this->database->setQuery("SELECT id FROM #__permissions WHERE role='$role' AND action='$action' AND subject_type='$subject_type' AND subject_id='$subject_id'");
		$id = $this->database->loadResult();
		if ($id) return "UPDATE #__permissions SET control=$control WHERE id=$id";
		else return "INSERT INTO #__permissions (role, control, action, subject_type, subject_id) VALUES ('$role', '$control', '$action', '$subject_type', '$subject_id')";
	}

	public function permit ($role, $control, $action, $subject_type, $subject_id) {
		$sql = $this->permitSQL($role, $control, $action, $subject_type, $subject_id);
		$this->doSQL($sql, true);
	}

	public function assign ($role, $access_type, $access_id, $clear=true) {
		if ($this->handler->barredRole($role)) return false;
		$this->database->setQuery("SELECT id FROM #__assignments WHERE role='$role' AND access_type='$access_type' AND access_id='$access_id'");
		if ($this->database->loadResult()) return true;
		$sql = "INSERT INTO #__assignments (role, access_type, access_id) VALUES ('$role', '$access_type', '$access_id')";
		$this->doSQL($sql, $clear);
		return true;
	}

	public function unassign ($role, $access_type, $access_id) {
		$this->database->doSQL("DELETE FROM #__assignments WHERE role='$role' AND access_type='$access_type' AND access_id='$access_id'", true);
		return true;
	}

	public function assignRoleSet ($roleset, $access_type, $access_id) {
		$this->dropAccess ($access_type, $access_id);
		$roleset = $this->authoriser->minimizeRoleSet($roleset);
		foreach ($roleset as $role) $this->assign ($role, $access_type, $access_id, false);
		$this->clearCache();
	}

	public function dropAccess ($access_type, $access_id) {
		$sql = "DELETE FROM #__assignments WHERE access_type='$access_type' AND access_id='$access_id'";
		$this->doSQL($sql, true);
	}

	public function &getMyControllingRoles ($action, $subject_type, $subject_id) {
		$user = aliroUser::getInstance();
		$sql = "SELECT a.role FROM #__permissions AS p INNER JOIN #__assignments AS a ON a.role=p.role"
		." WHERE a.access_type='aUser'"
		." AND a.access_id='$user->id' AND (p.control&1)"
		." AND p.action='$action' AND p.subject_type='$subject_type' AND p.subject_id='$subject_id'";
		$this->doSQL($sql);
		$roles = $this->database->loadResultArray();
		return $roles;
	}

	public function &getMyPermissions () {
		$user = aliroUser::getInstance();
		$sql = 'SELECT p.action, p.subject_type, p.subject_id, control '
		. ' FROM #__permissions AS p INNER JOIN #__assignments AS a ON p.role=a.role '
		. " WHERE a.access_type='aUser' AND (a.access_id='$user->id' OR a.access_id='*')"
		. ' AND (p.control&1)';
		$this->doSQL($sql);
		$permissions = $this->database->loadObjectList();
		return $permissions;
	}

	public function getMyJointPermissions ($role) {
		$user = aliroUser::getInstance();
		$sql = "SELECT p2.control AS hiscontrol, p1.control AS mycontrol, p1.action, p1.subject_type, p1.subject_id"
		." FROM `#__assignments` AS a INNER JOIN `#__permissions` AS p1 ON p1.role=a.role "
		." LEFT JOIN `#__permissions` AS p2"
		." ON (p2.role='$role' AND p1.action=p2.action AND p1.subject_type=p2.subject_type AND p1.subject_id=p2.subject_id)"
		." WHERE  (p1.control&1) AND a.access_type='aUser' AND (a.access_id='$user->id' OR a.access_id='*')";
		$this->doSQL($sql);
		$permissions = $this->database->loadObjectList();
		return $permissions;
	}

	public function getAccessLists ($access_type, $access_id, $action, $subject_type, $subject_id) {
		if ($this->authoriser->checkControl($access_type, $access_id, $action, $subject_type, $subject_id)) {
			$cangrant = $this->authoriser->checkGrant($access_type, $access_id, $action, $subject_type, $subject_id);
			$permissions = $this->permissionHolders($subject_type, $subject_id);
			$allroles = $this->getAllRoles();
			$alirohtml = aliroHTML::getInstance();
			foreach ($allroles as $role) {
				$itemc[] = $optionc = $alirohtml->makeOption($role, $role);
				$itema[] = $optiona = $alirohtml->makeOption($role, $role);
				if ($cangrant) $itemg[] = $optiong = $alirohtml->makeOption($role, $role);
				foreach ($permissions as $permission) {
					if (($permission->action == '*' OR $permission->action == $action) AND $permission->role == $role) {
						if ($permission->control & 1) $cselected[] = $optionc;
						if ($permission->control & 2) $aselected[] = $optiona;
						if ($cangrant AND $permission->control & 4) $gselected[] = $optiong;
					}
				}
			}
			$results[] = $alirohtml->selectList($itema, $action.'_arole[]', 'multiple="multiple"', 'value', 'text', $aselected);
			$results[] = $alirohtml->selectList($itemc, $action.'_crole[]', 'multiple="multiple"', 'value', 'text', $cselected);
			if ($cangrant) $results[] = $alirohtml->selectList($itemg, $action.'_grole[]', 'multiple="multiple"', 'value', 'text', $gselected);
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
		$sql = "DELETE FROM #__permissions WHERE action='administer' AND subject_type='$role' AND system=0";
		$this->doSQL($sql);
		$sql = "DELETE a FROM #__assignments AS a LEFT JOIN #__permissions AS p ON a.role=p.role WHERE a.role='$role' AND (p.system=0 OR p.system IS NULL)";
		$this->doSQL($sql);
		$sql = "DELETE FROM #__permissions WHERE role='$role' AND system=0";
		$this->doSQL($sql, true);
	}

	public function dropPermissions ($action, $subject_type, $subject_id) {
		$sql = "DELETE FROM #__permissions WHERE action='$action' AND subject_type='$subject_type'AND subject_id='$subject_id' AND system=0";
		$this->doSQL($sql, true);
	}

}
