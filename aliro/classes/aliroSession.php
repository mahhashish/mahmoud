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
 * aliroSession is the abstract class for session objects.  These are stored in
 * the database as a record of the currently active users.  Every user has a
 * session, whether logged in or not (admin side users cannot be anything but
 * logged in except when they are just about to log in).  The same session is
 * preserved across a user log in, so that e.g. a shopping cart will remain
 * intact when the user logs in.  But the session is not preserved across logout
 * as that would prejudice security and tidiness too much (that's my view at
 * present, anyway)
 *
 * aliroUserSession is the concrete class for the user side version of a session.
 *
 * aliroAdminSession is (!) the admin side session class.
 *
 * aliroSessionFactory will instantiate the appropriate kind of session object
 *
 * aliroSessionData is the class that stores session data in the database.
 *
 */

abstract class aliroSession {
	protected static $currentSession = null;
    public $session_id=null;
    public $time=null;
    public $userid=0;
    public $usertype='';
    public $username='';
    public $gid=0;
    public $guest=1;
    protected $_lifetime;
    protected $_newsess = false;

    protected function __construct() {
        $this->time = time();
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
		session_name(md5('aliro_'.$this->_prefix.$this->getip().criticalInfo::getInstance()->absolute_path));
		if (!session_id()) {
		    $sh = aliroSessionData::getInstance();
            session_set_save_handler(array($sh,'sess_open'), array($sh,'sess_close'), array($sh,'sess_read'),
            array($sh,'sess_write'), array($sh,'sess_destroy'), array($sh,'sess_gc'));
		    session_start();
		}
    }

    protected function __clone () {
    	// Enforce singleton
    }

