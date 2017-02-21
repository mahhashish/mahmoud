<?php
/**
 * User model definition
 *
 * @author	Jason E. Sweat
 * @since	2003-04-27
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Models
 */
 
 /**
  * local IP subnet for admin validation
  */
 define('USER_LOCAL_SUBNET', '192.168.10.');
 /**
  * loopback address for admin validation
  */
 define('USER_LOCAL_HOST', '127.0.0.1');
 /**
  * hard coded password
  */
 define('USER_ADMIN_PASS', 'letMeIn');
 /**
  * phrase to hash for admin cookie storage
  *
  * never store clear text value in cookies when security is concerned
  * this md5 hash is trivial, but at least a passive deterant
  */
 define('USER_ADMIN_VAL', md5('links application administrator'));
 /**
  * cookie name
  *
  * try to avoid clashes with other GET or POST vars
  */
 define('USER_ADMIN_COOKIE', 'c_links_admin');
 
/**
 * User class defintion
 *
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	Models
 */
class User
{
 	/**
	 * determine if we should consider the user an application admin
	 * @return	boolean
	 */
	function IsAdmin()
	{
		$s_user_ip = $_SERVER['REMOTE_ADDR'];
		$b_admin = false;
		if (	USER_LOCAL_SUBNET == substr($s_user_ip,0,strlen(USER_LOCAL_SUBNET)) 
			||	USER_LOCAL_HOST == substr($s_user_ip,0,strlen(USER_LOCAL_HOST))
			|| (array_key_exists(USER_ADMIN_COOKIE, $_COOKIE) 
				&& USER_ADMIN_VAL == $_COOKIE[USER_ADMIN_COOKIE])
			) {
			$b_admin = true;
		}
		return $b_admin;
	}
	
	/**
	 * drop admin cookie
	 */
	function SetAdmin($psPassCheck)
	{
		if (USER_ADMIN_PASS == $psPassCheck) {
			//Set Cookie for 30 days
			SetCookie(USER_ADMIN_COOKIE
					, USER_ADMIN_VAL
					, time()+30*24*3600
					, ''
					, $_SERVER['HTTP_HOST']
					); 
			return true;
		} else {
			appl_error('Invalid Administrator Password');
			return false;
		}
	}
	
	/**
	 * enforce admin check, and redirect to default page if not admin
	 */
	function ValidateAdmin($psMsg='You have requested an action reserved for application administrators. Access denied.')
	{
		if (!User::IsAdmin()) {
			appl_error($psMsg);
			header(ERROR_VIEW);
			exit;
		}
	}
 }


?>
