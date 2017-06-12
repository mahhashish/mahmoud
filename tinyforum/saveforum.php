<?php

if( empty($_POST["title"]) || empty($_POST["desc"]) ){
	die('Enter Data');
}

include_once('forumAPI.php');

$result = tinyf_forum_add(trim($_POST["title"]), trim($_POST["desc"]));
tf_db_close();

if($result){
	header("location:showforum.php");
}


?>