	public function getip() {
	    $ip = false;
	    if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];
	    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
	        if ($ip != false) {
	            array_unshift($ips,$ip);
	            $ip = false;
	        }
	        $count = count($ips);
	        // Exclude IP addresses that are reserved for LANs
	        for ($i = 0; $i < $count; $i++) {
	            if (!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i])) {
	                $ip = $ips[$i];
	                break;
	            }
	        }
	    }
	    if (false == $ip AND isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
	    return $ip;
	}

	public function cookiesAccepted () {
		return isset($_COOKIE['aliroCookieCheck']);
	}

    public function setNew () {
    	$this->_newsess = true;
    }

    // For a session to be valid, we must check it against the sessions table in the database
    protected function checkValidSession () {
       if ($this->session_id = session_id()) {
			// We try to update the time stamp in the matching record of the session table
			$result = $this->updateTime();
			if (!$result) {
                setcookie('aliroCookieCheck', 'A', time()+365*24*60*60, '/');
                $this->saveOrphanData();
			    $this->session_id = '';
			}
			return $result;
        }
        else {
            trigger_error(T_('No session ID found, although aliroSession has been instantiated'));
            return false;
        }
    }

    private function saveOrphanData () {
    	if (isset($_REQUEST['option']) AND ('login' == $_REQUEST['option'] OR 'logout' == $_REQUEST['option'])) return;
		$orphandata['get'] = $_GET;
		$orphandata['post'] = $_POST;
		$orphanstring = base64_encode(serialize($orphandata));
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL("INSERT INTO #__orphan_data VALUES ('$this->session_id', '$orphanstring') ON DUPLICATE KEY UPDATE orphandata = '$orphanstring'");
		setcookie ('aliroOrphanData', $this->session_id, time()+300, '/');
    }

    public function rememberMe ($request) {
    	if (!$this->_newsess) return;
    	$user = aliroUser::getInstance();
		if (0 == $user->id AND $usercookie = isset($_COOKIE['usercookie']) ? $_COOKIE['usercookie'] : null) {
			// Remember me cookie exists. Login with usercookie information if all present.
			if (!empty($usercookie['username']) AND !empty($usercookie['password'])) {
				// If the login is successful, then the session data will be updated
				// In any case, the return will be set either to user data or to null
				$message = aliroUserAuthenticator::getInstance()->systemLogin ($usercookie['username'], $usercookie['password'], 1);
				if ($message) $request->setErrorMessage(T_('Remember Me login failed - incorrect username-password combination'), _ALIRO_ERROR_WARN);
				else $user->reset();
			}
		}
    }

    private function updateTime () {
		if (aliro::getInstance()->installed) {
	     	$database = aliroCoreDatabase::getInstance();
	        $past = $this->time - $this->_lifetime;
	        $database->doSQL("UPDATE #__session SET time = '$this->time', marker = marker+1 WHERE session_id = '$this->session_id' AND isadmin = $this->isadmin AND time > $past");
	        return ($database->getAffectedRows()) ? true : false;
		}
    	return false;
    }

    protected function setSessionData ($my) {
    	$database = aliroCoreDatabase::getInstance();
    	if ($my->id AND !empty($_COOKIE['aliroOrphanData'])) {
    		$database->setQuery("SELECT orphandata FROM #__orphan_data WHERE session_id = '{$_COOKIE['aliroOrphanData']}'");
    		$orphanstring = $database->loadResult();
    		if (!empty($orphanstring)) {
    			$orphandata = unserialize(base64_decode($orphanstring));
    			foreach (array_keys($_GET) as $key) unset($_REQUEST[$key]);
    			foreach (array_keys($_POST) as $key) unset($_REQUEST[$key]);
    			$_GET = $orphandata['get'];
    			$_POST = $orphandata['post'];
    			foreach ($_GET as $key=>$value) $_REQUEST[$key] = $value;
    			foreach ($_POST as $key=>$value) $_REQUEST[$key] = $value;
    			// $database->doSQL("DELETE FROM #__orphan_data WHERE session_id = '{$_COOKIE['aliroOrphanData']}'");
    			setcookie('aliroOrphanData', 'A', time()-7*24*60*60, '/');
    		}
    	}
		session_regenerate_id();
		$this->session_id = session_id();
		$this->httphost = $_SERVER['HTTP_HOST'];
		$this->servername = $_SERVER['SERVER_NAME'];
		$this->ipaddress = getenv('REMOTE_ADDR');
		$_SESSION["aliro_{$this->_prefix}id"] = $this->userid = $my->id;
		$_SESSION["aliro_{$this->_prefix}name"] = $my->name;
		$_SESSION["aliro_{$this->_prefix}username"] = $this->username = $my->username;
		$_SESSION["aliro_{$this->_prefix}email"] = $my->email;
		$_SESSION["aliro_{$this->_prefix}sendEmail"] = $my->sendEmail;
		$_SESSION["aliro_{$this->_prefix}type"] = $this->usertype = $my->usertype;
		$_SESSION["aliro_{$this->_prefix}gid"] = $this->gid = $my->gid;
		$_SESSION["aliro_{$this->_prefix}logintime"] = $this->time = time();
		if (!isset($_SESSION["aliro_{$this->_prefix}state"])) $_SESSION["aliro_{$this->_prefix}state"] 	= array();
        $this->userid = (int) $this->userid;
        $this->gid = (int) $this->gid;
    	$database->insertObject('#__session', $this);
        $this->purge();
    }

    public function purge($timeout=0) {
    	// Note purge only records on the current side - admin or user - because lifetime may be different
		if (aliro::getInstance()->installed) {
	        $past = time() - ($timeout ? $timeout : $this->_lifetime);
	        $database = aliroCoreDatabase::getInstance();
	        $database->setQuery("SELECT session_id, username, isadmin FROM #__session WHERE (time < $past) AND isadmin = $this->isadmin");
	        $expired = $database->loadObjectList();
	        if ($expired) foreach ($expired as $exp) {
	        	$sessions[] = $exp->session_id;
	        	$this->forceLogout ($exp);
	        }
	        if (isset($sessions)) {
	            $sessionlist = implode ("','", $sessions);
	            $database->doSQL("DELETE LOW_PRIORITY FROM `#__session` WHERE session_id IN('$sessionlist')");
	        }
	        aliroSessionData::getInstance()->sess_destroy_orphans();
		}
		if (!$this->isadmin) $this->handleCoreDumps();
    }
	
	protected function handleCoreDumps () {
		$docroot = new aliroDirectory(_ALIRO_ABSOLUTE_PATH);
		$dumps = $docroot->listFiles('^core.');
		if (count($dumps)) {
			$dumpfile = $dumps[0];
			$dump = _ALIRO_ABSOLUTE_PATH.'/'.$dumpfile;
			$f = fopen ($dump, 'rb');
			fseek($f, -3000, SEEK_END);
			$chars = fread($f, 3000);
			$chars = strstr($chars, '/usr/bin/php');
			$chars = trim(preg_replace('/[[:^print:]]/', ' ', $chars));
			$later = strstr($chars, 'SERVER_SIGNATURE=');
			$latesize = strlen($later);
			$later = str_replace(' SERVER', "\nSERVER", $later);
			$chars = substr($chars, 0, -$latesize)."\n".$later."\n";
			$subhead = T_('CORE DUMP ANALYSIS:');
			$chars .= "\n$subhead\n";
			exec('gdb --batch /usr/bin/php '.$dumpfile, $output);
			foreach ($output as $line) $chars .= $line."\n";
			$chars = $dumpfile."\n".$chars;
			aliroFileManager::getInstance()->deleteFile($dump);
			$recorder = aliroErrorRecorder::getInstance();
			$recorder->recordError (T_('CORE_DUMP ').$dumpfile, $dumpfile, $chars);
		}
	}

    protected function forceLogout ($exp) {
    	// Implemented by User Session, not Admin
    }

    public static function isAdminPresent () {
        if (isset($_COOKIE['aliroAdminSession'])) $admin_session = $_COOKIE['aliroAdminSession'];
        else return false;
        $database = aliroCoreDatabase::getInstance();
        $database->setQuery("SELECT COUNT(session_id) FROM #__session WHERE session_id = '$admin_session' AND isadmin = 1");
    	return $database->loadResult() ? true : false;
    }

}

