<?php
include_once 'session.php';
if($_SESSION['user_info']->isadmin != 1){
    die("Please Login");
}

if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if((!isset($_GET['c'])) || ($_GET['c'] != 1)){
	die('<a href="deleteforum.php?id='.$_id.'&c=1">Are You Sure</a>');
}

if($_id == 0){
	die('NO ID == 0');
}

include_once('forumAPI.php');

$forum = tinyf_forum_get_by_id($_id);

if($forum == null){
	tf_db_close();
	die('NO Forum');
}

$result = tinyf_forum_delete($_id);
tf_db_close();

if($result){
	header("location:showforum.php");
}
else{
	die('FAIL');
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Delete<?php echo $forum->title; ?></title>
</head>
<body>

</body>
</html>