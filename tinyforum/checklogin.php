<?php
include_once('usersAPI.php');
include_once('session.php');

if( empty($_POST["username"]) || empty($_POST["password"]) ){
    tf_db_close();
	die('Enter Data');
}

$user = tinyf_user_get_by_name($_POST["username"]);

if(!$user){
    tf_db_close();
	die("Bad User");
}

$pass = md5(mysql_real_escape_string(strip_tags($_POST["password"]), $tf_handle));

if(strcmp($pass, $user->password) != 0){
    tf_db_close();
    die("Bad User");
}

$user->password = '';

$_SESSION['user_info'] = $user;  

header("location: showforum.php");

?>