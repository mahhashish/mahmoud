<?php
/**
 * Library that provides common functions that are used to help integrating Swekey Authentication in a PHP web site 
 * Version 1.0
 * 
 * History:
 * 1.3 Martin Brampton version for Aliro or other advanced OO systems
 *	   Functions made into methods
 *     Globals made into methods
 *     Utilize autoloading services
 *     Avoid reliance on curl if CMS can provide a service
 * 1.2 Use curl (widely installed) to query the server
 *     Fixed a possible tempfile race attack
 *     Random token cache can now be disabled 
 * 1.1 Added Swekey_HttpGet function that support faulty servers 
 *     Support for custom servers 
 * 1.0 First release  
 *  
 */

// $Id$

/**
 * Errors codes
 */
define ("SWEKEY_ERR_INVALID_DEV_STATUS",901);   // The satus of the device is not SWEKEY_STATUS_OK
define ("SWEKEY_ERR_INTERNAL",902);             // Should never occurd
define ("SWEKEY_ERR_OUTDATED_RND_TOKEN",910);   // You random token is too old
define ("SWEKEY_ERR_INVALID_OTP",911);          // The otp was not correct

/**
 * Those errors are considered as an attack and your site will be blacklisted during one minute
 * if you receive one of those errors     
 */
define ("SWEKEY_ERR_BADLY_ENCODED_REQUEST",920);	
define ("SWEKEY_ERR_INVALID_RND_TOKEN",921);	
define ("SWEKEY_ERR_DEV_NOT_FOUND",922);	

/**
 * Values that are associated with a key.
 * The following values can be returned by the Swekey_GetStatus() function
 */
define ("SWEKEY_STATUS_OK",0);
define ("SWEKEY_STATUS_NOT_FOUND",1);  // The key does not exist in the db
define ("SWEKEY_STATUS_INACTIVE",2);   // The key has never been activated
define ("SWEKEY_STATUS_LOST",3);	   // The user has lost his key
define ("SWEKEY_STATUS_STOLEN",4);	   // The key was stolen
define ("SWEKEY_STATUS_FEE_DUE",5);	   // The annual fee was not paid
define ("SWEKEY_STATUS_OBSOLETE",6);   // The hardware is no longer supported
define ("SWEKEY_STATUS_UNKOWN",201);   // We could not connect to the authentication server

/**
 * Values that are associated with a key.
 * The Javascript Api can also return the following values
 */
define ("SWEKEY_STATUS_REPLACED",100);	 // This key has been replaced by a backup key
define ("SWEKEY_STATUS_BACKUP_KEY",101); // This key is a backup key that is not activated yet
define ("SWEKEY_STATUS_NOTPLUGGED",200); // This key is not plugged in the computer

class swekey {

	/**
	 * The last error of an operation is alway put in this global var
	 */
 
	private $gSwekeyLastError = 0;

	private $gSwekeyLastResult = "<not set>";

	/**
	 *  Insert the plugin and the activex in the page.
	 *  You should not need to include the plugin statically in the page since
	 *  swekey.js creates it dynamically.
	 *  Some browsers may however have trouble inserting dynamic content so you can use this method for those brownsers
	 *  @access public
	 */
	public function Swekey_InsertPlugin()
	{
	    if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE"))
	    {
	        return '<object id="swekey_activex" style="display:none" CLASSID="CLSID:8E02E3F9-57AA-4EE1-AA68-A42DD7B0FADE"></object>'."\n";
	    }
	    else
	    {
	        // do not use display:none beacause the plugin can not be scripted in that case for Firefox
	    	return '<embed type="application/fbauth-plugin" id="swekey_plugin" style="height:0px;width:0px" pluginspage="http://downloads.swekey.com?download_category=installer"/>'."\n";
	    }
	}

	/**
	 *  Return the last error.
	 *
	 *  @return                     The Last Error
	 *  @access public
	 *  No sign this is currently used ?
	 */
	protected function Swekey_GetLastError()
	{
	    return $this->gSwekeyLastError;
	}

