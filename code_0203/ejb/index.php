<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>PHP N-Tiered Application</title>
	<style type="text/css">
		td { font-family: Verdana; font-size: 9pt; }
		h1 { font-family: Verdana; font-size: 16pt; font-weight: bold; }
	</style>
</head>

<body bgcolor="White">

<h1>PHP N-Tiered Application Example</h1>
<hr/>

<?php

if(!isset($attributes) || !is_array($attributes)) {
	$attributes = array();
	$attributes = array_merge($HTTP_POST_VARS, $HTTP_GET_VARS, $_FILES); 
}

$factory   = "org.jnp.interfaces.NamingContextFactory";
$url       = "jnp://192.168.1.1:1099";
$jndiName  = "ew/phpa/User";

$java_obj = new Java ("org.ew.phpa.User.clients.UserClient");
$java_obj->init($factory,$url,$jndiName);

if (isset($attributes["edit"])) {
    printEditUserForm();
}
else if (isset($attributes["add"])) {
    printNewUserForm();
}
else if (isset($attributes["save"])) {
    saveChanges();
}
else {
    printEntry();
}

function saveChanges() {
	// we just need to build our structure, serialize it, then pass
	// it into our EJB client where it will be deserialized and sent
	// to the EJB to chew on!
	global $attributes;
	global $java_obj;
	$username 	= $attributes["username"];
	$password 	= $attributes["password"];
	$firstname 	= $attributes["firstname"];
	$lastname 	= $attributes["lastname"];
	$email 		= $attributes["email"];
	$user_id 	= $attributes["user_id"];
	$packet = wddx_serialize_vars("username","password","firstname",
		"lastname","email","user_id");
	$status = $java_obj->saveUser($packet);
	echo '<table width="50%" cellspacing="0" cellpadding="2" border="0"><tr><td><b>';
	print $status;
	echo '</b></td></tr></table>';
}

function printNewUserForm () {
	echo '
		<form action="index.php" method="POST">
		<input type="hidden" name="user_id" value="0">
		<table width="50%" cellspacing="0" cellpadding="2" border="0">
		<tr>
			<td colspan="2"><strong>Add New User</strong></td>
		</tr>
		<tr>
			<td>User name:</td>
			<td><input type="text" name="username" size="20"></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="password" size="20"></td>
		</tr>
		<tr>
			<td>First Name:</td>
			<td><input type="text" name="firstname" size="20"></td>
		</tr>
		<tr>
			<td>Last Name:</td>
			<td><input type="text" name="lastname" size="20"></td>
		</tr>
		<tr>
			<td>Email:</td>
			<td><input type="text" name="email" size="20"></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="save" value="save">
			</td>
		</tr>
		</table>
		</form>';
}

function printEditUserForm () {
	global $attributes;
	global $java_obj;
	if (! isset($attributes["user_id"])) {
		print "You must select a user first!";
		exit;
	}
	
	$uid = $attributes["user_id"];
	$userHash = wddx_deserialize($java_obj->getUser($uid));
	
	echo '
		<form action="index.php" method="POST">
		<input type="hidden" name="user_id" value="'.$uid.'">
		<table width="50%" cellspacing="0" cellpadding="2" border="0">
		<tr>
			<td colspan="2">Edit user: <strong>'. $userHash["username"] . 
				'</strong> (uid: '.$userHash["user_id"].')</td>
		</tr>
		<tr>
			<td>User name:</td>
			<td><input type="text" name="username" 
				size="20" value="'.$userHash["username"] . '"></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="password"
				size="20" value="'.$userHash["password"] . '"></td>
		</tr>
		<tr>
			<td>First Name:</td>
			<td><input type="text" name="firstname"
				size="20" value="'.$userHash["firstname"].'"></td>
		</tr>
		<tr>
			<td>Last Name:</td>
			<td><input type="text" name="lastname"
				size="20" value="'.$userHash["lastname"].'"></td>
		</tr>
		<tr>
			<td>Email:</td>
			<td><input type="text" name="email"
				size="20" value="'.$userHash["email"].'"></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="save" value="save">
			</td>
		</tr>
		</table>
		</form>';
}

function printEntry () {
    global $java_obj;
    $wddx = $java_obj->getUsers();
    $user_array = wddx_deserialize($wddx);
    echo '
        <form action="index.php" method="POST">
        <table width="50%">
        <tr>
            <td width="50%" valign="top">
                <b>Current users in the system</b>: 
                <p/>
                <select name="user_id" size="5">';
    
	
    for ($i = 0; $i < count($user_array); $i++) {
        $uid = $user_array[$i]["user_id"];
        $username = $user_array[$i]["username"];
        echo '<option value="'.$uid.'">'.$username.'</option>';
    }
    
    echo '  </select>
          </td>
          <td width="50%" valign="top">
          Select a user name from the list then click an action:
          <p/>
          <input type="submit" name="edit" value="edit"> 
          <input type="submit" name="add" value="add">
          </td></tr>
          </table>
          </form>';
            
}

?>
<p/>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<tr>
	<td>
		<a href="index.php"><strong>Main Menu</strong></a>
	</td>
</tr>
</table>
</body>
</html>
