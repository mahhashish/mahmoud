<!DOCTYPE html> 
<meta content="text/html; charset=utf-8" />
<?PHP 
/* data from controller : 
$since
$past 
*/

?>
<body style="text-align:left;color:blue;">

<H1>Page B</H1>
<HR></HR>
<div style="float:left" >
Since : <?=$since; ?> past  <?=$past; ?> years
</div>
<div style="clear:both;"></div>

<HR></HR>
<?php echo anchor ('homepage', 'Back to Home Page' ) ?>

</body>
</html>


