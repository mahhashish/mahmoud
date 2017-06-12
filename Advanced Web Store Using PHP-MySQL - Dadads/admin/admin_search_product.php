<?php
	session_start();
	include("php/connect_to_mysql.php");
	include("php/myFunctions.php");
	
	$msg = "";
	$displayTitle = "";
	$displayContent = "";
	$submit = $_POST['btnSearch'];
	
	if($submit == "Search"){
		$product_name = $_POST['txtProdName'];
		if(empty($_POST['txtProdName']))
			$msg = "Search product name is empty!";
		else{
			
			$sqlSelProdExist = mysql_query("select * from tblproduct where prod_name like '$product_name%' order by prod_id") or die(mysql_error());
			if(mysql_num_rows($sqlSelProdExist) >= 1){
				$displayTitle .= '<table cellpadding="0" cellspacing="0" border="0" class="margintop_3">
						<tr style="background-color: #401; color: white;">
						<td class="border border_ts border_bs border_ls col_1">Product ID</td>
						<td class="border border_ts border_bs border_ls col_03">Product Name</td>
						<td class="border border_ts border_bs border_ls col_1">Product Price</td>
						<td class="border border_ts border_bs border_ls col_1">Product Quantity</td>
						<td class="border border_ts border_bs border_ls border_rs col_2">Date Added</td></tr>';
				while($getProdRec = mysql_fetch_array($sqlSelProdExist)){
					$prodNo = $getProdRec["prod_no"];
					$prodID = $getProdRec["prod_id"];
					$prodName = $getProdRec["prod_name"];
					$prodPrice = $getProdRec["prod_price"];
					$prodQuan = $getProdRec["prod_quan"];
					$prodDateAdded = $getProdRec["date_added"];
					
					$displayContent .= '<tr style="background-color: #fed; color: black;">
						<td style="font-size: 11px; border-bottom-style: double; color:#001;">'.$prodID.'</td>
						<td style="font-size: 11px; border-bottom-style: double; color:#001;"><a href="admin_editdelete_product.php?prodid='.$prodID.'" style="color: #001;">'.$prodName.'</a></td>
						<td style="font-size: 11px; border-bottom-style: double; color:#001;">'.$prodPrice.'</td>
						<td style="font-size: 11px; border-bottom-style: double; color:#001;">'.$prodQuan.'</td>
						<td style="font-size: 11px; border-bottom-style: double; color:#001;">'.$prodDateAdded.'</td></tr>';
				}
				$displayContent .= '</table>';
			}else
				$msg = "Record not found!";
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
				<li><a href="admin_editdelete_product.php">Edit / Delete Product</a></li>				
				<li><a class="selected" href="admin_search_product.php">Search Product</a></li>				
			</ul>
		</div><!-- end of sidebar -->
		<div id="content">
			<h2>Product Information <span style="color: #a11; font-size: 13px; margin-left: 50px;"><?php echo $msg; ?><span></h2>
			
			<form name="search_product_form" method="post" action="admin_search_product.php">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="border border_tlr border_ts border_ls border_bs col_02">Product Name:</td>
					<td class="border border_ts border_bs col_1"><input type="text" name="txtProdName" value="<?php echo $prodID; ?>" /></td>
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