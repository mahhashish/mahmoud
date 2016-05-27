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
 * mosUser has kept its old name because of the extent that it has become embedded
 * in the system, including the new Role Based Access Control mechanism, and also
 * because aliroUser exists with a different purpose (see below).  It is
 * the user object that knows the basics about an Aliro user.  It could well be
 * extended to have more functionality and to know more.  Right now it is rather
 * feeble.
 *
 * aliroUser is a singleton class that embeds the mosUser object referring to
 * the currently active user - that is to say the person on the browser whose
 * request we are handling.  Any class wanting to obtain access to the current
 * user can get the instance of aliroUser.
 *
 */


/**
* Users Table Class
*
* Aliro
*/

class aliroCustomUser extends aliroDatabaseRow {
	protected static $staticDBclass = 'aliroDatabase';
	protected static $staticTableName = '#__custom_users';
	protected $DBclass = '';
	protected $tableName = '';
	protected $rowKey = 'id';
	
	public function __construct () {
		$this->tableName = self::$staticTableName;
		$this->DBclass = self::$staticDBclass;
	}
	
	public static function checkExists () {
		$database = call_user_func (array(self::$staticDBclass, 'getInstance'));
		return $database->tableExists(self::$staticTableName);
	}
	
	public static function checkProperty ($property) {
		$database = call_user_func (array(self::$staticDBclass, 'getInstance'));
		return $database->fieldExists(self::$staticTableName, $property);
	}
	
	public static function deleteUsers ($idlist) {
		$database = call_user_func (array(self::$staticDBclass, 'getInstance'));
		$table = self::$staticTableName;
		if ($database->tableExists($table)) {
			$database->doSQL("DELETE FROM $table WHERE id IN ($idlist)");
		}
	}
}

class aliroAnyUser extends aliroDatabaseRow {
	protected $DBclass = 'aliroDatabase';
	protected $tableName = '#__users';
	protected $rowKey = 'id';
	public $id = 0;
	public $gid = 0;
	
	protected $activation = '';
	protected $customuser = null;
	
	public function __get ($property) {
		return isset($this->$property) ? $this->$property : 
			($this->checkCustomUser($property) AND isset($this->customuser->$property))
			? $this->customuser->$property : null;
	}
	
	public function __set ($property, $value) {
		if ($this->checkCustomUser($property)) $this->customuser->$property = $value;
		else $this->$property = $value;
	}
	
	protected function checkCustomUser ($property) {
		if (aliroCustomUser::checkProperty($property)) {
			$this->loadCustomUser();
			return true;
		}
		return false;
	}
	
	protected function loadCustomUser () {
		if (aliroCustomUser::checkExists()) {
			if (!($this->customuser instanceof aliroCustomUser)) {
				$this->customuser = new aliroCustomUser();
				if (!empty($this->id)) $this->customuser->load($this->id);
			}
			return true;
		}
		return false;
	}
	
	public function load ($key=null) {
		return $this->returnWithoutAvatar(parent::load($key));
	}
	
	protected function returnWithoutAvatar ($result) {
		unset($this->avatar);
		return $result;
	}

    public function userStore ($password='') {
		if (!$this->check()) return false;
		unset($this->avatar);
    	$salt = aliroAuthenticator::makeSalt();
    	if ($this->id) {
			$this->fixUserNames();
        	$ret = $this->update(false);
			if (aliroUser::getInstance()->id == $this->id) aliroSession::getSession()->setSessionData($this);
        	if ($password) {
        		$database = aliroCoreDatabase::getInstance();
        		$database->doSQL("UPDATE #__core_users SET salt = IF(salt='', '$salt', salt), password = MD5(CONCAT(salt, '$password')) WHERE id = $this->id");
        	}
        }
		else {
			$database = aliroCoreDatabase::getInstance();
			$this->registerDate = $database->dateNow();
			$database->doSQL("INSERT INTO #__core_users (password, salt, activation) VALUES (MD5(CONCAT('$salt', '$password')), '$salt', MD5(CONCAT('$password', '$salt')))");
			$this->id = $database->insertid();
			$this->fixUserNames();
			$this->activation = md5($password.$salt);
			$ret = $this->insert();
		}
        if ($ret) {
			if ($this->customuser instanceof aliroCustomUser) $this->customuser->storeNonAuto();
        	$this->storeNonAuto();
        	return $this->id;
        }
        aliroRequest::getInstance()->setErrorMessage(T_('aliroAnyUser::store failed'), _ALIRO_ERROR_SEVERE);
        return false;
    }
	
