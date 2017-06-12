<?php
require_once('admin/inc/connect.ini.php');
require_once('admin/inc/functions.ini.php');

    $result              = $conn->query("SELECT * FROM $t_paypal");
    $row                  = $result->fetch(PDO::FETCH_ASSOC);

$month1       = $row['1month'];
$month3       = $row['3month'];
$month6       = $row['6month'];
$paypal_email = $row['paypal_email'];
$email_to       = $row['email_to'];
$email_from       = $row['email_from'];
$email_subject       = $row['email_subject'];
$item_name    = $row['item_name'];
?>
 <!DOCTYPE html>
<html lang="en">
  <head> <meta charset="utf-8">


<meta name="rating" content="safe for kids" />
<meta name="author" content="ianjgough.com" />
<meta name="robots" content="index, follow" />
    <title>Advertise Here Banner Bar</title>
<script type="text/javascript" src="<?php echo $jQueryjs; ?>"></script>
<link rel="stylesheet" href="inc/advertise.css" type="text/css" media="screen" />




</head>
<body>
<?php
if(!isset($_POST['Submit'])) {

?>
<div id="body-container">

<div id="content">

<div class="orderdiv">
<h1 style="text-align:center; margin-top:20px;"><span class="big">ADVERTISE HERE</span></h1>
<h2 class="c2">Your Ad on the Bar?</h2>
<p><br />
If you'd like to promote your product, site or service simply fill in this form and submit payment.<br /> All payments are made via Paypal and if you have any questions please send email to: <a href="mailto:<?php echo $email; ?>?Subject=Advertiser%20enquiry"><?php echo $email; ?></a>.</p>

<p>
Your 125px by 125px banner will be displayed on the bar below across this site and show up on all pages <a href="http://ianjgough.com/wp-content/demos/bannerbar/">Banner Bar</a> is installed.<br /></p>
<ul>
  <li>1 x month - &pound;<?php echo $month1; ?></li>
  <li>3 x month - &pound;<?php echo $month3; ?></li>
  <li>6 x month - &pound;<?php echo $month6; ?></li>
</ul>


<?php


$client_banners = $conn->query("SELECT client_banners FROM $t_settings")->fetchColumn();




$result = $conn->query("SELECT count(*) FROM $t_client_banners WHERE status !='e' AND status !='deny'")->fetchColumn();

if($result >= $client_banners){
echo "<br /><p>Sorry we are currently not accepting any ads or have no spaces available.</p>";
}
else {


$paymentForm = <<<paymentForm
  


<form action=" " method="post" id="form1">
		

<p id="re">
Fields marked with <span class="redText">*</span> are required.<br/>
</p>

<p> 
<label for="name">Your Name:</label> 
<input type="text" name="name" maxlength="50" size="30" placeholder="Enter your name" id="name" title="Enter your name"  tabindex="1" class="error"/> 
<span class="redText">*</span>
</p>

<p> 
<label for="email">Email address:</label> 
<input type="text" name="email" maxlength="50" size="30" placeholder="Enter your email address" id="email"  title="Enter your email address" tabindex="2" class="error2"/> 
<span class="redText">*</span>
</p>

<p> 
<label for="link">Link URL:</label> 
<input type="text" name="link" maxlength="90" size="30" placeholder="Enter your link URL" id="link" title="Enter your link URL" tabindex="3"/>
<span class="redText">*</span>

</p>

<p> 
<label for="image">Banner URL:</label> 
<input type="text" name="image" maxlength="90" size="30" placeholder="Enter your Banner URL" id="image" title="Enter your Banner URL"  tabindex="4"/>
<span class="redText">*</span>

</p>


<p>
<input type="submit" name="Submit" value="Continue" id="Submit" tabindex="5"/>
</p>

</form>
paymentForm;

echo $paymentForm;


}
?>
</div>
</div>

</div>
<?php } 