	/**
	 *  Return the last result.
	 *
	 *  @return                     The Last Error
	 *  @access public
	 * No sign this is currently used ?
	 */
	protected function Swekey_GetLastResult()
	{
	    return $this->gSwekeyLastResult;
	}

	/**
	 *  Send a synchronous request to the  server.
	 *  This function manages timeout then will not block if one of the server is down
	 *
	 *  @param  url                 The url to get
	 *  @param  response_code       The response code
	 *  @return                     The body of the response or "" in case of error
	 *  @access protected - could be overidden by my_swekey
	 */
	protected function Swekey_HttpGet($url, &$response_code)
	{
	    $this->gSwekeyLastError = 0;
	    $this->gSwekeyLastResult = "<not set>";

		// use curl if available
		if (function_exists('curl_init'))
		{
			$sess = curl_init($url);
			if (substr($url, 0, 8) == "https://")
			{
				$caFileOk = false;
				if (! empty($this->swekeyCA))
				{
					if (file_exists($this->swekeyCA))
					{
						if (! curl_setopt($sess, CURLOPT_CAINFO, $this->swekeyCA))
							$this->swekeyErrorCouldNotSetCAFile(curl_error($sess));
						else
							$caFileOk = true;
					}
					else
						$this->swekeyErrorCouldNotFindCAFile($this->swekeyCA, $url);
				}

				if ($caFileOk)
				{
					curl_setopt($sess, CURLOPT_SSL_VERIFYHOST, '2');
					curl_setopt($sess, CURLOPT_SSL_VERIFYPEER, '2');
				}
				else
				{
					curl_setopt($sess, CURLOPT_SSL_VERIFYHOST, '0');
					curl_setopt($sess, CURLOPT_SSL_VERIFYPEER, '0');
				}

				curl_setopt($sess, CURLOPT_CONNECTTIMEOUT, '20');
				curl_setopt($sess, CURLOPT_TIMEOUT, '20');
			}
			else
			{
				curl_setopt($sess, CURLOPT_CONNECTTIMEOUT, '3');
				curl_setopt($sess, CURLOPT_TIMEOUT, '5');
			}

			curl_setopt($sess, CURLOPT_RETURNTRANSFER, '1');
			$res=curl_exec($sess);
			$response_code = curl_getinfo($sess, CURLINFO_HTTP_CODE);
			$curlerr = curl_error($sess);
			curl_close($sess);

			if ($response_code == 200)
			{
			    $this->gSwekeyLastResult = $res;
			    return $res;
			}

			if (! empty($response_code))
			{
			    $this->gSwekeyLastError = $response_code;
				$this->swekeyErrorGetting($this->gSwekeyLastError." ($curlerr)", $url);
			    return "";
			}

			$response_code = 408; // Request Timeout
			$this->gSwekeyLastError = $response_code;
			$this->swekeyErrorGetting($curlerr, $url);
			return '';
		}
	
		// use pecl_http if available
		if (class_exists('HttpRequest'))
		{
			// retry if one of the server is down
			for ($num=1; $num <= 3; $num++ )
			{
			    $r = new HttpRequest($url);
			    $options = array('timeout' => '3');

				if (substr($url,0, 6) == "https:")
				{
				    $sslOptions = array();
				    $sslOptions['verifypeer'] = true;
				    $sslOptions['verifyhost'] = true;

					$capath = __FILE__;
					$name = strrchr($capath, '/');
					if (empty($name)) // windows
					$name = strrchr($capath, '\\');
					$capath = substr($capath, 0, strlen($capath) - strlen($name) + 1).'musbe-ca.crt';
                
					if (! empty($this->swekeyCA))
                    $sslOptions['cainfo'] = $this->swekeyCA;
                
					$options['ssl'] = $sslOptions;
				}

				$r->setOptions($options);
            
				$reply = $r->send();
				$res = $reply->getBody();
				$info = $r->getResponseInfo();
				$response_code = $info['response_code'];
				if ($response_code != 200)
				{
				    $this->gSwekeyLastError = $response_code;
					$this->swekeyErrorGetting($this->gSwekeyLastError, $url);
				    return '';
				}

				$this->gSwekeyLastResult = $res;
				return $res;
			}
        
			$response_code = 408; // Request Timeout
			$this->gSwekeyLastError = $response_code;
			$this->swekeyErrorGetting($this->gSwekeyLastError, $url);
			return '';
		}
    
		global $http_response_header;
		$res = @file_get_contents($url);
		$response_code = substr($http_response_header[0], 9, 3); //HTTP/1.0
		if ($response_code == 200)
		{
		   $this->gSwekeyLastResult = $res;
		   return $res;
		}
      
		$this->gSwekeyLastError = $response_code;
		$this->swekeyErrorGetting($response_code, $url);
		return '';
	}