class aliroUserSession extends aliroSession {
	protected $_prefix = 'user';
	public $isadmin = 0;

	protected function __construct () {
		parent::__construct();
		$this->_lifetime = max (aliroCore::getInstance()->getCfg('lifetime'), 300);
	}

    public static function getInstance () {
        if (!is_object(self::$currentSession)) {
            self::$currentSession = new aliroUserSession();
            if (!self::$currentSession->checkValidSession()) {
				// Must be a new visitor
				self::$currentSession->setNew();
				$_SESSION = array();
				self::$currentSession->setNewUserData(new mosUser());
				$_SESSION['aliro_user_session_start'] = @date('Y-M-d H:i:s');
			}
        }
		return self::$currentSession;
    }

    protected function forceLogout ($exp) {
       	if ($exp->username) {
			$loginfo = new aliroLoginDetails($exp->username);
			aliroMambotHandler::getInstance()->trigger('expireLogout', array($loginfo));
       	}
    }

    public function setNewUserData ($my) {
    	if ($this->session_id) aliroCoreDatabase::getInstance()->doSQL("DELETE FROM #__session WHERE session_id = '$this->session_id' AND isadmin = $this->isadmin");
    	$this->setSessionData($my);
    }

	public function logout () {
		$my = new mosUser();
		$stamp = $_SESSION['aliro_user_session_start'];
		$redirect = isset($_SESSION['aliro_redirect_here']) ? $_SESSION['aliro_redirect_here'] : '';
		$_SESSION = array();
		$_SESSION['aliro_user_session_start'] = $stamp;
		$this->setNewUserData($my);
		$request = aliroRequest::getInstance();
		if ($return = $request->getParam($_REQUEST, 'return')) $request->redirect($return);
		else $request->redirect ($redirect);
	}

}

class aliroAdminSession extends aliroSession {
	protected $_prefix = 'admin';
	public $isadmin = 1;

	protected function __construct () {
		parent::__construct();
		$this->_lifetime = max (aliroCore::getInstance()->getCfg('adminlife'), 300);
	}

