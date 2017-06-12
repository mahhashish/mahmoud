<?php

if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if($_id == 0){
	die('NO ID == 0');
}

include_once('forumAPI.php');
include_once('postsAPI.php');

$post = tinyf_post_get_by_id($_id);

if(!$post){
	tf_db_close();
	die('No Forum Id');
}

if( empty($_POST["title"]) || empty($_POST["content"]) ){
	die('Enter Data');
}

$result = tinyf_reply_add($_id, 0, trim($_POST["title"]), trim($_POST["content"]));
tf_db_close();

if($result){
	header("location: showpost.php?id=$_id");
}else{
    echo "NO";
}


?>



