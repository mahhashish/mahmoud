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
	<title>Modify User: <?php echo $user->name; ?></title>
</head>
<body>
<form action="updateuser.php?id=<?php echo $_id; ?>" method="post">
<table align="center" style="width: 60%">
	<tr>
		<td>Name:</td>
  		<td><input type="text" value="<?php echo $user->name; ?>" name="username"></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" value="<?php echo $user->password; ?>" name="password"></td>
	</tr>
	<tr>
		<td>Email:</td>
  		<td><input type="email" value="<?php echo $user->email; ?>" name="email"></td>
	</tr>
	<tr>
	<td><input type="submit" name="submit" value="Modify: <?php echo $user->name; ?>"></input></td>
	</tr>
</table>
</form>
</body>
</html>