<?php
// Script users (registration /login) - coursesweb.net/php-mysql/

// here add you data for connecting to MySQL database (MySQL server, user, password, database name)
$mysql['host'] = 'localhost';
$mysql['user'] = 'root';
$mysql['pass'] = 'LOflower';
$mysql['bdname'] = 'register_login_script_users_online_v1';

// if $rank is 0, the script will send a link to the user's e-mail, to confirm the registration 
// if $rank > 0, the user can log in immediately after registration
$rank = 2;

// here you cand edit the settings for the image uploded by User
$imgup = array(
  'dir' => 'usersimg/',                // directory where the images will be saved
  'allowext' => array('gif', 'jpg', 'jpe', 'png'),        // allowed extensions
  'maxsize' => 500,       // maximum allowed size for the image file, in KiloBytes
  'width' => 800,         // maximum allowed width, in pixeli
  'height' => 600         // maximum allowed height, in pixeli
);


// start session (if isn't started), and header for utf-8
if(!isset($_SESSION)) session_start();
header('Content-type: text/html; charset=utf-8');

// if "get_magic_quotes_gpc" is activated, delete additional slashes
if(get_magic_quotes_gpc()) { $_POST = array_map("stripslashes", $_POST); }

include('class.Logare.php');         // Include the Logare class
?>