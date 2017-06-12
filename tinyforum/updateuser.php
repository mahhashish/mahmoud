<?php
if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if($_id == 0){
	die('NO ID == 0');
}

if(empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"])){
	die('Enter Data');
}

include_once('usersAPI.php');

$user = tinyf_user_get_by_name($_POST["username"]);
if(($user != null) && ($user->id != $_id)){
	tf_db_close();
	die('Name Is Exist');
}

$user = tinyf_user_get_by_email($_POST["email"]);
if(($user != null) && ($user->id != $_id)){
	tf_db_close();
	die('Email Is Exist');
}

$pass = trim($_POST['password']);


$result = tinyf_user_update($_id, trim($_POST["username"]), $pass, trim($_POST["email"]), 0);
tf_db_close();

if($result){
	echo'<pre>';
	print_r($_POST);
	echo'</pre>';	
}


?>