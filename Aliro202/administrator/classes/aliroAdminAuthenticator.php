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
 * aliroAdminAuthenticator is instantiated to handle user login and logout for
 * the admin side.  It is simpler, since no mambots are triggered and the
 * only authentication mechanism is a check against the database.
 *
 */

final class aliroAdminAuthenticator extends aliroAuthenticator {
	private static $instance = __CLASS__;
	protected $prefix = 'admin';

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	public function login () {
		$session = aliroSession::getSession();
		if (!($session->cookiesAccepted())) return null;

		$database = aliroDatabase::getInstance();
		/** escape and trim to minimise injection of malicious sql */
		$request = aliroRequest::getInstance();

		$usrname = $database->getEscaped($request->getParam($_POST, 'usrname'));
		$pass = $database->getEscaped($request->getParam($_POST, 'pass'));

		$my = null;
		$swekey = new my_swekey();
		if (false AND !$swekey->SwekeyLoginVerify($usrname)) // $user_name should contains the name of the user that want to login
		{
		    // Do whatever you should do to tell that login failed
			$request->setErrorMessage(T_('Swekey is required but not present'), _ALIRO_ERROR_WARN);
			return $my;
		}
		if (!$pass) {
			$request->setErrorMessage(T_('Please enter a password'), _ALIRO_ERROR_WARN);
			return $my;
		}

		$users = $database->doSQLget("SELECT * FROM #__users WHERE usertype IN ('Administrator', 'Super Administrator') OR (username='$usrname' AND block<=10)", 'aliroCurrentUser');
		$admins = count($users);
		$database = aliroCoreDatabase::getInstance();
		foreach ($users as $key=>$oneuser) {
			if ($oneuser->username == $usrname) {
				$database->setQuery("SELECT COUNT(*) FROM #__core_users WHERE id=$oneuser->id  AND password=MD5(CONCAT(salt,'$pass'))");
				if ($database->loadResult()) {
					if (in_array($oneuser->usertype, array('Administrator', 'Super Administrator'))) $my = $users[$key];
					else $admins--;
				}
			}
		}
		if ($admins == 0) {
			$request->setErrorMessage(T_('You cannot login. There are no administrators set up.'), _ALIRO_ERROR_FATAL);
			return null;
		}
		if (isset($my)) {
			$session->recoverOrphanData($my->id);
			if (isset($_SESSION['_aliro_orphan_data'])) {
	   			foreach (array_keys($_POST) as $key) unset($_REQUEST[$key]);
	   			foreach (array_keys($_GET) as $key) unset($_REQUEST[$key]);
	   			$_POST = $_SESSION['_aliro_orphan_data']['post'];
	   			$_GET = $_SESSION['_aliro_orphan_data']['get'];
	   			foreach ($_POST as $key=>$value) $_REQUEST[$key] = $value;
	   			foreach ($_GET as $key=>$value) $_REQUEST[$key] = $value;
	   			unset($_SESSION['_aliro_orphan_data']);
			}
			$session->setNewUserData ($my);
			$currentDate = date("Y-m-d H:i:s");
			$query = "UPDATE #__users SET lastvisitDate='$currentDate', block=0 where id='$my->id'";
		}
		else {
			$session->logout(true);
			$request->setErrorMessage(T_('Incorrect Username, Password, or Access Level.  Please try again'), _ALIRO_ERROR_WARN);
			$query = "UPDATE #__users SET block=block+1 where username='$usrname'";
			sleep(2);
		}
		$database->doSQL("OPTIMIZE TABLE #__error_log, #__session, #__session_data");
		$database = aliroDatabase::getInstance();
		$database->doSQL($query);
		return $my;
	}

	public function logout () {
		parent::logout();
		$request = aliroRequest::getInstance();
		$request->redirect($request->getCfg('live_site'));
	}

}