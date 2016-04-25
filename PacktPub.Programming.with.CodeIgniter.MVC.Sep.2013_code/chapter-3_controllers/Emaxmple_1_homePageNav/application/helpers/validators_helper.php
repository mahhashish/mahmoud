<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('isValidEmail')) {
	function isValidEmail($email){
	return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	}
}

if (!function_exists('isValidURL')) {
	function isValidURL($url)
	{
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}
}

if (!function_exists('isURLExists')) {
 function isURLExists($url)
{
 $handle = curl_init($url);
 curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
 /* Get the HTML or whatever is linked in $url. */
 $response = curl_exec($handle);
 /* Check for 404 (file not found). */
 $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
 if($httpCode == 404) {
    return FALSE; 
 }
// its ok! 
   return TRUE; 
 }
}