    public static function getInstance () {
        if (!is_object(self::$currentSession)) {
            self::$currentSession = new aliroAdminSession();
            if (!self::$currentSession->checkValidSession()) {
            	self::$currentSession->logout();
            	$_SESSION = array();
            	setcookie ('aliroAdminSession', 0, time()-7*24*60*60, '/');
            }
        }
		return self::$currentSession;
    }

    public function setNewUserData ($my) {
    	aliroCoreDatabase::getInstance()->doSQL("DELETE FROM #__session WHERE (session_id = '$this->session_id' OR userid = $this->userid) AND isadmin = $this->isadmin");
    	$this->setSessionData($my);
    	setcookie ('aliroAdminSession', $this->session_id, 0, '/');
    }

    public function logout () {
   		if ($adminid = isset($_SESSION['aliro_adminid']) ? (int) $_SESSION['aliro_adminid'] : 0) {
			aliroCoreDatabase::getInstance()->doSQL( "DELETE FROM #__session WHERE isadmin = 1 AND userid='$adminid'");
   		}
   		setcookie ('aliroAdminSession', 0, time()-7*24*60*60, '/');
    	$_SESSION = array();
    }
}

abstract class aliroSessionFactory {

	public static function getSession () {
		if (criticalInfo::getInstance()->isAdmin) return aliroAdminSession::getInstance();
		else return aliroUserSession::getInstance();
	}

}

class aliroSessionData {
    private static $instance = __CLASS__;
    private $db = null;

    private function __construct () {
		if (aliro::getInstance()->installed) $this->db = aliroCoreDatabase::getInstance();
    }

    private function __clone () {
        // Enforce singleton
    }

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

    public function sess_open () {
        // No action required
    }

    public function sess_close () {
        // No action required
    }

    public function sess_read ($session_id) {
        if (isset($_COOKIE['aliroTempSession'])) return base64_decode($_COOKIE['aliroTempSession']);
        if (!isset($_COOKIE['aliroCookieCheck']) OR !isset($this->db)) return '';
        $session_id = $this->db->getEscaped($session_id);
        $this->db->setQuery("SELECT session_data FROM #__session_data WHERE session_id = '$session_id'");
        return base64_decode($this->db->loadResult());
    }

    public function sess_write ($session_id, $session_data) {
        if ((!isset($_COOKIE['aliroCookieCheck']) AND !isset($_COOKIE['usercookie'])) OR !$this->db) {
            if (!headers_sent()) setcookie ('aliroTempSession', base64_encode($session_data), 0, '/');
            return true;
        }
        if (isset($_COOKIE['aliroTempSession'])) setcookie ('aliroTempSession', null, time()-7*24*60*60, '/');
        $session_id = $this->db->getEscaped($session_id);
        $session_data = base64_encode($session_data);
        $this->db->doSQL("INSERT INTO #__session_data (session_id, session_data) VALUES ('$session_id', '$session_data') ON DUPLICATE KEY UPDATE session_data = '$session_data'");
        return true;
    }

    public function sess_destroy ($session_id) {
        setcookie ('aliroTempSession', null, time()-7*24*60*60, '/');
        if (!isset($_COOKIE['aliroCookieCheck']) OR !isset($this->db)) return;
        $session_id = $this->db->getEscaped($session_id);
        $this->db->doSQL("DELETE FROM #__session_data WHERE session_id = '$session_id'");
        return true;
    }

    public function sess_destroy_orphans () {
        if ($this->db AND 42 == mt_rand(0,49)) {
			$this->db->doSQL("DELETE d FROM `#__session_data` AS d LEFT JOIN #__session AS s ON d.session_id = s.session_id WHERE s.session_id IS NULL");
			$this->db->doSQL("OPTIMIZE TABLE `#__session_data`");
			$this->db->doSQL("OPTIMIZE TABLE `#__session`");
		}
    }

    public function sess_gc ($timeout) {
    	$session = aliroSessionFactory::getSession();
    	$session->purge($timeout);
    }

}