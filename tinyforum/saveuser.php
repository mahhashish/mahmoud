<?php

if(empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"])){
	die('Enter Data');
}

include_once('usersAPI.php');

$user = tinyf_user_get_by_name($_POST["username"]);
if($user != null){
	tf_db_close();
	die('Name Is Exist');
}

$user = tinyf_user_get_by_email($_POST["email"]);
if($user != null){
	tf_db_close();
	die('Email Is Exist');
}

$result = tinyf_user_add(trim($_POST["username"]), trim($_POST["password"]), trim($_POST["email"]), 0);
tf_db_close();

if($result){
	header("location: showuser.php");
}


?>