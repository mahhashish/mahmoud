<!DOCTYPE html> 
<meta content="text/html; charset=utf-8" />
<?PHP 

/* data from controller : 

$email 
$email_valid
	
$url 
$url_valid 
$url_exist 



*/

$validation_text  = ( $email_valid ) ? "Is Valid " : "Is Not Valid!"; 
$validation_url   = ( $url_valid   ) ? "Is Valid " : "Is Not Valid!"; 
$exist_url        = ( $url_exist   ) ? "Exist "    : "Not exist!"; 


?>
<body style="text-align:left;color:blue;">

<H1>Main Page</H1>
<HR></HR>
<div style="float:left" >
The Email : <?=$email; ?> <?=$validation_text; ?> 
</div>
<div style="clear:both;"></div>

<HR></HR>
<div >
  The url : <?=$url; ?>   <?=$validation_url; ?> and <?=$exist_url ; ?> 
  <?=anchor($url, '[visit the url]', array ("target" => "_blank", "title" => "opens a new Tab"));  ?>
</div>
<div style="clear:both;"></div>

<HR></HR>
<?php echo anchor ('homepage/page_b', 'Navigate me to page B' ) ?>

</body>
</html>


