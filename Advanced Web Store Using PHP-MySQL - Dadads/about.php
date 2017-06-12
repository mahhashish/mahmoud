
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
            <li><a href="shoppingcart.php">Cart</a></li>
            <li><a href="checkout.php">Checkout</a></li>
            <li><a class="selected" href="about.php">About</a></li>
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
       		<h2>Company Information</h2> <!-- change rakan ijo gusto na title -->
            <div class="col col_13">
				<a href="images/product/01;.jpg"><img src="images/product/01.jpg" style="width:250px; height: 210px; margin-left:15px; border: 2px double;" alt="Image 10" /></a>
			</div>
			<div class="col col_13 no_margin_right">
				<h4><span style="font-weight:bold; color:#001;">Address</span></h4>
				<p><span >Nueva Street, Surigao City</span></p>
				<p style="border-top-style:double;"><span style="font-weight:bold; color:#001;">Phone:</span> 090909090990</p>
				<p><span style="font-weight:bold; color:#001;">Email:</span> mycompany@gmail.com</p>
			</div>
			<div class="cleaner"></div>
			<div style="margin-top:30px;">
				<h2>About Us</h2>
				<p>information regarding your group.</p>
			</div>
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