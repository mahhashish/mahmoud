<?php
	session_start();
	include("php/connect_to_mysql.php");
	include("php/myFunctions.php");
	
	$msg = "";
	disableProductComponents();
	$displayPic = "";
	$submit = $_POST['btnSearch'];
	$submit1 = $_POST['btnUpdate'];
	$submit2 = $_POST['btnDelete'];
	$pid = $_GET['prodid'];

	if(!empty($pid) || isset($pid)){
		selectProductUsingId($pid);
		$displayPic .= '<img style="width:100px; height:130px; border:2px solid; margin-left:150px; margin-bottom:20px;" src="../images/product/'.$prodNo.'.jpg" >';
	}
	
	if($submit == "Search"){
		if(empty($_POST['txtProdId']))
			$msg = "Product ID is empty!";
		else{
			selectProductUsingId($_POST['txtProdId']);
			$displayPic .= '<img style="width:100px; height:130px; border:2px solid; margin-left:150px; margin-bottom:20px;" src="../images/product/'.$prodNo.'.jpg" >';
		}
		
	}else if($submit1 == "Update"){
		$imgID = $_POST['txtHoldProdNo'];
		mysql_query("update tblproduct set prod_name = '$_POST[txtProdName]', prod_descr = '$_POST[txtProdDescr]', prod_cat = '$_POST[selProdCat]', prod_price = '$_POST[txtProdPrice]', prod_quan = '$_POST[txtProdQuan]' where prod_id = '$_POST[txtProdId]'") or die(mysql_error());
		$msg = "Successfully updated the record!";
		if($_FILES['fileField']['tmp_name'] != ""){
			$newname = "$imgID.jpg";
			move_uploaded_file($_FILES['fileField']['tmp_name'],"../images/product/$newname");
		}
		disableProductComponents();
	}else if($submit2 == "Delete"){
		$imgID = $_POST['txtHoldProdNo'];
		mysql_query("delete from tblproduct where prod_id = '$_POST[txtProdId]'") or die(mysql_error());
		$msg = "Successfully deleted the record!";
		$pictodelete=("../images/product/$imgID.jpg");
		if(file_exists($pictodelete)){
			unlink($pictodelete);
		}
		disableProductComponents();
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
				<li><a href="admin_addnew_product.php">Add New Product</a></li>
				<li><a class="selected" href="admin_editdelete_product.php">Edit / Delete Product</a></li>				
				<li><a href="admin_search_product.php">Search Product</a></li>				
			</ul>
		</div><!-- end of sidebar -->
		<div id="content">
			<h2>Product Information <span style="color: #a11; font-size: 13px; margin-left: 50px;"><?php echo $msg; ?><span></h2>
			
			<form name="editdelete_product_form" method="post" action="admin_editdelete_product.php" enctype="multipart/form-data">
			<div><?php echo $displayPic; ?></div>
			<div class="cleaner"></div>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="border border_tlr border_ts border_ls border_bs col_02">Product ID:</td>
					<td class="border border_ts border_bs col_1"><input type="text" name="txtProdId" value="<?php echo $prodID; ?>" /></td>
					<td class="border border_ts border_bs border_rs border_trr"><input class="src_btn" type="submit" name="btnSearch" value="Search" /></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Change Image:</td>
					<td colspan="2" class="border border_bs border_rs"><input class="col_3 marginleft_3" type="file" name="fileField" <?php echo $imageFile; ?> /></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Name:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><input class="col_4" type="text" name="txtProdName" value="<?php echo $prodName; ?>" <?php echo $txtProdName; ?> /></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Description:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><textarea name="txtProdDescr" cols="40" rows="4" <?php echo $txtProdDescr; ?> ><?php echo $prodDescr; ?></textarea></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Category:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><select name="selProdCat" class="col_2" value="<?php echo $prodCat; ?>" <?php echo $txtProdCat; ?> ><option></option><option>Juice</option><option>Dessert Sprinkler</option><option>JunkFood</option></select></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Price:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><input type="text" name="txtProdPrice" value="<?php echo $prodPrice; ?>" <?php echo $txtProdPrice; ?> /></td>
				</tr>
				<tr>
					<td class="border border_ls border_bs col_1">Product Quantity:</td>
					<td colspan="3" class="border border_bs border_rs col_2"><input type="text" name="txtProdQuan" value="<?php echo $prodQuan; ?>"  <?php echo $txtProdQuan; ?> /></td>
				</tr>
			</table>
			
			<div class="marginleft_1">
				<input type="hidden" name="txtHoldProdNo" value="<?php echo $prodNo; ?>" />
				<input class="margintop_2 sub_btn" type="submit" name="btnUpdate" value="Update" <?php echo $btnUpdateDisable; ?> />
				<input class="margintop_2 sub_btn" type="submit" name="btnDelete" value="Delete" <?php echo $btnDeleteDisable; ?> />
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