<?php
// Comments Script - coursesweb.net/php-mysql/
// start session (if isn't started), and header for utf-8
if(!isset($_SESSION)) session_start();
if(!headers_sent()) header('Content-type: text/html; charset=utf-8');

        /* Settings for Admin */

// HERE add you data for connecting to MySQL database (MySQL server, user, password, database name)
$mysql['host'] = 'localhost';
$mysql['user'] = 'userdb';
$mysql['pass'] = 'passdb';
$mysql['bdname'] = 'dbname';

// Set administrator login data, and e-mail
define('ADMINNAME', 'admin');                      // the admin name (at least 3 characters)
define('ADMINPASS', 'pass123');                    // the admin password (at least 7 characters)
define('ADMINMAIL', 'contact@domain.net');         // Here add the Administrator e-mail

// If you want to use a GMail account for sending mails to user (Link confirmation, Recovery data)
// set USEGMAIL to 1, and add your GMail Username and Password to GMAILUSER, and GMAILPASS
define('USEGMAIL', 0);
define('GMAILUSER', 'username@gmail.com');
define('GMAILPASS', 'gmailpass');

// If you want to include button Connect with FaceBook,
// Set FBCONN to 1, and add your FaceBook ID Developer (APP ID), and the secret key (APP Secret)
define('FBCONN', 0);
if(FBCONN == 1) {
  define('FBID', 'YOUR_APP_ID');
  define('FBSK', 'YOUR_APP_SECRET');
}

// Value of 1 include button Connect with Yahoo, 0 removes it
define('YHCONN', 1);

// Value of 1 include button Connect with Google, 0 removes it
define('GOCONN', 1);

include('texts.php');             // file with the texts for different languages
$lsite = $en_site;               // Gets the language for site ($en_site for English, $ro_site for Romana)

         /* Settings for User */

// if RANK is 0, the script will send a link to the user's e-mail, to confirm the registration 
// if RANK > 0, the user can log in immediately after registration
define('RANK', 0);
define('ACCOUNT', 1);       // If the value is different from 1, allow to create multiple accounts with same IP

define('ALLOWIMG', 1);                             // allows upload images in message (1), not allow (0)
define('ALLOWMAIL', 1);                            // allows mail notification when new message (1), not allow (0)
define('ROWSPAGE', 12);                            // numbers of messages displayed in the page

// HERE you cand edit the permissions for the image uploded by User
$imguprule = array(
  'dir' => 'usrimgup/',                // directory to store uploded images
  'allowext' => array('gif', 'jpg', 'jpe', 'png'),        // allowed extensions
  'maxsize' => 200,       // maximum allowed size for the image file, in KiloBytes
  'width' => 800,         // maximum allowed width, in pixeli
  'height' => 600         // maximum allowed height, in pixeli
);


     /* From Here no need to modify */

// sets $_SESSION['username'] with the session that your script uses to keep logged users
if(isset($_SESSION['username'])) $nameusr = $_SESSION['username'];

// define directories with files used in this script (BASE to start include)
if(basename(dirname($_SERVER['PHP_SELF']))=='usrincls') {
  define('BASE', '../');
}
else define('BASE', '');
define('USRINCLS', BASE.'usrincls/');                        // classes for register /login
define('USRTEMPL', BASE.'usrtempl/');                        // for templates
define('USRJS', BASE.'usrjs/');                              // for .js files

include('functions.php');             // file with functions
$functions->cleanGP();                       // calls the function to clean data sent via GET or POST
include('class.Base.php');              // the main class from which the others are extended

// values for 'usr=' in URL reserved for page with all users lists
$allusrpg = array('0'=>$lsite['allusr'], 'fb'=>' Facebook', 'yh'=>' Yahoo', 'go'=>' Google');

// define a constant used to check if Ajax request
define('ISAJAX', isset($_REQUEST['isajax']) ? $_REQUEST['isajax'] : 0);
include('class.Users.php');         // Include the Users class