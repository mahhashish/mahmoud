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
 * aliroAuthoriser is a singleton class that handles questions concerning the Role Based
 * Access Control (RBAC) system for Aliro.  It is a companion to aliroAuthorisationAdmin
 * which is the class that deals with updating the RBAC information.  Since the information
 * used in this class is often particular to the current user, it makes poor sense to
 * have a general cache.  Instead, information is cached using session variables. An
 * exception to this principle is the linking structure that enables implied roles to
 * be derived - e.g. a Publisher implicitly also has the rights belonging to an Editor.
 * Since this information is tricky to construct and general to all users, it is cached
 * in the file system.
 *
 * The code is complicated by an effort to achieve a good degree of backwards compatibility
 * with Mambo 4.x and Joomla 1.x.  A few of the GACL methods are emulated.
 *
 */

class aliroAuthoriserCache extends cachedSingleton {
	protected static $instance= __CLASS__;

	private $linked_roles = array();
	private $user_roles = array();
	private $all_roles = array();
	private $all_subjects = array();
	private $translations = array (
		'Registered' => 'Registered(translated)',
		'Visitor' => 'Visitor(translated)',
		'Nobody' => 'Nobody(translated)',
		'none' => 'None of these(trans)'
		);

	protected function __construct () {
		// Making private enforces singleton
		$database = aliroCoreDatabase::getInstance();
		$database->setQuery("SELECT role, implied FROM #__role_link UNION SELECT DISTINCT role, role AS implied FROM #__assignments UNION SELECT DISTINCT role, role AS implied FROM #__permissions");
		$links = $database->loadObjectList();
		if ($links) foreach ($links as $link) {
			$this->all_roles[$link->role] = $link->role;
			$this->linked_roles[$link->role][$link->implied] = 1;
			foreach ($this->linked_roles as $role=>$impliedarray) {
				foreach ($impliedarray as $implied=>$marker) {
					if ($implied == $link->role OR $implied == $link->implied) {
						$this->linked_roles[$role][$link->implied] = 1;
						if (isset($this->linked_roles[$link->implied])) foreach ($this->linked_roles[$link->implied] as $more=>$marker) {
							$this->linked_roles[$role][$more] = 1;
						}
					}
				}
			}
		}
		$user_roles = $database->doSQLget("SELECT role, access_id FROM #__assignments WHERE access_type = 'aUser' AND (access_id = '*' OR access_id = '0')");
		foreach ($user_roles as $role) $this->user_roles[$role->access_id][$role->role] = 1;
		if (!isset($this->user_roles['0'])) $this->user_roles['0'] = array();
		if (isset($this->user_roles['*'])) $this->user_roles['0'] = array_merge($this->user_roles['0'], $this->user_roles['*']);
		$allsubject = $database->doSQLget("SELECT role, control, action FROM #__permissions WHERE subject_type = '*' AND subject_id - '*'");
		foreach ($allsubject as $asub) $this->all_subjects[$asub->role] = $asub;
		
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	public function getTranslatedRole ($role) {
		if (isset($this->translations[$role])) return $this->translations[$role];
		else return $role;
	}

	public function getAllRoles ($addSpecial=false) {
		$roles = $this->all_roles;
		if ($addSpecial) foreach ($this->translations as $raw=>$translated) $roles[$raw] = $translated;
		return $roles;
	}

	public function barredRole ($role) {
		if (isset($this->translations[$role])) return true;
		else return false;
	}

	public function getLinkedRoles () {
	    return $this->linked_roles;
	}

	public function getUserRoles ($id) {
	    return isset($this->user_roles[$id]) ? array_keys($this->user_roles[$id]) : array();
	}
	
	public function canRoleAccessAll ($role, $action, $control) {
		if (isset($this->all_subjects[$role])) {
			$asub = $this->all_subjects[$role];
			if ($action == $asub->action AND ($control & $asub->control)) return true;
		}
		return false;
	}

}


class aliroAuthoriser {
	private static $instance = __CLASS__;

	private $subj_found = array();
	private $permissions = array();
	private $access_found = array();
	private $access_roles = array();

	private $linked_roles = array();
	private $auth_vars = array ('subj_found', 'permissions', 'access_found', 'access_roles', 'refused');
	private $old_groupids = array ('Registered' => 18, 'Author' => 19, 'Editor' => 20, 'Publisher' => 21, 'Manager' => 23, 'Administrator' => 24, 'Super Administrator' => 25);

	private $handler = null;
	private $database = null;