if(isset($_POST['Submit'])) {
     
    //$email_subject = "Possible Advertiser";
     
    function died($error) {
        // your error code can go here
        echo "We are very sorry, but there were error(s) found with the form you submitted. ";
        echo "These errors appear below.<br /><br />";
        echo $error."<br /><br />";
        echo "<br /><a href=\"javascript:history.go(-1)\">Please go back and fix these errors.</a><br />";
        die();
    }
     
    // validation expected data exists
    if(!isset($_POST['name']) ||
        !isset($_POST['email']) ||
        !isset($_POST['link']) ||
        !isset($_POST['image'])) {
        died('We are sorry, but there appears to be a problem with the form you submitted.');       
    }
     
    $name = $_POST['name']; // required
    $email = $_POST['email']; // required
    $link = $_POST['link']; // required
    $image = $_POST['image']; // required
     
    $error_message = "";
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
  if(!preg_match($email_exp,$email)) {
    $error_message .= 'The Email Address you entered does not appear to be valid.<br />';
  }
    $string_exp = "/^[A-Za-z .'-]+$/";
  if(!preg_match($string_exp,$name)) {
    $error_message .= 'The Name you entered does not appear to be valid.<br />';
  }
  if(strlen($link) < 2) {
    $error_message .= 'The link you entered do not appear to be valid.<br />';
  }
if(strlen($image) < 2) {
    $error_message .= 'The image you entered do not appear to be valid.<br />';
  }
  if(strlen($error_message) > 0) {
    died($error_message);
  }

?>
<!-- Paypal -->
<?php 
function getIp() {
    $ip = $_SERVER['REMOTE_ADDR'];
 
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
 
    return $ip;
}
?>
<div id="body-container">
<div id="content">
<div class="orderdiv">
<h1 style="text-align:center; margin-top:20px;"><span class="big">ADVERTISE HERE</span></h1>
<h2 class="c2">Thank you for sending us your details</h2>
<p class="center">Please proceed to payment and your ad will be up usually within 24 hours.  Your be emailed when your ad is live or if there are any problems with your ad.</p>

<form class="paypal" action="<? echo $notify_url; ?>" method="post" id="paypal_form" target="_blank">    
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="lc" value="GB">
<input type="hidden" name="item_name" value="<?php echo $item_name; ?>">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="cn" value="Add special instructions to the seller">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="rm" value="1">
<input type="hidden" name="currency_code" value="GBP">
<input type="hidden" name="shipping" value="0.00">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_paynow_LG.gif:NonHosted">
<input type="hidden" name="on0" value="Months">

<div class="center">Please Select:-<br />
<select name="os0">
<option value="1 Month">1 Month £<?php echo $month1; ?> GBP</option>
<option value="3 Months">3 Months £<?php echo $month3; ?> GBP</option>
<option value="6 Months">6 Months £<?php echo $month6; ?> GBP</option>
</select> 
<br />
<br />

<input type="hidden" name="on1" value="Link URL">
<input type="hidden" name="os1" maxlength="200" value="<?php echo $link; ?>">
<input type="hidden" name="on2" value="Image URL">
<input type="hidden" name="os2" maxlength="200" value="<?php echo $image; ?>">
<input type="hidden" name="currency_code" value="GBP">
<input type="hidden" name="option_select0" value="1 Month">
<input type="hidden" name="option_amount0" value="<?php echo $month1; ?>">
<input type="hidden" name="option_select1" value="3 Months">
<input type="hidden" name="option_amount1" value="<?php echo $month3; ?>">
<input type="hidden" name="option_select2" value="6 Months">
<input type="hidden" name="option_amount2" value="<?php echo $month6; ?>">
<input type="hidden" name="option_index" value="0">
<input type="hidden" name="custom" value="<?php echo getIp()."|".$name."|".$email; ?>">

<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_paynow_LG.gif" name="submit" alt="PayPal — The safer, easier way to pay online.">
<img alt="OnebyOne" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</div>
</form>

</div>
</div>

</div>
<!-- Paypal -->
<?php } ?>
</body>
</html>