<?php
	include("admin/php/connect_to_mysql.php");
	include("admin/php/myFunctions.php");
	
	$displayProdCat = "";
	$prodCatCtr = 0;
	if(!empty($_GET['keyword'])){
		$productName = $_GET['keyword'];
		$sqlSelectSearchProduct = mysql_query("select * from tblproduct where prod_name like '$productName%'") or die(mysql_error());
		if(mysql_num_rows($sqlSelectSearchProduct) >= 1){
			while($getRowSearchProduct = mysql_fetch_array($sqlSelectSearchProduct)){
				$prodNo = $getRowSearchProduct["prod_no"];
				$prodId = $getRowSearchProduct["prod_id"];
				$prodName = $getRowSearchProduct["prod_name"];
				$prodPrice = $getRowSearchProduct["prod_price"];
				
				$displayProdCat .= '<div class="col col_14 product_gallery">
				<a href="productdetail.php?prodid='.$prodId.'"><img src="images/product/'.$prodNo.'.jpg" width="170" height="150" /></a>
				<h3>'.$prodName.'</h3>
				<p class="product_price">Php '.$prodPrice.'</p>
				<a href="shoppingcart.php?prodid='.$prodId.'" class="add_to_cart">Add to Cart</a></div>';
			}
		}
	}else{
	$sqlSelectProdCat1 = mysql_query("select * from tblproduct where prod_cat = 'juice'") or die(mysql_error());
	if(mysql_num_rows($sqlSelectProdCat1) >= 1){
		$displayProdCat .= '<h2>Juice</h2>';
		while($getProdInfo1 = mysql_fetch_array($sqlSelectProdCat1)){
			$prodNo = $getProdInfo1["prod_no"];
			$prodId = $getProdInfo1["prod_id"];
			$prodName = $getProdInfo1["prod_name"];
			$prodPrice = $getProdInfo1["prod_price"];
			$displayProdCat .= '<div class="col col_14 product_gallery">
			<a href="productdetail.php?prodid='.$prodId.'"><img src="images/product/'.$prodNo.'.jpg" width="170" height="150" /></a>
			<h3>'.$prodName.'</h3>
			<p class="product_price">Php '.$prodPrice.'</p>
			<a href="shoppingcart.php?prodid='.$prodId.'" class="add_to_cart">Add to Cart</a></div>';
			
			if(++$prodCatCtr == 3)
				break;
		}
		$displayProdCat .= '<a href="index.php?cat=juice" class="more float_r">View all</a><div class="cleaner h50"></div>';
	}
	$sqlSelectProdCat2 = mysql_query("select * from tblproduct where prod_cat = 'junkfood'") or die(mysql_error());
	if(mysql_num_rows($sqlSelectProdCat2) >= 1){
		$displayProdCat .= '<h2>Junk Food</h2>';
		while($getProdInfo1 = mysql_fetch_array($sqlSelectProdCat2)){
			$prodNo = $getProdInfo1["prod_no"];
			$prodId = $getProdInfo1["prod_id"];
			$prodName = $getProdInfo1["prod_name"];
			$prodPrice = $getProdInfo1["prod_price"];
			$displayProdCat .= '<div class="col col_14 product_gallery">
			<a href="productdetail.php?prodid='.$prodId.'"><img src="images/product/'.$prodNo.'.jpg" width="170" height="150" /></a>
			<h3>'.$prodName.'</h3>
			<p class="product_price">Php '.$prodPrice.'</p>
			<a href="shoppingcart.php?prodid='.$prodId.'" class="add_to_cart">Add to Cart</a></div>';
			
			if(++$prodCatCtr == 3)
				break;
		}
		$displayProdCat .= '<div class="cleaner"></div><a href="index.php?cat=junkfood" class="more float_r">View all</a><div class="cleaner h50"></div>';
	}
	$sqlSelectProdCat3 = mysql_query("select * from tblproduct where prod_cat = 'dessert sprinkler'") or die(mysql_error());
	if(mysql_num_rows($sqlSelectProdCat3) >= 1){
		$displayProdCat .= '<h2>Dessert Sprinkler</h2>';
		while($getProdInfo1 = mysql_fetch_array($sqlSelectProdCat3)){
			$prodNo = $getProdInfo1["prod_no"];
			$prodId = $getProdInfo1["prod_id"];
			$prodName = $getProdInfo1["prod_name"];
			$prodPrice = $getProdInfo1["prod_price"];
			$displayProdCat .= '<div class="col col_14 product_gallery">
			<a href="productdetail.php?prodid='.$prodId.'"><img src="images/product/'.$prodNo.'.jpg" width="170" height="150" /></a>
			<h3>'.$prodName.'</h3>
			<p class="product_price">Php '.$prodPrice.'</p>
			<a href="shoppingcart.php?prodid='.$prodId.'" class="add_to_cart">Add to Cart</a></div>';
			
			if(++$prodCatCtr == 3)
				break;
		}
		$displayProdCat .= '<div class="cleaner"></div><a href="index.php?cat=dessert sprinkler" class="more float_r">View all</a><div class="cleaner h50"></div>';
	}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dadads Store</title>
<link href="css/slider.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="css/ddsmoothmenu.css" />

<link rel="stylesheet" type="text/css" href="css/styles.css" />

<script language="javascript" type="text/javascript">

	function clearText(field)
	{
		if (field.defaultValue == field.value) field.value = '';
		else if (field.value == '') field.value = field.defaultValue;
	}
</script>

</head>

<body id="subpage">

<div id="main_wrapper">
	<div id="main_header">
    	<div id="site_title"><h1><a href="#" rel="nofollow">Dadads Store</a></h1></div>
        
        <div id="header_right">
            <div id="main_search">
                <form action="products.php" method="get" name="search_form">
                  <input type="text" value="Search" name="keyword" onfocus="clearText(this)" onblur="clearText(this)" class="txt_field" />
                  <input type="submit" name="Search" value="" alt="Search" id="searchbutton" title="Search" class="sub_btn"  />
                </form>
            </div>
         </div> <!-- END -->
    </div> <!-- END of header -->
    
    <div id="main_menu" class="ddsmoothmenu">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php" class="selected">Products</a></li>
            <li><a href="shoppingcart.php">Cart</a></li>
            <li><a href="checkout.php">Checkout</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
        <br style="clear: left" />
    </div> <!-- end of menu -->
    
    <div class="cleaner h20"></div>
    <div id="main_top"></div>
    <div id="main">
    	
        <div id="sidebar">
            <h3>Categories</h3>
            <ul class="sidebar_menu">
                <li><a href="index.php?cat=juice">Juice</a></li>				
                <li><a href="index.php?cat=junkfood">Junk Food</a></li>
                <li><a href="index.php?cat=dessert sprinkler">Dessert Sprinkler</a></li>
		</ul>
        </div> <!-- END of sidebar -->
        
        <div id="content">
		<?php echo $displayProdCat; ?>
        </div> <!-- END of content -->
        <div class="cleaner"></div>
    </div> <!-- END of main -->
    
    <div id="main_footer">   
        <div class="cleaner h40"></div>
		<center>
			Copyright © 2048 Your Company Name
		</center>
    </div> <!-- END of footer -->   
   
</div>


<script type='text/javascript' src='js/logging.js'></script>
</body>
</html>