	/**
	 *  Get a Random Token from a Token Server
	 *  The RT is a 64 vhars hexadecimal value
	 *  You should better use Swekey_GetFastRndToken() for performance
	 *  @access public
	 *  Apparently not used?
	 */
	protected function Swekey_GetRndToken()
	{
	    return $this->Swekey_HttpGet($this->swekey_rndtoken_server.'/FULL-RND-TOKEN', $response_code);
	}

	/**
	 *  Get a Half Random Token from a Token Server
	 *  The RT is a 64 vhars hexadecimal value
	 *  Use this value if you want to make your own Swekey_GetFastRndToken()
	 *  @access public?  Appears to be used only within swekey
	 */
	protected function Swekey_GetHalfRndToken()
	{
		return $this->Swekey_HttpGet($this->swekey_rndtoken_server.'/HALF-RND-TOKEN', $response_code);
	}

	/**
	 *  Get a Half Random Token
	 *  The RT is a 64 vhars hexadecimal value
	 *  This function get a new random token and reuse it.
	 *  Token are refetched from the server only once every 30 seconds.
	 *  You should always use this function to get half random token.
	 *  @access public? Appears to be used only within swekey
	 */
	protected function Swekey_GetFastHalfRndToken()
	{

	    $res = "";
	    $cachefile = "";

	    // We check if we have a valid RT is the session
	    if (isset($_SESSION['-swekey-rnd-token-date']))
	       if (time() - $_SESSION['-swekey-rnd-token-date'] < 30)
		   	  $res = $_SESSION['-swekey-rnd-token'];
    
		// If not we try to get it from a temp file (PHP >= 5.2.1 only)
		if (strlen($res) != 32 && $this->swekeyTokenCacheEnabled)
		{
	   		$tempdir = '';
			if (function_exists('sys_get_temp_dir'))
	           $tempdir = sys_get_temp_dir();
	        else
	        	$tempdir = '/tmp';
        	
	        if (is_dir($tempdir))
	        {
	            $cachefile = $tempdir."/swekey-rnd-token-".get_current_user();
	            $modif = filemtime($cachefile);
				if ($modif != false)
	                if (time() - $modif < 30)
		            {
		                $res = @file_get_contents($cachefile);
		                if (strlen($res) != 32)
		                    $res = "";
	               		else
	               		{
		           		 	$_SESSION['-swekey-rnd-token'] = $res;
		           		 	$_SESSION['-swekey-rnd-token-date'] = $modif;
			 			}
		            }
		    }
		}
      
		// If we don't have a valid RT here we have to get it from the server
		if (strlen($res) != 32)
		{
			$res = substr($this->Swekey_GetHalfRndToken(), 0, 32);
			$_SESSION['-swekey-rnd-token'] = $res;
			$_SESSION['-swekey-rnd-token-date'] = time();
			if (! empty($cachefile))
			{
				// we unlink the file so no possible tempfile race attack (thanks Thijs)
				unlink($cachefile);
				$file = fopen($cachefile , "x");
				if ($file != FALSE)
				{
					@fwrite($file, $res);
					@fclose($file);
				}
		    }
		}
   
		return $res."00000000000000000000000000000000";
	}