	public function bind ($objectorarray, $ignore='', $strip=true, $purify=true) {
		parent::bind($objectorarray, $ignore, $strip, $purify);
		if ($this->loadCustomUser()) $this->customuser->bind($objectorarray, $ignore, $strip, $purify);
	}
	
	public function getActivation () {
		return $this->activation;
	}
	
	public function updateEmail ($newemail) {
		if (!self::validateEmail($newemail) OR !self::checkEmailUnique ($newemail, $this->id)) return false;
		$handler = aliroMambotHandler::getInstance();
		$notifiers = $handler->trigger('onUserEmailChange', array($this, $newemail));
		if (count($notifiers) AND !array_product($notifiers)) {
			$handler->trigger('onUserEmailUndoChange', $this);
			return false;
		}
		$database = aliroDatabase::getInstance();
		$escemail = $database->getEscaped($this->email);
		$database->doSQL("INSERT INTO #__users_former_emails (user_id, email, changed) VALUES($this->id, '$escemail', NOW())
			ON DUPLICATE KEY UPDATE changed = NOW()");
		$this->email = $newemail;
		$escnewemail = $database->getEscaped($newemail);
		$database->doSQL("UPDATE #__users SET email = '$escnewemail' WHERE id = $this->id");
		$database->doSQL("DELETE m FROM #__users_former_emails AS m INNER JOIN #__users AS u ON m.email = u.email");
		return true;
	}
	
	public function mergeUserIn ($userid) {
		$merger = new aliroAnyUser();
		if ($merger->load($userid)) {
			$database = aliroDatabase::getInstance();
			$database->doSQL("UPDATE #_users_former_emails SET user_id = $this->id WHERE user_id = $userid");
			$escemail = $database->getEscaped($merger->email);
			$database->doSQL("DELETE m FROM #__users_former_emails AS m INNER JOIN #__users AS u ON m.email = u.email");
			$database->doSQL("INSERT INTO #__users_former_emails (user_id, email, changed) VALUES($this->id, '$escemail', NOW())");
			aliroMambotHandler::getInstance()->trigger('onMergeUserIn', array($this, $userid));
			self::deleteUsers(array($userid));
			return true;
		}
		return false;
	}
	
	public function updateAvatar () {
		if (!empty($_FILES['avatar']['tmp_name'])) {
			$files = aliroFileManager::getInstance()->makeUploadSafe('avatar', true);
			// Only really use the first item returned, should not be multiple uploads in this case
			foreach ($files as $file) {
				$image = new aliroImage($file);
				$image->imgresize(80,80);
				if ($image->saveAs($file)) {
					$avatar = file_get_contents($file);
					$avatype = $image->getType();
				}
				$dir = new aliroDirectory(dirname($file));
				$dir->deleteAll();
				break;
			}
		}
		elseif (aliroRequest::getInstance()->getParam($_POST, 'avatarreset', 0)) $avatar = $avatype = '';
		if (isset($avatar)) {
			$database = $this->getDatabase();
			$avatar = $database->getEscaped($avatar);
			$database->doSQL("UPDATE #__users SET avatar = '$avatar', avatype = '$avatype' WHERE id = $this->id");
		}
	}
	
	protected function fixUserNames () {
		if ($this->username AND preg_match('/^user[0-9]+$/', $this->username)) $this->username = '';
		if ($this->username AND false !== strpos($this->username, '@')) $this->username = '';
		if (!$this->username) $this->username = 'user'.(string) $this->id;
		if (!$this->name) $this->name = 'User '.(string) $this->id;
	}
    
    public function mailUserFromSystem ($subject, $body) {
    	$core = aliroCore::getInstance();
    	$mail = new aliroMailMessage ($core->getCfg('mailfrom'), $core->getCfg('fromname'));
    	$mail->sendMail ($this->email, $subject, $body);
    }

    public function delete($oid=null) {
        if ($oid) $this->id = intval( $oid );
        aliroAnyUser::deleteUsers((array) $this->id);
        return true;
    }

    public function check() {
        if ($this->email == '') $error = T_('Please enter an address');
        elseif (!aliroAnyUser::checkEmailUnique ($this->email, $this->id)) {
            $error = T_('This e-mail is already registered. If you forgot the password click on "Password Reset" and new password will be sent to you.');
        }
        if (isset($error)) {
        	aliroRequest::getInstance()->setErrorMessage($error, _ALIRO_ERROR_SEVERE);
        	return false;
        }
        return true;
    }
    
    public function isAdmin () {
    	if ($this->id) {
    		$roles = aliroAuthoriser::getInstance()->getAccessorRoles('aUser', $this->id);
    		if (in_array('Super Administrator', $roles)) return true;
    	}
    	return false;
    }
    
    public function isLogged () {
    	$database = aliroCoreDatabase::getInstance();
    	$database->setQuery("SELECT COUNT(*) FROM #__session WHERE isadmin = 0 AND userid = ".intval($this->id));
    	return $database->loadResult() ? true : false;
    }
    
    public function loadByUsername ($username) {
		$database = $this->getDatabase();
		$username = $database->getEscaped($username);
		$database->setQuery("SELECT * FROM $this->tableName WHERE username = '$username'" );
		return $this->returnWithoutAvatar($database->loadObject($this));
    }
    
	public function loadByEmail ($email) {
		$database = $this->getDatabase();
		$email = $database->getEscaped($email);
		$database->setQuery("SELECT * FROM $this->tableName WHERE email = '$email'" );
		return $this->returnWithoutAvatar($database->loadObject($this));
    }
    
    public static function makeUserByEmail ($email) {
    	$user = new aliroAnyUser();
    	return ($user->loadByEmail($email) ? $user : null);
    }
    
    private static function deleteUsers ($userids) {
    	foreach ($userids as $key=>$userid) {
    		if ($userid) $userids[$key] = intval($userid);
    		else unset($userids[$key]);
    	}
		aliroMambotHandler::getInstance()->trigger('onDeleteUsers', $userids);
    	$userlist = implode(',', $userids);
    	if (!$userlist) return;
        aliroCoreDatabase::getInstance()->doSQL("DELETE FROM `#__core_users` WHERE `id` IN ($userlist)");
        $database = aliroDatabase::getInstance();
        $database->doSQL("DELETE FROM `#__users` WHERE `id` IN ($userlist)");
        // cleanup related data from private messaging
        $database->setQuery( "DELETE FROM `#__messages_cfg` WHERE `user_id` IN ($userlist)" );
        $database->query();
        $database->setQuery( "DELETE FROM `#__messages` WHERE `user_id_to` IN ($userlist)" );
        $database->query();
		aliroCustomUser::deleteUsers($userlist);
    }
    
    public static function purgeRegistrations () {
    	$database = aliroDatabase::getInstance();
    	$database->setQuery("SELECT id FROM #__users WHERE lastvisitDate = '0000-00-00 00:00:00' AND registerDate < SUBDATE(NOW(),14)");
    	$items = $database->loadResultArray();
    	if ($items) aliroAnyUser::deleteUsers($items);
    }

	public static function checkEmailUnique ($email, $id=0) {
		$database = aliroDatabase::getInstance();
		$email = $database->getEscaped($email);
		$sql = "SELECT COUNT(*) FROM #__users WHERE email='$email'";
		if ($id) $sql .= ' AND id != '.intval($id);
		$database->setQuery($sql);
		if ($database->loadResult()) return false;
		$sql = "SELECT COUNT(*) FROM #__users_former_emails WHERE email='$email'";
		if ($id) $sql .= ' AND user_id != '.intval($id);
		$database->setQuery($sql);
		return $database->loadResult() ? false : true;
	}

	public static function checkUsernameUnique ($username, $id=0) {
		$database = aliroDatabase::getInstance();
		$username = $database->getEscaped($username);
		$sql = "SELECT COUNT(*) FROM #__users WHERE username='$username'";
		if ($id) $sql .= ' AND id != '.intval($id);
		$database->setQuery($sql);
		return $database->loadResult() ? false : true;
	}

	public static function validateEmail ($email) {
		$atpos = strrpos($email, "@");
		if ($atpos) {
			$domain = substr($email,$atpos+1);
			if ($domain AND (checkdnsrr($domain, 'MX') OR checkdnsrr($domain, 'A'))) return true;
		}
		return false;
	}

}

// An alternative name for the user class
final class mosUser extends aliroAnyUser {
	
