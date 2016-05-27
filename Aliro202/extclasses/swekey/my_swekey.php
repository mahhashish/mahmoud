<?php
/* 
 * Application specific Swekey code
 */

class my_swekey extends swekey {

	// REQUIRED Set up whatever environment is required
	public function __construct () {
		if (!defined('_ALIRO_ABSOLUTE_PATH')) {
			$swekeydir = dirname(__FILE__);
			$extclassesdir = dirname($swekeydir);
			$basedir = dirname($extclassesdir);
			require_once($basedir.'/administrator/aliro.php');
			aliro::getInstance()->startup(false);
			// Should set swekeyCA to null string or to path of CA cert file
			$this->swekeyCA = _ALIRO_ABSOLUTE_PATH.'/extclasses/swekey/musbe-ca.crt';
		}
	}

	//
	// This url is open when the user clicks on the key logo next to the
	// 'user name' field of the login page
	//

	private $swekey_promo_url='http://www.swekey.com?promo=aliro';

	//
	// By default the swekey module uses the http swekey servers
	// If you need more security use the https server (higher response time)
	//

	//private $swekey_check_server='http://auth-check.musbe.net';
	//private $swekey_status_server='http://auth-status.musbe.net';
	//private $swekey_rndtoken_server='http://auth-rnd-gen.musbe.net';

	protected $swekey_check_server='https://auth-check-ssl.musbe.net';
	protected $swekey_status_server='https://auth-status-ssl.musbe.net';
	protected $swekey_rndtoken_server='https://auth-rnd-gen-ssl.musbe.net';

	// This must be a null string or point to an actual CA file
	// Can be initialised statically here, or set in the constructor
	protected $swekeyCA;
	//
	// By default we allow users that have disabled swekeys
	// (less secure but user friendly)
	//

	protected $swekey_allow_disabled=false;

	//
	// By default we allow access after comms failre
	// (less secure but user friendly)
	//

	protected $swekey_allow_commsfail=true;

	//  Enable or disable the random token caching
	//  Because everybody has full access to the cache file, it can be a DOS vulnerability
	//  So disable it if you are running in a non secure enviromnement
	protected $swekeyTokenCacheEnabled = false;

	//
	// By default each user can manage its swekey
	// Set this value to false ifd only the admin can manage the swekeys
	//

	private $swekey_user_managment=true;

	//
	// By default we accept all swekeys
	// A brand is a 8 chars hexacimal numver (upper case)
	// Brands are coma separated
	//

	private $swekey_brands = '';

	private function IsAUserLogged()
	{
		// REQUIRED: return true if a user is logged false otherwise
		return aliroUser::getInstance()->id ? true : false;
	}

	private function LoggedUserSwekeyId()
	{
		// REQUIRED: return the id of the swekey that is attached to the user that is logged
		// if the logged user has no swekey attached retrun ''
		// You should have a request like
		// "SELECT swekey_id from users where id=$logged_user_id";
		$user = aliroUser::getInstance();
		if ($user->id) {
			$database = aliroDatabase::getInstance();
			$database->setQuery("SELECT swekey_id FROM #__users WHERE id = $user->id");
			$id = $database->loadResult();
		}
		return empty($id) ? '' : $id;
	}


