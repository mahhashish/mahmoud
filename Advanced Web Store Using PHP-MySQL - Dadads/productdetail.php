<?php
	include("admin/php/connect_to_mysql.php");
	include("admin/php/myFunctions.php");
	
	$prodID = $_GET['prodid'];
	$prodAvail = "";
	$txtQuanDisable = "";
	$otherProdList = "";
	$otherProdCtr = 0;
	if(!empty($prodID)){
		$sqlSelectSpecProd = mysql_query("select * from tblproduct where prod_id = '$prodID'") or die(mysql_error());
		$getProdInfo = mysql_fetch_array($sqlSelectSpecProd);
		$prodNo = $getProdInfo["prod_no"];
		$prodid = $getProdInfo["prod_id"];
		$prodName = $getProdInfo["prod_name"];
		$prodDescr = $getProdInfo["prod_descr"];
		$prodCat = $getProdInfo["prod_cat"];
		$prodPrice = $getProdInfo["prod_price"];
		$prodQuan = $getProdInfo["prod_quan"];
		
		if($prodQuan >= 1){
			$prodAvail = "In Stock";
		}
		else{
			$prodAvail = "Out of Stock";
			$txtQuanDisable = "disabled";
		}
		$sqlSelectOtherProduct = mysql_query("select * from tblproduct order by date_added desc") or die(mysql_error());
		$sqlCountOtherProduct = mysql_num_rows($sqlSelectOtherProduct);
		if($sqlCountOtherProduct >=1 ){
			while($getOtherProductInfo = mysql_fetch_array($sqlSelectOtherProduct)){
				$otherProdNo = $getOtherProductInfo["prod_no"];
				$otherProdId = $getOtherProductInfo["prod_id"];
				$otherProdName = $getOtherProductInfo["prod_name"];
				$otherProdPrice = $getOtherProductInfo["prod_price"];
				
				$otherProdList .= '<div class="col col_14 product_gallery">
				<a href="productdetail.php?prodid='.$otherProdId.'"><img src="images/product/'.$otherProdNo.'.jpg" width="170" height="150"" /></a>
				<h3>'.$otherProdName.'</h3>
				<p class="product_price">Php '.$otherProdPrice.'</p>
				<a href="shoppingcart.php?prodid='.$otherProdId.'" class="add_to_cart">Add to Cart</a></div>';
				
				if(++$otherProdCtr >= 3){
					$otherProdList .= '<a href="index.php" class="more float_r">View all</a>';
					break;
				}
			}
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
        	<h2>Product Details</h2>
            <div class="col col_13">
		<a href="images/product/<?php echo $prodNo; ?>.jpg" title="Lady Shoes"><img src="images/product/<?php echo $prodNo; ?>.jpg" style="width:250px; height: 210px; margin-left:15px; border: 2px double;" alt="Image 10" /></a>
            </div>
            <div class="col col_13 no_margin_right">
                <table>
					<tr>
						<td colspan="2" height="30" width="160"><br></td>
					</tr>
                    <tr>
                        <td height="30" width="160">Price:</td>
                        <td>Php <?php echo $prodPrice; ?></td>
                    </tr>
                    <tr>
                        <td height="30">Availability:</td>
                        <td><?php echo $prodAvail; ?></td>
                    </tr>
                    <tr>
                        <td height="30">Name:</td>
                        <td><?php echo $prodName; ?></td>
                    </tr>
                    <tr>
                        <td height="30">Category:</td>
                        <td><?php echo $prodCat; ?></td>
                    </tr>
                    <!-- <tr><td height="30">Quantity</td><td><input type="text" name="txtQuantity" value="1" style="width: 20px; text-align: right" /></td></tr> -->
                </table>
                <div class="cleaner h20"></div>
                <a href="shoppingcart.php?prodid=<?php echo $prodid; ?>" class="add_to_cart">Add to Cart</a>
			</div>
            <div class="cleaner h30"></div>
            
            <h5><strong>Product Description</strong></h5>
            <p><?php echo $prodDescr; ?></p>	
            
            <div class="cleaner h50"></div>
            
            <h4>Other Products</h4>
	    <?php echo $otherProdList; ?>    
            <div class="cleaner"></div>
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