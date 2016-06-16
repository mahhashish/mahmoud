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

class mosUser extends aliroDatabaseRow {
	protected $DBclass = 'aliroDatabase';
	protected $tableName = '#__users';
	protected $rowKey = 'id';

    /**
	 * Fill a user object with information from the current session
	 */
    protected function getSessionData() {
    	// Avoid using aliroRequest here - this will run before it is available
    	$prefix = criticalInfo::getInstance()->isAdmin ? 'admin' : 'user';
    	// Get session to ensure initialisation - don't actually need it - but do need session started
    	// This shouldn't be necessary, but left in just to be sure
    	aliroSessionFactory::getSession();
        $this->id = isset($_SESSION["aliro_{$prefix}id"]) ? (int) $_SESSION["aliro_{$prefix}id"] : 0;
        $this->name = isset($_SESSION["aliro_{$prefix}name"]) ? $_SESSION["aliro_{$prefix}name"] : '';
        $this->username = isset($_SESSION["aliro_{$prefix}username"]) ? $_SESSION["aliro_{$prefix}username"] : '';
        $this->email = isset($_SESSION["aliro_{$prefix}email"]) ? $_SESSION["aliro_{$prefix}email"] : '';
        $this->sendEmail = isset($_SESSION["aliro_{$prefix}sendEmail"]) ? $_SESSION["aliro_{$prefix}sendEmail"] : '';
        $this->usertype = isset($_SESSION["aliro_{$prefix}type"]) ? $_SESSION["aliro_{$prefix}type"] : '';
        $this->gid = isset($_SESSION["aliro_{$prefix}gid"]) ? (int) $_SESSION["aliro_{$prefix}gid"] : 0;
    }

    // Parameter will be ignored - required for consistency with parent class
    public function userStore($password='', $activation='') {
    	$salt = aliroAdminAuthenticator::getInstance()->makeSalt();
    	if ($this->id) {
        	$ret = $this->update();
        	if ($password) {
        		$database = aliroCoreDatabase::getInstance();
        		$database->doSQL("UPDATE #__core_users SET salt = IF(salt='', '$salt', salt), password = MD5(CONCAT(salt, '$password')) WHERE id = $this->id");
        	}
        }
		else {
			$database = aliroCoreDatabase::getInstance();
			$database->doSQL("INSERT INTO #__core_users (password, salt, activation) VALUES (MD5(CONCAT('$salt', '$password')), '$salt', '$activation')");
			$this->id = $database->insertid();
			$ret = $this->insert();
		}
        if ($ret) return true;
        $this->_error = T_('mosUser::store failed');
        return false;
    }

    public function delete($oid=null) {
        if ($oid) $this->id = intval( $oid );
        aliroCoreDatabase::getInstance()->doSQL("DELETE FROM `#__core_users` WHERE `id` = '$this->id'");
        $database = aliroDatabase::getInstance();
        $database->doSQL("DELETE FROM `#__users` WHERE `id` = '$this->id'");
        // cleanup related data from private messaging
        $database->setQuery( "DELETE FROM `#__messages_cfg` WHERE `user_id`='$this->id'" );
        $database->query();
        $database->setQuery( "DELETE FROM `#__messages` WHERE `user_id_to`='{$this->id}'" );
        $database->query();
        return true;
    }

    public function check() {
        if ($this->name == '') $error = T_('Please enter your name');
        elseif ($this->username == '') $error = T_('Please enter a user name');
        elseif (strlen($this->username) < 3 OR preg_match("/[\\<\\>\\\"\\'\\%\\;\\(\\)\\&\\+\\-]/", $this->username)) $error = sprintf(T_('Please enter a valid %s.  No spaces, more than %d characters and containing only the characters 0-9,a-z, or A-Z'), T_('Username'), 2 );
        elseif (($this->email == '') OR preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $this->email ) == 0) $error = T_('Please enter a valid e-mail address');
        else {
            // check for existing username
            $database = aliroDatabase::getInstance();
            $database->setQuery( "SELECT COUNT(id) FROM #__users WHERE LOWER(username)=LOWER('$this->username') AND id!='$this->id'");
            if ($database->loadResult()) $error = T_('This username/password is already in use. Please try another.');
            elseif (aliroCore::get('mosConfig_uniquemail')) {
                // check for existing email
                $database->setQuery( "SELECT COUNT(id) FROM #__users WHERE email='$this->email' AND id!='$this->id'");
                if ($database->loadResult()) $error = T_('This e-mail is already registered. If you forgot the password click on "Password Reminder" and new password will be sent to you.');
            }
        }
        if (isset($error)) {
        	aliroRequest::getInstance()->setErrorMessage($error, _ALIRO_ERROR_FATAL);
        	return false;
        }
        return true;
    }

}

class aliroUser extends mosUser {
	private static $instance = __CLASS__;

    private function __construct () {
		$this->getSessionData();
	}

	private function __clone () {
	    // Enforce singleton
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	public function reset () {
		$this->getSessionData();
	}
}