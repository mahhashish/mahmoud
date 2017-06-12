<?php
if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if($_id == 0){
	die('NO ID == 0');
}

if( empty($_POST["title"]) || empty($_POST["desc"]) ){
	die('Enter Data');
}

include_once('forumAPI.php');

$result = tinyf_forum_update($_id, trim($_POST["title"]), trim($_POST["desc"]) );
tf_db_close();

if($result){
	echo'<pre>';
	print_r($_POST);
	echo'</pre>';	
}


?>