	/**
	 *  Get a Random Token
	 *  The RT is a 64 vhars hexadecimal value
	 *  This function generates a unique random token for each call but call the
	 *  server only once every 30 seconds.
	 *  You should always use this function to get random token.
	 *  @access public
	 */
	public function Swekey_GetFastRndToken()
	{
	    $res = $this->Swekey_GetFastHalfRndToken();
	    if (strlen($res) == 64)
	    {
	    	// Avoid a E_NOTICE when strict is enabled
	    	if (function_exists('date_default_timezone_set'))
	    		date_default_timezone_set(date_default_timezone_get());
    	
	        return substr($res, 0, 32).strtoupper(md5("Musbe Authentication Key".mt_rand().date(DATE_ATOM)));
	    }
    
	    return "";
	}

	/**
	 *  Checks that an OTP generated by a Swekey is valid
	 *
	 *  @param  id                  The id of the swekey
	 *  @param rt                   The random token used to generate the otp
	 *  @param otp                  The otp generated by the swekey
	 *  @return                     true or false
	 *  @access public? Used within swekey
	 */
	protected function Swekey_CheckOtp($id, $rt, $otp)
	{
		$res = $this->Swekey_HttpGet($this->swekey_check_server.'/CHECK-OTP/'.$id.'/'.$rt.'/'.$otp, $response_code);
		return $response_code == 200 && $res == "OK";
	}

	/**
	 *  Checks that an OTP generated by a Swekey for the specified host is valid
	 *
	 *  @param  id                  The id of the swekey
	 *  @param rt                   The random token used to generate the otp
	 *  @param host                 The hostname of the page the otp was calculated
	 *  @param otp                  The otp generated by the swekey
	 *  @return                     true or false
	 *  @access public? Used within swekey
	 */
	protected function Swekey_CheckLinkedOtp($id, $rt, $host, $otp)
	{
		$res = $this->Swekey_HttpGet($this->swekey_check_server.'/CHECK-LINKED-OTP/'.$id.'/'.$rt.'/'.$otp.'/'.$host, $response_code);
		return $response_code == 200 && $res == "OK";
	}

	/**
	 *  Calls Swekey_CheckOtp or Swekey_CheckLinkedOtp depending if we are in
	 *  an https page or not
	 *
	 *  @param  id                  The id of the swekey
	 *  @param rt                   The random token used to generate the otp
	 *  @param otp                  The otp generated by the swekey
	 *  @return                     true or false
	 *  @access public
	 */
	public function Swekey_CheckSmartOtp($id, $rt, $otp)
	{
	    if (! empty($_SERVER['HTTPS']))
	        return $this->Swekey_CheckLinkedOtp($id, $rt, $_SERVER['HTTP_HOST'], $otp);

		return $this->Swekey_CheckOtp($id, $rt, $otp);
	}



	/**
	 *  If your web site requires a key to login you should check that the key
	 *  is still valid (has not been lost or stolen) before requiring it.
	 *  A key can be authenticated only if its status is SWEKEY_STATUS_OK
	 *  @param  id                  The id of the swekey
	 *  @return                     The status of the swekey
	 *  @access protected
	 */
	protected function Swekey_GetStatus($id)
	{
		$res = $this->Swekey_HttpGet($this->swekey_status_server.'/GET-STATUS/'.$id, $response_code);
	    if ($response_code == 200)
	        return intval($res);
 
	    return SWEKEY_STATUS_UNKOWN;
	}

