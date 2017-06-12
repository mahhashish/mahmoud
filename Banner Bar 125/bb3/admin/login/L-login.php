<?php
              if (!isset($_SESSION)) session_start();

require_once('../inc/connect.ini.php');
require_once('../inc/functions.ini.php');

if (isset($_SESSION['loggedin'])) {
	header("Location: ../index.php");
	exit;
} 
 $error = 0;
$postUName = isset( $_POST['username'] )? $_POST['username']: false;
$postUPass = isset( $_POST['password'] )? $_POST['password']: false;
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
      //if (!ereg("^[A-Za-z0-9]", $_POST['username'])){



if (!preg_match ("/^[a-zA-Z0-9]+$/", $postUName)){
          $error = "ILD";
}
      //username taken from what was entered on the form
      $username = $postUName;
      //password the sha1 hash of the password entered on the form
      $password = sha1($postUPass);
      //require('L-users.php');
     // if (array_key_exists($username, $users)) {
          // at this point we know the username exists
          // let's compare the submitted password to value of the array key (the right password)
          if ($username == $login_name && $password == $login_hash) {
              // password is correct
              // Start the session
              if (!isset($_SESSION)) session_start();
              //sha1 username,password and the salt (From L-users.php)
              $_SESSION['loggedin'] = sha1($username . $password . $salt);
              header("Location: ../index.php");
              exit;
          } else {
              $error = "ILD";
              //exit("<p>Invalid password.</p>");
          }
    //  } else {
         // $error = "ILD";
     // }
  }
  //  Show login page
  include('../inc/login.ini.html');
?>