	public static function checkEmailUnique ($email, $id=0) {
		return aliroAnyUser::checkEmailUnique($email, $id);
	}
	
}

class aliroCurrentUser extends aliroAnyUser {

    /**
	 * Fill a user object with information from the current session
	 *
    protected function getSessionData() {
    	// Avoid using aliroRequest here - this will run before it is available
    	$prefix = _ALIRO_IS_ADMIN ? 'admin' : 'user';
    	// Get session to ensure initialisation - don't actually need it - but do need session started
    	// This shouldn't be necessary, but left in just to be sure
    	aliroSession::getSession();
        $this->id = isset($_SESSION["aliro_{$prefix}id"]) ? (int) $_SESSION["aliro_{$prefix}id"] : 0;
        $this->name = isset($_SESSION["aliro_{$prefix}name"]) ? $_SESSION["aliro_{$prefix}name"] : '';
        $this->username = isset($_SESSION["aliro_{$prefix}username"]) ? $_SESSION["aliro_{$prefix}username"] : '';
        $this->email = isset($_SESSION["aliro_{$prefix}email"]) ? $_SESSION["aliro_{$prefix}email"] : '';
        $this->sendEmail = isset($_SESSION["aliro_{$prefix}sendEmail"]) ? $_SESSION["aliro_{$prefix}sendEmail"] : '';
        $this->usertype = isset($_SESSION["aliro_{$prefix}type"]) ? $_SESSION["aliro_{$prefix}type"] : '';
        $this->ipaddress = isset($_SESSION["aliro_{$prefix}ipaddress"]) ? $_SESSION["aliro_{$prefix}ipaddress"] : '';
        $this->gid = isset($_SESSION["aliro_{$prefix}gid"]) ? (int) $_SESSION["aliro_{$prefix}gid"] : 0;
        $this->countrycode = isset($_SESSION["aliro_{$prefix}countrycode"]) ? $_SESSION["aliro_{$prefix}countrycode"] : '';
    }
	 * 
	 */

	public function isLogged () {
    	return $this->id ? true : false;
    }
}

// A singleton class to hold the current user 
final class aliroUser extends aliroCurrentUser {
	private static $instance = null;

    private function __construct () {
		// $this->getSessionData();
	}

	private function __clone () {
	    // Enforce singleton
	}

	public static function getInstance () {
		if (self::$instance instanceof aliroCurrentUser) return self::$instance;
		self::$instance = new aliroCurrentUser();
    	$prefix = _ALIRO_IS_ADMIN ? 'admin' : 'user';
		if (!empty($_SESSION["aliro_{$prefix}_user_object"])) {
			$sessionuser = unserialize($_SESSION["aliro_{$prefix}_user_object"]);
			if ($sessionuser instanceof aliroCurrentUser) self::$instance->bind($sessionuser);
		}
		if (empty(self::$instance->id)) self::$instance->id = 0;
		return self::$instance;
	    // return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}

    public static function reset () {
		self::$instance = null;
	}


}