	protected function Swekey_GetIntegrationScript($params)
	{
		$javascript_included = false;

		$output = "\n\n<!-- Swekey Integration Begin -->\n";
		$output .= "<!-- PHP-Integration-Kit 1.0.5.4016 12/01/09 -->\n";

	    if (empty($params['session_id']))
	        $params['session_id'] = '9999';


	 	if (!$params['user_logged'])
		{
			if (! empty($_COOKIE['swekey_disabled_id']))
			{
		        $output .= '<script type="text/javascript">'."\n"
		    		.'document.cookie = "swekey_disabled_id=; path=/;";'."\n"
		            .'</script>'."\n";
			}
		}

	    // We are logged with a swekey
	    if (isset($params['logout_url']) && $params['user_logged'] && strlen($params['user_swekey_id']) == 32)
	    {
			$disabled_swekey = '';
			if (empty($_COOKIE['swekey_disabled_id']))
			{
		    	include_once('swekey.php');
		        $status = $this->Swekey_GetStatus($params['user_swekey_id']);
		        if ($status == SWEKEY_STATUS_INACTIVE || $status == SWEKEY_STATUS_LOST || $status == SWEKEY_STATUS_STOLEN)
		        {
			        $disabled_swekey = $params['user_swekey_id'];
			        $output .= '<script type="text/javascript">'."\n"
			    		.'document.cookie = "swekey_disabled_id='.$params['user_swekey_id'].'; path=/;";'."\n"
			            .'</script>'."\n";
		        }
		        else
		        {
			        $output .= '<script type="text/javascript">'."\n"
			    		.'document.cookie = "swekey_disabled_id=none; path=/;";'."\n"
			            .'</script>'."\n";
		        }
			}
			else
				$disabled_swekey = $_COOKIE['swekey_disabled_id'];

			if ($disabled_swekey != $params['user_swekey_id'])
			{
				if (! $javascript_included)
				{
			    	$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey.js"></script>'."\n";
					$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey_integrate.js"></script>'."\n";
					$javascript_included = true;
				}
		        $output .= '<script type="text/javascript">'."\n"
		            .'swekey_logout_url = "'.$params['logout_url'].'";'."\n"
		            .'swekey_to_check = "'.$params['user_swekey_id'].'";'."\n"
		    		.'document.cookie = "swekey_proposed='.$params['session_id'].'; path=/;";'."\n"   // never propose
		     		.'setTimeout("check_swekey_presence()", 1000);'."\n"
		            .'</script>'."\n";
		    }
	    }

	 // We are logged but we don't use a swekey
	    if (! empty($params['attach_url']) && $params['user_logged'] && empty($params['user_swekey_id']))
	    {
			if (! $javascript_included)
			{
		    	$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey.js"></script>'."\n";
				$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey_integrate.js"></script>'."\n";
				$javascript_included = true;
			}
	        $output .= '<script type="text/javascript">'."\n";
	        $output .= 'swekey_session_id = "'.$params['session_id'].'";'."\n";
	        $output .= 'swekey_attach_url = "'.$params['attach_url'].'";'."\n";
	        if (isset($params['brands']))
	            $output .= 'swekey_brands = "'.$params['brands'].'";'."\n";
	        if (isset($params['str_attach_ask']))
	            $output .= 'swekey_str_attach_ask = "'.$params['str_attach_ask'].'";'."\n";
	        if (isset($params['str_attach_success']))
	            $output .= 'swekey_str_attach_success = "'.$params['str_attach_success'].'";'."\n";
	        if (isset($params['str_attach_failed']))
	            $output .= 'swekey_str_attach_failed = "'.$params['str_attach_failed'].'";'."\n";
	        $output .= 'swekey_propose_to_attach();'."\n";
	        $output .= '</script>'."\n";
		}

		// We are not logged
		if (isset($params['loginname_path']))
		{
			if (! $javascript_included)
			{
		    	$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey.js"></script>'."\n";
				$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey_integrate.js"></script>'."\n";
				$javascript_included = true;
			}
	        $output .= '<script type="text/javascript">'."\n";
	        $output .= 'swekey_artwork_path = "'.$params['swekey_url'].'";'."\n";
	        $output .= 'swekey_loginname_path = '.$params['loginname_path'].';'."\n";
	        if (isset($params['mutltiple_loginnames_input']))
	            $output .= 'swekey_mutltiple_loginnames_input = true;'."\n";
	        if (! empty($params['swekey_promo_url']))
	        {
	     		if (strpos($params['swekey_promo_url'], '://') === false)
	 	            $output .= 'swekey_promo_url = "http://www.swekey.com?promo='.$params['swekey_promo_url'].'";'."\n";
	            else
	    	        $output .= 'swekey_promo_url = "'.$params['swekey_promo_url'].'";'."\n";
	        }
	        else if (! empty($params['promo']))
	            $output .= 'swekey_promo_url = "http://www.swekey.com?promo='.$params['promo'].'";'."\n";
			else
	            $output .= 'swekey_promo_url = "http://www.swekey.com?promo=none";'."\n";
	        if (isset($params['brands']))
	            $output .= 'swekey_brands = "'.$params['brands'].'";'."\n";
	        if (isset($params['loginname_resolve_url']))
	            $output .= 'swekey_loginname_resolve_url = "'.$params['loginname_resolve_url'].'";'."\n";
	       if (isset($params['authframe_url']))
	            $output .= 'swekey_authframe_url = "'.$params['authframe_url'].'";'."\n";
	        if (! empty($params['force_authframe_url']))
	            $output .= 'swekey_force_authframe_url = true;'."\n";
	        if (isset($params['show_unplugged']))
	            $output .= 'swekey_show_unplugged = "'.$params['show_unplugged'].'";'."\n";
	        if (isset($params['image_xoffset']))
	            $output .= 'swekey_image_xoffset = "'.$params['image_xoffset'].'";'."\n";
	        if (isset($params['image_yoffset']))
	            $output .= 'swekey_image_yoffset = "'.$params['image_yoffset'].'";'."\n";
	        if (isset($params['loginname_width_offset']))
	            $output .= 'swekey_loginname_width_offset = "'.$params['loginname_width_offset'].'";'."\n";
	        if (isset($params['str_unplugged']))
	            $output .= 'swekey_str_unplugged = "'.$params['str_unplugged'].'";'."\n";
	        if (isset($params['str_plugged']))
	            $output .= 'swekey_str_plugged = "'.$params['str_plugged'].'";'."\n";

	        $output .= 'swekey_login_integrate();'."\n";

	        $output .= '</script>'."\n";
	    }

		$output .= "<!-- Swekey Integration End -->\n\n";

	    return $output;
	}

