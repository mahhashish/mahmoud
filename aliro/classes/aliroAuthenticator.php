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
 * aliroLoginDetails is a simple data class used to create an object to carry the
 * information from a user login - user ID, password and the flag for whether the
 * system is to "remember" the user and automatically log them in.  The main use
 * for objects of this class is to pass data to mambots related to the authentication
 * process.
 *
 * aliroExtensionHandler knows all about the various installed extensions in
 * the system.  Anything not integral to the core - components, modules, mambots,
 * templates - are counted as extensions.  It is a cached singleton class and
 * uses common code the implement the object cache.
 *
 * aliroAuthenticator is the abstract class that contains common code for use
 * on both the user and admin sides of Aliro.
 *
 * aliroUserAuthenticator is the class that is instantiated to handle user side
 * authentication - basically login and logout.  On the user side, the actual
 * authentication is done by mambots.  The default Aliro authentication mambot
 * checks the credentials against the database, although it calls back to the
 * aliroUserAuthenticator class to perform the actual validation.  It is possible
 * to supplement the default processing with other mambots, or replace it
 * completely.  Uses for such an approach might include use of an LDAP system.
 * There are several mambot "hooks" and the other purpose for this is to be able
 * to integrate extensions that elaborate the handling of users with additional
 * properties and such like.
 *
 * aliroAdminAuthenticator is instantiated to handle user login and logout for
 * the admin side.  It is simpler, since no mambots are triggered and the
 * only authentication mechanism is a check against the database.
 *
 */

class aliroLoginDetails {
    private $_user = '';
    private $_password = '';
    private $_remember = '';

    public function __construct ($user, $password='', $remember='') {
        $this->_user = $user;
        $this->_password = $password;
        $this->_remember = $remember;
    }

    public function getUser () {
        return $this->_user;
    }

    public function getPassword () {
        return $this->_password;
    }

    public function getRemember () {
        return $this->_remember;
    }

}

abstract class aliroAuthenticator {

	// Not to be called to act on anything other than the current user
	public function logout () {
		if (!empty($_SESSION["aliro_{$this->prefix}id"])) {
			$currentDate = date('Y-m-d/TH:i:s');
			$query = "UPDATE #__users SET lastvisitDate='$currentDate' WHERE id='" . $_SESSION["aliro_{$this->prefix}id"] . "'";
			aliroDatabase::getInstance()->doSQL($query);
		}
		aliroSessionFactory::getSession()->logout();
	}

	public function makePassword ($syllables = 3) {
		// Developed from code by http://www.anyexample.com
		// 8 vowel sounds 
		$vowels = array ('a', 'o', 'e', 'i', 'y', 'u', 'ou', 'oo'); 
		// 20 random consonants 
		$consonants = array ('w', 'r', 't', 'p', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'qu');
		// Generate three syllables
		for ($i=0, $password=''; $i<$syllables; $i++) $password .= $this->makeSyllable($vowels, $consonants, $i);
		// Return with suffix added
		return $password.$this->makeSuffix($vowels, $consonants);
	}

	private function makeSuffix ($vowels, $consonants) {
		// 10 random suffixes
		$suffix = array ('dom', 'ity', 'ment', 'sion', 'ness', 'ence', 'er', 'ist', 'tion', 'or');
		$new = $suffix[array_rand($suffix)];
		// return suffix, but put a consonant in front if it starts with a vowel
		return (in_array($new[0], $vowels)) ? $consonants[array_rand($consonants)].$new : $new;
	}

	private function makeSyllable ($vowels, $consonants, $double=false) {
		$doubles = array('n', 'm', 't', 's');
		$c = $consonants[array_rand($consonants)];
		// One in three chance of doubling the consonant - except for first syllable
		if ($double AND in_array($c, $doubles) AND 1 == mt_rand(0,2)) $c .= $c;
		return $c.$vowels[array_rand($vowels)];
	}
	
	public function makeSalt () {
		return $this->makeRandomString(24);
	}
	
	private function makeRandomString ($length=8) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!%,-:;@_{}~";
		for ($i = 0, $makepass = '', $len = strlen($chars); $i < $length; $i++) $makepass .= $chars[mt_rand(0, $len-1)];
		return $makepass;
	}

}

class aliroUserAuthenticator extends aliroAuthenticator {
	private static $instance = __CLASS__;
	protected $prefix = 'user';

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	public function userLogin () {
		$request = aliroRequest::getInstance();
		$username = $request->getParam($_POST, 'username');
		$passwd = $request->getParam($_POST, 'passwd');
		$remember = $request->getParam($_REQUEST, 'remember');
		if (!$username OR !$passwd) {
			$message = T_('Please complete the username and password fields.');
			$request->redirectSame($message, _ALIRO_ERROR_WARN);
			exit;
		}
		$message = $this->systemLogin ($username, $passwd, $remember);
		if ($message) $request->redirectSame ($message, _ALIRO_ERROR_WARN);
		if ($return = $request->getParam($_REQUEST, 'return')) $request->redirect($return);
		elseif (isset($_SESSION['aliro_redirect_here'])) $request->redirect ($_SESSION['aliro_redirect_here']);
		else $request->redirect();
	}

