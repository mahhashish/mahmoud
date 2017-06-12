<?php
	session_start();
	include("php/connect_to_mysql.php");
	include("php/myFunctions.php");
	
	$msg = "";
	$displayTitle = "";
	$displayContent = "";
	$submit = $_POST['btnSearch'];
	if($submit == "Search"){
		$searchName = $_POST['txtNameSearch'];
		if(empty($_POST['txtNameSearch']) || empty($_POST['selSearchOption']))
			$msg = "Search option / name is empty!";
		else{
			if($_POST['selSearchOption'] == "Firstname"){
				$sqlSelUsers = mysql_query("select * from tblusers where fname like '$searchName%'") or die(mysql_error());
				if(mysql_num_rows($sqlSelUsers) >= 1){
					$displayTitle .= '<table cellpadding="0" cellspacing="0" border="0" class="margintop_3">
									<tr style="background-color: #401; color: white;">
									<td class="border border_ts border_bs border_ls col_1">Id Number</td>
									<td class="border border_ts border_bs border_ls col_03">Name</td>
									<td class="border border_ts border_bs border_ls col_4">Address</td>
									<td class="border border_ts border_bs border_ls border_rs col_1">Contact No</td></tr>';
					while($getRowUser = mysql_fetch_array($sqlSelUsers)){
						$userID = $getRowUser["user_id"];
						$fname = $getRowUser["fname"];
						$mname = $getRowUser["mname"];
						$lname = $getRowUser["lname"];
						$name = $lname.", ".$fname." ".$mname;
						$noSt = $getRowUser["no_street"];
						$city = $getRowUser["city"];
						$addr = $noSt.", ".$city;
						$contactNo = $getRowUser["contact_no"];
						
						$displayContent .= '<tr style="background-color: #2ef; color: black;">
						<td style="font-size: 11px; border-bottom-style: double;">'.$userID.'</td>
						<td style="font-size: 11px; border-bottom-style: double;"><a href="admin_editdelete_user.php?userid='.$userID.'" style="color: #001;">'.$name.'</a></td>
						<td style="font-size: 11px; border-bottom-style: double;">'.$addr.'</td>
						<td style="font-size: 11px; border-bottom-style: double;">'.$contactNo.'</td></tr>';
					}
					$displayContent .= '</table>';
				}else
					$msg = "No record found!";
			}else if($_POST['selSearchOption'] == "Lastname"){
				$sqlSelUsers = mysql_query("select * from tblusers where lname like '$searchName%'") or die(mysql_error());
				if(mysql_num_rows($sqlSelUsers) >= 1){
					$displayTitle .= '<table cellpadding="0" cellspacing="0" border="0" class="margintop_3">
									<tr style="background-color: #401; color: white;">
									<td class="border border_ts border_bs border_ls col_1">Id Number</td>
									<td class="border border_ts border_bs border_ls col_03">Name</td>
									<td class="border border_ts border_bs border_ls col_4">Address</td>
									<td class="border border_ts border_bs border_ls border_rs col_1">Contact No</td></tr>';
					while($getRowUser = mysql_fetch_array($sqlSelUsers)){
						$userID = $getRowUser["user_id"];
						$fname = $getRowUser["fname"];
						$mname = $getRowUser["mname"];
						$lname = $getRowUser["lname"];
						$name = $lname.", ".$fname." ".$mname;
						$noSt = $getRowUser["no_street"];
						$city = $getRowUser["city"];
						$addr = $noSt.", ".$city;
						$contactNo = $getRowUser["contact_no"];
						
						$displayContent .= '<tr style="background-color: #2ef; color: black;">
						<td style="font-size: 11px; border-bottom-style: double;">'.$userID.'</td>
						<td style="font-size: 11px; border-bottom-style: double;"><a href="admin_editdelete_user.php?userid='.$userID.'" style="color: #001;">'.$name.'</a></td>
						<td style="font-size: 11px; border-bottom-style: double;">'.$addr.'</td>
						<td style="font-size: 11px; border-bottom-style: double;">'.$contactNo.'</td></tr>';
					}
					$displayContent .= '</table>';
				}else
					$msg = "No record found!";
			}
			
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href="css/styles.css" rel="stylesheet" type="text/css" />
<title>Dadads - Administrator</title>
</head>
<body id="adminpage">
<div id="main_wrapper">
	<div id="main_header">
		<div id="admin_name"><h1><h1>Welcome <?php echo $_SESSION["name"]; ?></h1></div>
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
			    <li><a href="admin_addnew_user.php">Add New User</a></li>
                <li><a href="admin_editdelete_user.php">Edit / Delete User</a></li>		
				<li><a href="admin_change_userpass.php">Change User's Password</a></li>
                <li><a class="selected" href="admin_search_user.php">Search User</a></li>			
			</ul>
		</div><!-- end of sidebar -->
		<div id="content">
			<h2>User Information <span style="color: #a11; font-size: 13px; margin-left: 50px;"><?php echo $msg; ?><span></h2>
			
			<form name="search_user_form" method="post" action="admin_search_user.php">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="border border_tlr border_ts border_ls border_bs col_1">Search by:</td>
					<td class="border border_ts border_bs"><select name="selSearchOption"><option></option><option>Firstname</option><option>Lastname</option></select></td>
					<td class="border border_ts border_bs col_1"><input type="text" name="txtNameSearch" /></td>
					<td class="border border_ts border_bs border_rs border_trr col_1"><input class="src_btn" type="submit" name="btnSearch" value="Search" /></td>
				</tr>
			</table>
			<div class="cleaner"></div>
			<?php echo $displayTitle; ?>
			<?php echo $displayContent; ?>
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