<!DOCTYPE html>
<html>
<head>
	<title>USERS LIST</title>
</head>
<body>
<?php
include_once('usersAPI.php');

$user = tinyf_user_get();
if($user == null){
	die('NULL!');
}

$ucount = count($user);

if($ucount == 0){
	die('There Is No Users');
}

?>
<ul type="square">
<?php
for($i = 0; $i < $ucount; $i++){
	$users = $user[$i];
	echo"<li><a href=\"userprofile.php?id=$users->id\">$users->name</a> >>> $users->email >>> <a href=\"deleteuser.php?id=$users->id\">Delete</a> | <a href=\"modifyuser.php?id=$users->id\">Modify</a></li>";
}
?>
</ul>


</body>
</html>