	protected function IsSwekeyAuthenticated($swekey_id)
	{
		// delete the cookie
		@setcookie('swekey_dont_verify_'.$swekey_id, "0", time()-60000);
		$delaycount = 0;

		while (16 > $delaycount++) {
		    $ids = $this->GetAuthFrameRes();
		    if (is_array($ids) && in_array($swekey_id, $ids))
		    {
			    return $this->swekey_allow_disabled ? $this->setDisabledAndReturnTrue($swekey_id) : true;
		    }
			usleep(250000);
		}

        $status = $this->Swekey_GetStatus($swekey_id);

	    if ($this->swekey_allow_disabled AND ($status == SWEKEY_STATUS_INACTIVE || $status == SWEKEY_STATUS_LOST || $status == SWEKEY_STATUS_STOLEN))
        {
			return $this->setDisabledAndReturnTrue($swekey_id);
        }

		if (SWEKEY_STATUS_UNKOWN == $status AND $this->swekey_allow_commsfail)
		{
			$this->commsFailWarning();
			return true;
		}

	    return false;
	}

	private function setDisabledAndReturnTrue ($swekey_id) {
       	@setcookie('swekey_disabled_id', $swekey_id, 0, '/');
        return true;
	}

	protected function UnserializeCookie($var)
	{
	    $ar = explode(",", $var);
	    return array
	    (
	        'time' => empty($ar[0]) ? 0 : $ar[0],
	        'session_id' => empty($ar[1]) ? "" : $ar[1],
	        'file_id' => empty($ar[2]) ? 0 : $ar[2]
	    );
	}

	private function GetAuthFrameRes()
	{
		if (isset($_SESSION['swekey_authframe'])) {
			$valid_ids = $_SESSION['swekey_authframe']['valid_ids'];
	    	unset($_SESSION['swekey_authframe']);
			return $valid_ids;
		}
		else return null;
	}

}
