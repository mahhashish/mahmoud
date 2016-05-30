<!DOCTYPE html">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<html>
<head>
<script src="http://code.jquery.com/jquery-latest.js" type='text/javascript'></script>

<!-- render all the JS provided by the Library via the rendering controller  -->
<?php echo $map['js']; ?>
</head>
<body>
<H3>Codeigniter Powered CI GoogleMaps Library : <H3>
<HR></HR>
<ul>

<!-- Let the User Always Get Back to the default Zoom out with all places marked   -->
<li><?php echo anchor ("index.php/gmaps", '<B>See All Locatons</B>' ) ?></li>

<?PHP 
$i=0;
foreach ($locations as $location ) {  
// Show user all the possible Zoom-Ins of the defined places: 
$controller = $controllers["$i"]; 
$i++;
?>
<li><?php echo anchor ("index.php/gmaps/$controller", "Zoom-In to ".$location ) ?> </li>
<?PHP } ?>

</ul>
<HR></HR>
<?php echo $map['html']; ?>		
</body>
</html>