	private function __construct () {
		// Make sure session started
		aliroSessionFactory::getSession();
		// Use session data as the source for cached user related data
		foreach ($this->auth_vars as $one_var) {
			if (!isset($_SESSION['aliro_auth'][$one_var])) $_SESSION['aliro_auth'][$one_var] = array();
			$this->$one_var =& $_SESSION['aliro_auth'][$one_var];
		}
		$this->handler = aliroAuthoriserCache::getInstance();
		$this->linked_roles = $this->handler->getLinkedRoles();
		$this->database = aliroCoreDatabase::getInstance();
	}

	private function __clone () {
		// Enforce singleton class
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	public function clearCache () {
		$this->subj_found = $this->permissions = $this->access_found = $this->access_roles = $this->refused_cache = array();
	}

	public function getAllRoles ($addSpecial=false) {
		return $this->handler->getAllRoles($addSpecial);
	}

	public function getTranslatedRole ($role) {
		return $this->handler->getTranslatedRole($role);
	}

	public function minimizeRoleSet ($roleset) {
		if (0 == count($roleset)) return $roleset;
		$first = array_shift($roleset);
		foreach ($roleset as $key=>$role) {
			if (isset($this->linked_roles[$first][$role])) unset ($roleset[$key]);
			if (isset($this->linked_roles[$role][$first])) return $this->minimizeRoleSet ($roleset);
		}
		array_unshift($roleset, $first);
		return $roleset;
	}

	private function getSubjectData ($subject, $id, $action) {
		$stamp = time();
		if (isset($this->subj_found[$subject][$action][$id]) AND (($stamp - $this->subj_found[$subject][$action][$id]) < _ALIRO_AUTHORISER_SESSION_CACHE_TIME)) return;
		if (isset($this->subj_found[$subject][$action]['*']) AND ($stamp - $this->subj_found[$subject][$action]['*'] < _ALIRO_AUTHORISER_SESSION_CACHE_TIME)) return;
		$this->database->setQuery("SELECT COUNT(*) FROM `#__permissions` WHERE `subject_type`='$subject' AND (`action`='$action' OR `action`='*')");
		if ($this->database->loadResult() < 100) {
			$this->database->setQuery("SELECT `role`, `control`, `subject_id`, `action` FROM `#__permissions` WHERE `subject_type`='$subject' AND (`action`='$action' OR `action`='*')");
			$new_permissions = $this->database->loadObjectList();
			unset($this->subj_found[$subject][$action]);
			$this->subj_found[$subject][$action]['*'] = $stamp;
		}
		else {
			$this->database->setQuery("SELECT role, control, subject_id, action FROM #__permissions WHERE subject_type='$subject' AND (subject_id='$id' OR subject_id='*') AND (action='$action' OR action='*')");
			$new_permissions = $this->database->loadObjectList();
			unset($this->subj_found[$subject][$action][$id]);
		}
		if ($new_permissions) {
			foreach ($new_permissions as $permit) {
				$this->permissions[$subject][$permit->action][$permit->subject_id][$permit->role] = $permit->control;
				$this->subj_found[$subject][$permit->action][$permit->subject_id] = $stamp;
			}
		}
	}

	public function getAccessorRoles ($type, $id) {
	    if ('aUser' == $type AND ('0' == $id OR '*' == $id)) return $this->handler->getUserRoles($id);
		if (isset($this->access_found[$type][$id])) {
			if ((time() - $this->access_found[$type][$id]) < _ALIRO_AUTHORISER_SESSION_CACHE_TIME) {
				return $this->mergeAccessorResults($type, $id);
			}
			unset ($this->access_found);
			$this->access_roles = array();
		}
		$sql = "SELECT role, access_id FROM #__assignments AS a WHERE a.access_type='$type'";
		$sql .= isset($this->access_found[$type]) ? " AND a.access_id='$id'" : " AND (a.access_id='$id' OR a.access_id='*' OR a.access_id='+')";
		$this->database->setQuery($sql);
		if ($results = $this->database->loadObjectList()) {
			foreach ($results as $result) {
				$this->access_roles[$type][$result->access_id][$result->role] = 1;
			}
		}
		$this->access_found[$type][$id] = time();
		return $this->mergeAccessorResults($type, $id);
	}

	private function mergeAccessorResults ($type, $id) {
		if (isset($this->access_roles[$type][$id])) $result = $this->access_roles[$type][$id];
		else $result = array();
		if (isset($this->access_roles[$type]['*'])) $result = array_merge($result, $this->access_roles[$type]['*']);
		if ($id AND isset($this->access_roles[$type]['+'])) $result = array_merge($result, $this->access_roles[$type]['+']);
		if ('aUser' == $type AND $id) $result['Registered'] = 1;
		if (count($result)) return array_keys ($result);
		else return array();
	}

	private function blanket ($action, $type) {
		return (isset($this->permissions[$type][$action]['*']) AND count($this->permissions[$type][$action]['*']));
	}

	private function specific ($action, $type, $id) {
		return (isset($this->permissions[$type][$action][$id]) AND count($this->permissions[$type][$action][$id]));
	}

	private function accessorPermissionOrControl  ($mask, $a_type, $a_id, $action, $s_type='*', $s_id='*') {
		$this->getSubjectData ($s_type, $s_id, $action);
		if ('*' != $s_type AND 2 == $mask AND !$this->blanket($action, $s_type) AND !($this->specific($action, $s_type, $s_id))) return 1;
		if ((!isset($this->permissions[$s_type][$action][$s_id]) OR 0 == count($this->permissions[$s_type][$action][$s_id]))
		AND (!isset($this->permissions[$s_type][$action]['*']) OR 0 == count($this->permissions[$s_type][$action]['*']))) return 1;
		$roles = $this->getAccessorRoles ($a_type, $a_id);
		return $this->rolePermissionOrControl ($mask, $roles, $action, $s_type, $s_id);
	}

	public function checkPermission ($a_type, $a_id, $action, $s_type='*', $s_id='*') {
		return $this->accessorPermissionOrControl(2, $a_type, $a_id, $action, $s_type, $s_id);
	}

	public function checkUserPermission ($action, $s_type='*', $s_id='*') {
		$user = aliroUser::getInstance();
		return $this->checkPermission ('aUser', $user->id, $action, $s_type, $s_id);
	}

	public function checkControl ($a_type, $a_id, $action, $s_type='*', $s_id='*') {
		return $this->accessorPermissionOrControl(1, $a_type, $a_id, $action, $s_type, $s_id);
	}

	public function checkGrant ($a_type, $a_id, $action, $s_type='*', $s_id='*') {
		return $this->accessorPermissionOrControl(4, $a_type, $a_id, $action, $s_type, $s_id);
	}

	private function rolePermissionOrControl ($mask, $roles, $actions, $s_type, $s_id) {
		foreach ((array) $roles as $role) {
			foreach ((array) $actions as $action) if ($this->handler->canRoleAccessAll ($role, $action, $mask)) return 1;
		}
		foreach ((array) $actions as $action) $this->getSubjectData ($s_type, $s_id, $action);
		if (in_array('Visitor', (array) $roles)) {
			foreach ((array) $actions as $action) {
				if (empty($this->permissions[$s_type][$action][$s_id])) return 1;
			}
		}
		if (count((array) $roles)) foreach ($this->permissions[$s_type] as $act=>$level2) {
				if (!in_array($act, (array) $actions) AND !in_array('*', (array) $actions)) continue;
			foreach ($level2 as $id=>$level3) {
				if ($id != $s_id AND $id != '*') continue;
				foreach ($level3 as $role=>$control)
					if (in_array($role, (array) $roles) AND ($mask & $control)) {
						return 1;
					}
			}
		}
		return 0;
	}

	public function checkRolePermission  ($role, $action, $s_type, $s_id) {
		return $this->rolePermissionOrControl(2, $role, $action, $s_type, $s_id);
	}

	public function checkRoleControl  ($role, $action, $s_type, $s_id) {
		return $this->rolePermissionOrControl(1, $role, $action, $s_type, $s_id);
	}

	public function checkRoleGrant  ($role, $action, $s_type, $s_id) {
		return $this->rolePermissionOrControl(4, $role, $action, $s_type, $s_id);
	}

	function getRefusedList ($a_type, $a_id, $s_type, $actionlist) {
		$roles = $this->getAccessorRoles($a_type, $a_id);
		$actions = explode(',', $actionlist);
		foreach ($actions as $i=>$action) $actions[$i] = trim($action);
		$alist = implode("','", $actions);
		if (isset($this->refused[$s_type][$alist])) $ids = $this->refused[$s_type][$alist];
		else {
			$ids = array();
			$results = $this->database->doSQLget("SELECT role, subject_id, action FROM #__permissions WHERE subject_type = '$s_type' AND action IN('$alist')");
			foreach ($results as $result) $ids[$result->subject_id][$result->action][] = $result->role;
			$this->refused[$s_type][$alist] = $ids;
		}
		if (count($ids)) {
			$refused = array_keys($ids);
			foreach ($ids as $id=>$actionset) {
				foreach ($actions as $action) if (!isset($actionset[$action])) $permits[$id] = 1;
				if (!isset($permits[$id])) foreach ($actionset as $action=>$permittedroles) {
					if (count(array_intersect($permittedroles, $roles))) $permits[$id] = 1;
				}
			}
			if (isset($permits)) $refused = array_diff ($refused, array_keys($permits));
		}
		else $refused = array();
		return $refused;
	}

	public function getRefusedListSQL ($a_type, $a_id, $s_type, $actionlist, $keyname) {
		$refused = $this->getRefusedList ($a_type, $a_id, $s_type, $actionlist);
		if (count($refused)) {
			$excludelist = implode("','", $refused);
			return " CAST($keyname AS CHAR) NOT IN ('$excludelist')";
		}
		return '';
	}

	public function listPermissions ($a_type, $a_id, $action) {
		$roles = $this->getAccessorRoles ($a_type, $a_id);
		$role_list = "IN ('".implode("','", $roles)."')";
		$this->database->setQuery("SELECT DISTINCT subject_type FROM #__permissions WHERE role $role_list AND action='$action' AND (control & 2) ORDER BY subject_type");
		$subjects = $this->database->loadResultArray();
		return $subjects;
	}

	public function &listUserPermissions ($action) {
		$user = aliroUser::getInstance();
		$results = $this->listPermissions ('aUser', $user->id, $action);
		return $results;
	}

	public function listAccessors ($accessor_type, $role) {
		$this->database->setQuery("SELECT access_id FROM #__assignments WHERE access_type = '$accessor_type' AND role = '$role'");
		$result = $this->database->loadResultArray();
		return $result ? $result : array();
	}

	//***** The following are emulations of old ACL functions for backwards compatibility
	public function acl_check($aco_section_value, $aco_value,
		$aro_section_value, $aro_value, $axo_section_value=NULL, $axo_value=NULL) {
		if ($axo_section_value == 'components') return $this->checkUserPermission ($aro_value, 'aliroComponent', $axo_value);
		return false;
	}

	public function getAroGroup ($id) {
		$old_roles = array_keys ($this->old_groupids);
		array_unshift($old_roles, '');
		$roles = $this->getAccessorRoles('aUser', $id);
		$max = 0;
		foreach ($roles as $role) {
			$key  = array_search($role, $old_roles);
			if ($key AND $key > $max) $max = $key;
		}
		$result = new stdClass();
		$result->name = $old_roles[$max];
		return $result;
	}

	public function get_group_name ($gid) {
		if (is_int($gid)) {
			$group = array_search($gid, $this->old_groupids);
			return $group;
		}
		return $gid;
	}

	public function get_group_children_tree ($root_id=null, $root_name=null, $inclusive=true) {
		if (null == $root_id AND true == $inclusive) {
			if ('Registered' == $root_name) {
				$result = unserialize('a:4:{i:0;O:8:"stdClass":2:{s:5:"value";s:2:"18";s:4:"text";s:17:"-&nbsp;Registered";}i:1;O:8:"stdClass":2:{s:5:"value";s:2:"19";s:4:"text";s:49:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Author";}i:2;O:8:"stdClass":2:{s:5:"value";s:2:"20";s:4:"text";s:85:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Editor";}i:3;O:8:"stdClass":2:{s:5:"value";s:2:"21";s:4:"text";s:124:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Publisher";}}');
				return $result;
			}
			if ('Public Backend' == $root_name) {
				$result = unserialize('a:4:{i:0;O:8:"stdClass":2:{s:5:"value";s:2:"30";s:4:"text";s:21:"-&nbsp;Public Backend";}i:1;O:8:"stdClass":2:{s:5:"value";s:2:"23";s:4:"text";s:50:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Manager";}i:2;O:8:"stdClass":2:{s:5:"value";s:2:"24";s:4:"text";s:92:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Administrator";}i:3;O:8:"stdClass":2:{s:5:"value";s:2:"25";s:4:"text";s:134:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;Super Administrator";}}');
				return $result;
			}
			trigger_error('Aliro emulation of get_group_children_tree needs extending');
		}
		else {
			foreach ($this->getAllRoles(true) as $i=>$role) $option[] = aliroHTML::makeOption($role, $role);
			return isset($option) ? $option : array();
		}
	}

	public function get_object_groups ($object_section_value, $object_value, $object_type=NULL) {
		return $this->getAllRoles(true);
	}

	public function get_group_children ($root_id=null, $root_name=null, $inclusive=true) {
		return array();
	}

}
