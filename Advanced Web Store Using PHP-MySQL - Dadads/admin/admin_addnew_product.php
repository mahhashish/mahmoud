<?php
	session_start();
	include("php/connect_to_mysql.php");
	
	$msg = "";
	$submit = $_POST['btnaddnew'];
	
	if($submit == "Save"){
		if(empty($_POST['txtProdId']))
			$msg = "Product Id is empty!";
		else{
			$sqlSelExistID = mysql_query("select * from tblproduct where prod_id = '$_POST[txtProdId]'") or die(mysql_error());
			if(mysql_num_rows($sqlSelExistID) >= 1)
				$msg = "Product ID is already in use!";
			else{
				mysql_query("insert into tblproduct(prod_id, prod_name, prod_descr, prod_cat, prod_price, prod_quan, date_added)
					values('$_POST[txtProdId]','$_POST[txtProdName]','$_POST[txtProdDescr]','$_POST[selProdCat]','$_POST[txtProdPrice]','$_POST[txtProdQuan]',now())") or die(mysql_error());
				$imgID = mysql_insert_id();
				$newname = "$imgID.jpg";
				move_uploaded_file($_FILES['fileField']['tmp_name'],"../images/product/$newname");
				$msg = "Successfully added new product!";
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
		<div id="admin_name"><h1>Welcome <?php echo $_SESSION["name"]; ?></h1></div>
	</div><!-- end of header -->
	<div id="main_menu" class="menu_sel">
		<ul id="menu_icons">
			<li><a href="admin_addnew_user.php"><img src="images/User-Group-icon.png" /><p>Users</p></a></li>
			<li><a href="admin_addnew_product.php"><img src="images/Products-icon.png" /><p class="selected">Products</p></a></li>
		</ul>
	</div><!-- end of menu -->
	<div class="cleaner h40"></div>
	<div id="main_top"></div>
	<div id="main_con">
		<div id="sidebar">
			<h3>Manage Product</h3>
			<ul class="sidebar_menu">
				<li><a class="selected" href="admin_addnew_product.php">Add New Product</a></li>
				<li><a href="admin_editdelete_product.php">Edit / Delete Product</a></li>				
				<li><a href="admin_search_product.php">Search Product</a></li>				
			</ul>
		</div><!-- end of sidebar -->
		<div id="content">
			<h2>Product Information <span style="color: #a11; font-size: 13px; margin-left: 50px;"><?php echo $msg; ?><span></h2>
			
			<form name="addnew_product_form" method="post" action="admin_addnew_product.php" enctype="multipart/form-data">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="border border_tlr border_ts border_ls border_bs col_02">Product ID:</td>
					<td class="border border_ts border_bs col_1"><input type="text" name="txtProdId" /></td>
					<td colspan="2" class="border border_ts border_bs border_rs border_trr">Product Image:<input class="col_3 marginleft_3" type="file" name="fileField" /></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Name:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><input class="col_4" type="text" name="txtProdName" /></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Description:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><textarea name="txtProdDescr" cols="40" rows="4"></textarea></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Category:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><select name="selProdCat" class="col_2"><option></option><option>Juice</option><option>Dessert Sprinkle</option><option>JunkFood</option></select></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Price:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><input type="text" name="txtProdPrice" /></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Quantity:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><input type="text" name="txtProdQuan" /></td>
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