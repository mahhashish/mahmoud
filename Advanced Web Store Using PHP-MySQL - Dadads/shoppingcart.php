<?php
	session_start();
	include("admin/php/connect_to_mysql.php");
	include("admin/php/myFunctions.php");
	
	if(!empty($_GET['prodid'])){
		$pid = $_GET['prodid'];
		$wasFound = false;
		$i = 0;
		if(!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1){
			$_SESSION["cart_array"]=array(0=>array("productID"=>$pid,"quantity"=>1));
		}else{
			foreach($_SESSION["cart_array"] as $each_product){
				$i++;
				while(list($key,$value)=each($each_product)){
					if($key=="productID" && $value==$pid){	
						array_splice($_SESSION["cart_array"],$i-1,1,array(array("productID"=>$pid,"quantity"=>$each_product ['quantity']+1)));
						$wasFound=true;
					}
				}		
			}
			if($wasFound==false){
				array_push($_SESSION["cart_array"],array("productID"=>$pid,"quantity"=>1));
			}
		}
		header("location:shoppingcart.php");
		exit();
	}
	//-------------------------------------------------------------------------------------------------
	$submit = $_POST['btnUpdate'];
	if($submit == "Update"){
		$x = 0;
		//echo $_POST['txtQuan2'];
		//echo $_POST['txtHoldProdId0'];
		foreach($_SESSION["cart_array"] as $each_product){
			$i++;
			$quantity = $_POST['txtQuan'.$x];
			$prodStock = $_POST['txtHoldQuan'.$x];
			$prodAdjustId = $_POST['txtHoldProdId'.$x++];
			if($quantity<1){ $quantity = 1; }
			if($quantity>$prodStock){ $quantity = $prodStock; }
			while(list($key,$value)=each($each_product)){
				array_splice($_SESSION["cart_array"],$i-1,1,array(array("productID"=>$prodAdjustId,"quantity"=>$quantity)));
			}		
		}
		
	}
	//-------------------------------------------------------------------------------------------------
	if(!empty($_GET['cid']) || isset($_GET['cid'])){
		$removeKey = $_GET['cid'];
		if(count($_SESSION["cart_array"])<=1){
			unset($_SESSION["cart_array"]);
		}else{
			unset($_SESSION["cart_array"]["$removeKey"]);
			sort($_SESSION["cart_array"]);
		}
	}
	//-------------------------------------------------------------------------------------------------
	$cartTitle = "";
	$cartOutput = "";
	$cartTotal = "";
	if(!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1){
		$cartOutput="<h2 align='center'> Your shopping cart is empty </h2>";
	}else{
		$x = 0;
		$cartTitle .= '<form name="shoppingcart_form" action="shoppingcart.php" method="post" /><table width="700px" cellspacing="0" cellpadding="5">
				<tr bgcolor="#CCCCCC">
                        	<th width="220" align="left">Image </th> 
                        	<th width="140" align="left">Name </th> 
                       	  	<th width="100" align="center">Quantity </th> 
							<th width="60" align="center">Stock </th> 
                        	<th width="60" align="right">Price </th> 
                        	<th width="60" align="right">Total </th> 
                        	<th width="90"> </th></tr>';
		$i = 0;
		foreach($_SESSION["cart_array"] as $each_product){
			$product_id = $each_product['productID'];
			$sql=mysql_query("select * from tblproduct where prod_id='$product_id' limit 1");
			while($row=mysql_fetch_array($sql)){
				$prodNo = $row["prod_no"];
				$prodID = $row["prod_id"];
				$prodName = $row["prod_name"];
				$prodPrice = $row["prod_price"];
				$prodQuan = $row["prod_quan"];
			}
			$pricetotal=$prodPrice*$each_product['quantity'];
			$cartTotal= number_format($pricetotal+$cartTotal,2);
			$cartOutput .= '<tr><td><img style="border: 2px solid;" src="images/product/'.$prodNo.'.jpg" width="150" height="120" /></td> 
				<td>'.$prodName.'</td> 
				<td align="center"><input type="hidden" name="txtHoldProdId'.$i.'" value="'.$prodID.'" /><input name="txtQuan'.$i.'" type="text" value="'.$each_product['quantity'].'" style="width: 40px; text-align: center" /> </td>
				<td align="center"><input type="hidden" name="txtHoldQuan'.$i.'" value="'.$prodQuan.'" /> '.$prodQuan 	.' pcs</td> 
				<td align="right">Php '.$prodPrice.'</td> 
				<td align="right">Php '.$pricetotal.'</td>
				<td align="center"> <a href="shoppingcart.php?cid='.$i++.'"><img src="images/remove_x.gif" alt="remove" /><br />Remove</a> </td></tr>';
		}
		$_SESSION['checkoutCartTotal'] = $cartTotal;
		$cartOutput .= '<tr>
                        	<td colspan="3" align="right"  height="40px">Have you modified your basket? Please click here to <input class="btn_upd" type="submit" name="btnUpdate" value="Update" />&nbsp;&nbsp;</td>
                            <td align="right" style="background:#ccc; font-weight:bold"> Total: </td>
                            <td colspan="2" align="left" style="background:#ccc; font-weight:bold;">Php '.$cartTotal.' </td>
                            <td style="background:#ccc; font-weight:bold"> </td>
						</tr>
					</table>
                    <div style="float:right; width: 215px; margin-top: 20px;">
                    
                        <div class="checkout"><a href="checkout.php" class="more">Proceed to Checkout</a></div>
                    	
                    </div></form>';
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
            <li><a href="products.php">Products</a></li>
            <li><a class="selected" href="shoppingcart.php">Cart</a></li>
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
		<?php echo $cartTitle; ?>
		<?php echo $cartOutput; ?>
            
		</div> <!-- end of content -->
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