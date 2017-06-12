<?php
if(!isset($_GET['id'])){
	header("location: showforum.php");
}

$_id = (int)$_GET['id'];

if((!isset($_GET['c'])) || ($_GET['c'] != 1)){
	die('<a href="deleteuser.php?id='.$_id.'&c=1">Are You Sure</a>');
}



if($_id == 0){
	die('NO ID == 0');
}

include_once('usersAPI.php');

$user = tinyf_user_get_by_id($_id);


if($user == null){
	tf_db_close();
	die('NO User');
}

$result = tinyf_user_delete($_id);
tf_db_close();

if($result){
	header("location:showuser.php");
}
else{
	die('FAIL');
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Delete<?php echo $user->name; ?></title>
</head>
<body>

</body>
</html>