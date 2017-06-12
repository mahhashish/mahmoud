<?php
if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if($_id == 0){
	die('NO ID == 0');
}

include_once('usersAPI.php');

$user = tinyf_user_get_by_id($_id);
tf_db_close();

if($user == null){
	die('NO User');
}


?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $user->name; ?></title>
</head>
<body>
<h4>User Name: <?php echo $user->name ?></h4>
<h4>User Email: <?php echo $user->email ?></h4>
</body>
</html>