	public function SwekeyIntegrationScript()
	{
		// REQUIRED: Comment this line after the first test
		// return "\n<!-- SWEKEY -->\n";

		$params = array();

		$params['swekey_promo_url'] = $this->swekey_promo_url;
		$params['brands'] = $this->swekey_brands;

		// REQUIRED: This is used to find the javascript files and the my-ajax.php file.
		// This value depend on the place you put the swekey directory
		// The path can be relative (swekey/) or absolute (http://wwww.mysite.com/swekey/)
		// Don't forget the trailing slash
		$params['swekey_url'] = aliroCore::getInstance()->getCfg('live_site').'/extclasses/swekey/';

		$isLogged = $this->IsAUserLogged();
		if (empty($isLogged)) // not logged we must customize the login page
		{
			$params['user_logged'] = false;

		    // localized strings
		    $params['str_unplugged'] = T_('No swekey plugged');
		    $params['str_plugged'] = T_('A swekey is plugged');

		    $params['loginname_path'] = '["usrname"]';
		    $params['loginname_resolve_url'] = $params['swekey_url'].'my_ajax.ajax.php?swekey_action=resolve&swekey_id=$swekey_id';

		    // FINE-TUNING: use those two values to move the swekey logo that is next to the user_name Field
		    $params['image_xoffset'] = '1px';
		    $params['image_yoffset'] = '-2px';

		    // FINE-TUNING: use this value if you want to reduce the width of the user_name Field
		    //$params['loginname_width_offset'] = '10';

			// that is the authentication frame that is inserted in the login page
		    $params['authframe_url'] = $params['swekey_url'].'authframe.ajax.php';
			//$params['authframe_url'] .='?check_server='.urlencode($this->swekey_check_server);
			//$params['authframe_url'] .= '&rndtoken_server='.urlencode($this->swekey_rndtoken_server);

			// OPTIONAL: If your application does tricky stuff about sessions like storing them in a DB just add the following line
			// $params['authframe_url'] .= '&use_file=1';
		}
		else
		{
			$params['user_logged'] = true;
			$params['user_swekey_id'] = $this->LoggedUserSwekeyId();
			if(! empty($params['user_swekey_id']))
			{
				// REQUIRED: Fill this value with an url that logs out the current used
				// This page will be loaded when the swekey will be unplugged.
				$params['logout_url'] = aliroCore::getInstance()->getCfg('admin_site').'/index.php?option=logout';
			}
			else
			{
				if ($this->swekey_user_managment)
				{
					$params['attach_url'] = $params['swekey_url'].'my_ajax.ajax.php?swekey_action=attach&swekey_id=$swekey_id';
					$params['session_id'] = session_id();

				    // localized strings
			        $params['str_attach_ask'] = T_("A swekey authentication key has been detected.\nDo you want to associate it with your account ?");
			        $params['str_attach_success'] = T_('The plugged swekey is now attached to your account');
			        $params['str_attach_failed'] = T_('Failed to attach the plugged swekey to your account');
				}
			}
		}

		return $this->Swekey_GetIntegrationScript($params);
	}

	private function UserSwekeyId($user_name)
	{
		// REQUIRED: return the id of the swekey that is attached to the user '$user_name'
		// If the user has no swekey attached return ''
		// You should have a request like
		// "SELECT swekey_id from users where username=$user_name";
		$database = aliroDatabase::getInstance();
		$database->setQuery("SELECT swekey_id FROM #__users WHERE username = '$user_name'");
		return $database->loadResult();
	}

	// This function returns false if the user
	public function SwekeyLoginVerify($user_name)
	{
		$swekey_id = $this->UserSwekeyId($user_name);
		if(! empty($swekey_id))
		{
			if (ereg('^[A-F0-9]{32}$',$swekey_id))
		    {
				if (! $this->IsSwekeyAuthenticated($swekey_id))
		    	{
		    		// OPTIONAL: Output a message like 'Swekey $swekey_id is required to login...';
		    		return false;
		    	}
			}
		}
		return true;
	}

		/**
	 *  Return the text corresponding to the integer status of a key
	 *
	 *  @param  status              The status
	 *  @return                     The text corresponding to the status
	 *  @access public?
	 *  Appears unused - but should be adapted for language handling
	 */
	public function Swekey_GetStatusStr($status)
	{
		switch($status)
		{
	       case SWEKEY_STATUS_OK			: return T_('OK');
	       case SWEKEY_STATUS_NOT_FOUND	    : return T_('Key does not exist in the db');
	       case SWEKEY_STATUS_INACTIVE		: return T_('Key not activated');
	       case SWEKEY_STATUS_LOST			: return T_('Key was lost');
	       case SWEKEY_STATUS_STOLEN		: return T_('Key was stolen');
	       case SWEKEY_STATUS_FEE_DUE		: return T_('The annual fee was not paid');
	       case SWEKEY_STATUS_OBSOLETE		: return T_('Key no longer supported');
	       case SWEKEY_STATUS_REPLACED	    : return T_('This key has been replaced by a backup key');
	       case SWEKEY_STATUS_BACKUP_KEY    : return T_('This key is not plugged in the computer');
	       case SWEKEY_STATUS_UNKOWN    	: return T_('Unknow Status, could not connect to the authentication server');
		}
		return T_('unknown status ').$status;
	}

	protected function commsFailWarning () {
		aliroRequest::getInstance()->setErrorMessage(T_('Swekey validation suffered comms failure'), _ALIRO_ERROR_WARN);
	}

	protected function swekeyErrorCouldNotSetCAFile ($error) {
		$message = T_('SWEKEY_ERROR:Could not set CA file : '.$error);
		$this->logMessage($message);
	}

	protected function swekeyErrorCouldNotFindCAFile ($file, $url) {
		$message = sprintf(T_('SWEKEY_ERROR:Could not find CA file %s getting $s'), $file, $url);
		$this->logMessage($message);
	}

	protected function swekeyErrorGetting ($error, $url) {
	    $message = sprintf(T_('SWEKEY_ERROR:Error %s getting %s'), $error, $url);
		$this->logMessage($message);
	}

	private function logMessage ($message) {
		aliroErrorRecorder::getInstance()->recordError($message, $message, $message);
	}
}
