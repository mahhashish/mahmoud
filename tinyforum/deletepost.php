<?php
if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

include_once'postsAPI.php';

$post = tinyf_post_get_by_id($_id);
$fid  = $post->fid;

echo"<html><head><title>حذف $post->title</title></head></html>";

if((!isset($_GET['c'])) || ($_GET['c'] != 1)){
	die('<a href="deletepost.php?id='.$_id.'&c=1">Are You Sure</a>');
}

if($_id == 0){
	die('NO ID == 0');
}



if($post == null){
	tf_db_close();
	die('NO Forum');
}

$result = tinyf_post_delete($_id);
tf_db_close();

if($result){
	header("location:forum.php?id=$fid");
}
else{
	die('FAIL');
}
?>