<?php
	session_start();
	include("php/connect_to_mysql.php");
	
	$msg = "";
	$msg2 = "";
	$submit = $_POST['btnaddnew'];
	if($submit == "Save"){
		if(empty($_POST['txtUserId']) || empty($_POST['txtFname']) || empty($_POST['txtMname']) || empty($_POST['txtLname']) || empty($_POST['txtNoStreet']) || empty($_POST['txtCity']) || empty($_POST['txtDOB']) || empty($_POST['txtAge']) || empty($_POST['selGender']) || empty($_POST['txtUser']) || empty($_POST['txtPass']) || empty($_POST['txtConPass']) || empty($_POST['selUserType'])){
			$msg = "Some field is empty!";
		}else if($_POST['txtPass'] != $_POST['txtConPass']){
			$msg2 = "Password did not match!";
		}else{
			$sqlExistId = mysql_query("select user_id from tblusers where user_id = '$_POST[txtUserId]'") or die(mysql_error());
			$sqlExistUsername = mysql_query("select * from tblaccount where username = '$_POST[txtUser]'") or die(mysql_error());
			if(mysql_num_rows($sqlExistId) >= 1)
				$msg = "User id is already taken";
			else if(mysql_num_rows($sqlExistUsername))
				$msg2 = "Username is already taken!";
			else{
				mysql_query("insert into tblusers(user_id, fname, mname, lname, no_street, city, contact_no, dob, age, gender, user_type)
							values('$_POST[txtUserId]','$_POST[txtFname]','$_POST[txtMname]','$_POST[txtLname]','$_POST[txtNoStreet]','$_POST[txtCity]','$_POST[txtContactNo]','$_POST[txtDOB]','$_POST[txtAge]','$_POST[selGender]','$_POST[selUserType]')") or die(mysql_error());
				mysql_query("insert into tblaccount(user_id, username, password) values('$_POST[txtUserId]','$_POST[txtUser]','$_POST[txtPass]')") or die(mysql_error());
				$msg = "Successfully added new user!";
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/slider.css" />
<title>Dadads - Administrator</title>
</head>
<body id="adminpage">
<div id="main_wrapper">
	<div id="main_header">
		<div id="admin_name"><h1>Welcome <?php echo $_SESSION["name"]; ?></h1></div>
	</div><!-- end of header -->
	<div id="main_menu" class="menu_sel">
		<ul id="menu_icons">
			<li><a href="admin_addnew_user.php"><img src="images/User-Group-icon.png" /><p class="selected">Users</p></a></li>
			<li><a href="admin_addnew_product.php"><img src="images/Products-icon.png" /><p>Products</p></a></li>
		</ul>
	</div><!-- end of menu -->
	<div class="cleaner h40"></div>
	<div id="main_top"></div>
	<div id="main_con">
		<div id="sidebar">
			<h3>Manage Users</h3>
			<ul class="sidebar_menu">
			    <li><a class="selected" href="admin_addnew_user.php">Add New User</a></li>
                <li><a href="admin_editdelete_user.php">Edit / Delete User</a></li>
				<li><a href="admin_change_userpass.php">Change User's Password</a></li>
                <li><a href="admin_search_user.php">Search User</a></li>				
			</ul>
		</div><!-- end of sidebar -->
		<div id="content">
			<h2>User Information <span style="color: #a11; font-size: 13px; margin-left: 50px;"><?php echo $msg; ?><span></h2>
			
			<form name="addnew_user_form" method="post" action="admin_addnew_user.php">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="border border_tlr border_ts border_ls border_bs col_1">User ID:</td>
					<td colspan="3" class="border border_ts border_bs border_rs border_trr col_1"><input type="text" name="txtUserId" /></td>
				</tr>
				<tr>
					<td class="border border_ls col_1"><br></td>
					<td class="border col_2">First</td>
					<td class="border col_2">Middle</td>
					<td class="border border_rs col_2">Last</td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Name:</td>
					<td class="border border_bs col_2"><input type="text" name="txtFname" /></td>
					<td class="border border_bs col_2"><input type="text" name="txtMname" /></td>
					<td class="border border_bs border_rs col_2"><input type="text" name="txtLname" /></td>
				</tr>
				<tr>
					<td class="border border_ls col_1"><br></td>
					<td class="border col_2">No. and Street</td>
					<td class="border col_2">City</td>
					<td class="border border_rs col_2">Contact No.</td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Address:</td>
					<td class="border border_bs col_2"><input type="text" name="txtNoStreet" /></td>
					<td class="border border_bs col_2"><input type="text" name="txtCity" /></td>
					<td class="border border_bs border_rs col_2"><input type="text" name="txtContactNo" /></td>
				</tr>
				<tr>
					<td colspan="2" class="border border_ls col_1"><br></td>
					<td class="border col_2">Age</td>
					<td class="border border_rs col_2">Gender</td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Date of Birth</td>
					<td class="border border_bs col_2"><input type="text" name="txtDOB" /></td>
					<td class="border border_bs col_2"><input type="text" name="txtAge" /></td>
					<td class="border border_bs border_rs col_2">
						<select name="selGender"><option></option><option>Male</option><option>Female</option></select>
					</td>
				</tr>
				<tr>
					<td class="border border_bs border_ls">User Type:</td>
					<td colspan="3" class="border border_bs border_rs border_rs"><select name="selUserType"><option></option><option>Administrator</option><option>Clerk</option></select>
				</tr>
			</table>
			<h2>Account Information <span style="color: #a11; font-size: 13px; margin-left: 50px;"><?php echo $msg2; ?><span></h2>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="border border_ts border_bs border_ls border_tlr col_1">Username:</td>
					<td colspan="3" class="border border_bs border_ts border_rs border_trr"><input type="text" name="txtUser" placeholder="Username" /></td>
				</tr>
				<tr>
					<td class="border border_bs border_ls col_1">Password:</td>
					<td class="border border_bs col_1"><input type="password" name="txtPass" placeholder="Password" /></td>
					<td class="border border_bs col_02">Confirm Password:</td>
					<td class="border border_bs border_rs col_2"><input type="password" name="txtConPass" placeholder="Password" /></td>
				</tr>
			</table>
			<div class="marginleft_1">
				<input class="margintop_2 sub_btn" type="submit" name="btnaddnew" value="Save"/>
				<input class="margintop_2 sub_btn" type="reset" name="btnreset" value="Reset" />
			</div>
			</form>
		</div><!-- end of content -->
		<div class="cleaner"></div>
	</div><!-- end of con -->
	<div id="main_footer">
		<div class="cleaner h40"></div>
		<center>
			Copyright © 2014  Tings Trells
		</center>
	</div><!-- end of footer -->
</div>
</body>
</html>