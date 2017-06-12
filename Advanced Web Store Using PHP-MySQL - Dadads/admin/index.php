<?php
	session_start();
	include("php/connect_to_mysql.php");

	$msg = "";
	$submit = $_POST['btnLogin'];
	
	if($submit == "Log in"){
		$sqlSelUser = mysql_query("select * from tblaccount where username = '$_POST[txtLogin]' and password = '$_POST[txtPassword]' limit 1") or die(mysql_error());
		if(mysql_num_rows($sqlSelUser) == 1){
			$getAccUserId = mysql_fetch_array($sqlSelUser);
			$userid = $getAccUserId["user_id"];
			
			$sqlSelUserInfo = mysql_query("select user_type, fname, mname, lname from tblusers where user_id = '$userid'") or die(mysql_error());
			$getUserInfo = mysql_fetch_array($sqlSelUserInfo);
			$fname = $getUserInfo["fname"];
			$mname = $getUserInfo["mname"];
			$lname = $getUserInfo["lname"];
			$name = $lname.", ".$fname." ".$mname;
			$userType = $getUserInfo["user_type"];
			$_SESSION["name"] = $name;
			
			if($userType == "Administrator")
				header("location: admin_addnew_user.php");
			else if($userType == "Clerk")
				header("location: clerk_addnew_product.php");
			else
				$msg = '<p align="right" style="color:#a03; font-size:13px; margin-bottom:3px; margin-right:50px;">Something wrong with the account!</p>';
		}else
			$msg = '<p align="right" style="color:#a03; font-size:13px; margin-bottom:3px; margin-right:50px;">Username / Password does not exist!</p>';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<title>Login Page</title>
	
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="stylesheet" type="text/css" href="css/loginstyle.css" />
</head>

<body>
	
	
	<form id="login-form" action="index.php" method="post">
		<fieldset>
			<?php echo $msg; ?>
			<legend>Log in</legend>
			<label for="login">Username</label>
			<input type="text" name="txtLogin" placeholder="Username" />
			<div class="clear"></div>
			
			<label for="password">Password</label>
			<input type="password" name="txtPassword" placeholder="Password" />
			<div class="clear"></div>
			<div class="clear"></div>
			
			<br />
			
			<input type="submit" style="margin: -20px 0 0 287px;" class="button" name="btnLogin" value="Log in"/>	
		</fieldset>
	</form>
	
</body>

</html>