	function systemLogin ($username=null, $passwd=null, $remember=null) {
		$session = aliroSessionFactory::getSession();
		if (!$session->cookiesAccepted()) return T_('Your browser is not accepting cookies - login is not possible.');
		$my = null;
		$mambothandler = aliroMambotHandler::getInstance();
		$database = aliroDatabase::getInstance();
		$username = $database->getEscaped($username);
		$escpasswd = $database->getEscaped($passwd);
		$remember = $remember ? true : false;
		$loginfo = new aliroLoginDetails($username, $escpasswd, $remember);
		$checkuser = true;
		$logresults = $mambothandler->trigger('requiredLogin',array($loginfo));
		$message = '';
		if (count($logresults) == 0) $logresults[] = T_('Logins are not permitted.  There is no authentication check active.');
		foreach ($logresults as $result) {
			if (($result instanceof mosUser) AND $result->id) {
				if (!isset($my)) $my = $result;
			}
			elseif ($result) {
				$message = $result;
				$checkuser = false;
				break;
			}
		}
		if ($checkuser AND isset($my)) {
			$session->setNewUserData($my);
			$mambothandler->trigger('goodLogin', array($loginfo));
			$currentDate = date("Y-m-d/TH:i:s");
			$query = "UPDATE #__users SET lastvisitDate='$currentDate', block=0 where id='$my->id'";
			if ($remember) {
				$lifetime = time() + 365*24*60*60;
				setcookie("usercookie[username]", $username, $lifetime, "/");
				setcookie("usercookie[password]", $passwd, $lifetime, "/");
			}
		}
		else {
			$my = null;
			$query = "UPDATE #__users SET block=block+1 where username='$username'";
			if ($remember) {
				$lifetime = time() - 365*24*60*60;
				setcookie("usercookie[username]", $username, $lifetime, "/");
				setcookie("usercookie[password]", $passwd, $lifetime, "/");
			}
		}
		$database->doSQL($query);
		if (is_null($my)) {
			$mambothandler->trigger('badLogin', array($loginfo));
			sleep(2);
		}
		return $message;
	}

	public function logout () {
		$mambothandler = aliroMambotHandler::getInstance();
		$loginfo = new aliroLoginDetails($_SESSION['aliro_username']);
		$mambothandler->trigger('beforeLogout', array($loginfo));
		parent::logout();
	}

	function authenticate (&$message, &$my, $username, $passwd, $remember=null) {
		$message = '';
		$database = aliroDatabase::getInstance();
		$my = new mosUser();
		$database->setQuery( "SELECT id, gid, block, name, username, email, sendEmail, usertype FROM #__users WHERE username='$username'");
		if ($database->loadObject($my)) {
			if ($my->block > 10) {
				$message = T_('Your login has been blocked. Please contact the administrator.');
				return false;
			}
			$database = aliroCoreDatabase::getInstance();
			$database->setQuery("SELECT COUNT(*) FROM #__core_users WHERE id=$my->id  AND password=MD5(CONCAT(salt,'$passwd'))");
			if ($database->loadResult()) {
				unset($my->block);
				return true;
			}
		}
		$message = T_('Incorrect username or password. Please try again.');
		return false;
	}

}

class aliroAdminAuthenticator extends aliroAuthenticator {
	private static $instance = __CLASS__;
	protected $prefix = 'admin';

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	function login () {
		$session = aliroSessionFactory::getSession();
		if (!($session->cookiesAccepted())) return null;
		
		$database = aliroDatabase::getInstance();
		/** escape and trim to minimise injection of malicious sql */
		$request = aliroRequest::getInstance();

		$usrname = $database->getEscaped($request->getParam($_POST, 'usrname'));
		$pass = $database->getEscaped($request->getParam($_POST, 'pass'));

		$my = null;
		if (!$pass) {
			$request->setErrorMessage(T_('Please enter a password'), _ALIRO_ERROR_WARN);
			return $my;
		}

		$users = $database->doSQLget("SELECT * FROM #__users WHERE usertype IN ('Administrator', 'Super Administrator') OR (username='$usrname' AND block<=10)");
		$admins = count($users);
		$database = aliroCoreDatabase::getInstance();
		foreach ($users as $key=>$oneuser) {
			if ($oneuser->username == $usrname) {
				$database->setQuery("SELECT COUNT(*) FROM #__core_users WHERE id=$oneuser->id  AND password=MD5(CONCAT(salt,'$pass'))");
				if ($database->loadResult()) {
					$my =& $users[$key];
					if (!in_array($my->usertype, array('Administrator', 'Super Administrator'))) $admins--;
				}
			}
		}
		if ($admins == 0) {
			$request->setErrorMessage(T_('You cannot login. There are no administrators set up.'), _ALIRO_ERROR_FATAL);
			return null;
		}
		if (isset($my)) {
			$session->setNewUserData ($my);
			$currentDate = date("Y-m-d/TH:i:s");
			$query = "UPDATE #__users SET lastvisitDate='$currentDate', block=0 where id='$my->id'";
		}
		else {
			$request->setErrorMessage(T_('Incorrect Username, Password, or Access Level.  Please try again'), _ALIRO_ERROR_WARN);
			$query = "UPDATE #__users SET block=block+1 where username='$usrname'";
			sleep(2);
		}
		$database->doSQL("OPTIMIZE TABLE #__error_log, #__session, #__session_data");
		$database = aliroDatabase::getInstance();
		$database->doSQL($query);
		return $my;
	}

	function logout () {
		parent::logout();
		$request = aliroRequest::getInstance();
		$request->redirect($request->getCfg('live_site'));
	}

}