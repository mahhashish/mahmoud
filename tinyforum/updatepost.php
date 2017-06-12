<?php
if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if($_id == 0){
	die('NO ID == 0');
}

if( empty($_POST["title"]) || empty($_POST["content"]) || empty($_POST["forum"]) ){
	die('Enter Data');
}

include_once'postsAPI.php';
include_once 'forumAPI.php';

$fid = trim($_POST["forum"]);

$result = tinyf_post_update($_id, $fid, 0 , 0, trim($_POST["title"]), trim($_POST["content"]) );
tf_db_close();

if($result){
	header("location:forum.php?id=$fid");
}


?>