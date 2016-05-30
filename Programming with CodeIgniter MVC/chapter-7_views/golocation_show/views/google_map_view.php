<!DOCTYPE html">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<html>
<head>

<script src="http://code.jquery.com/jquery-latest.js" type='text/javascript'></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>


<script>

var latitude 	= 0;
var longitude 	= 0;
		
		
function show_on_map () {

//alert ( "POS:" + latitude + longitude );  

var url_to_show = '<?php echo base_url(); ?>index.php/gmaps/user_location/' + longitude + '/' + latitude ; 
//alert ( url_to_show );
 
$(location).attr('href', url_to_show );

}

$(document).ready(function() {
	
	$('#getmylocation').click(checkLocation);
	
	function checkLocation() {
		// Check if the Browser support the HTML5 Geolocation 
		if (navigator.geolocation) {
		    $('#notifications').html ( 'fetching your location, wait...' );
		    $('#notifications').css ( 'color', 'blue' );
			
		 // Try to fecth the lat / long of the browsing user and provide the callbacks:
		 // Success : getLocation
		 // Failure : locationFail 
			navigator.geolocation.getCurrentPosition(getLocation, locationFail);
		}
		
		else {
		    $('#notifications').html ( 'Sorry, your browser settings does not enable fetchinh your Geo location...' ); 
			}
	
	} // ends checkLocation()
	
	//this is what happens if getCurrentPosition is successful (getCurrentPosition successCallback)
	function getLocation(position) { 
	  
		latitude 	= position.coords.latitude;
		longitude 	= position.coords.longitude;
				
	   $('#notifications').html ( 'Your approx. position : (' + latitude + ',' + longitude + ')' );//.delay(5000).html ( 'will show your location on the map...' );
	   $('#notifications').css ( 'color', 'green' );
	   setTimeout ( show_on_map, 2000);   
 
 
	   
	}
	
	//this is what happens if getCurrentPosition is unsuccessful (getCurrentPosition errorCallback)
	function locationFail() {
	  $('#notifications').html ( 'Sorry, your browser could not fetch your location ...' ); 
	  $('#notifications').css ( 'color', 'red' );
			
	}
	
});

</script>


<!-- render all the JS provided by the Library via the rendering controller  -->
<?php echo $map['js']; ?>
</head>
<body>
<H3>Codeigniter Powered CI GoogleMaps Library : <H3>
<HR></HR>
<DIV style='background:lightgreen;width:300px;'>
<span id='notifications'>...</span>
</DIV>
<HR></HR>

<ul>


<!-- Let the User Always Get Back to the default Zoom out with all places marked   -->
<li><?php echo anchor ("index.php/gmaps", '<B>See All Locatons</B>' ) ?></li>
<li id='getmylocation' style='cursor:pointer;color:blue;decorations:underline' ><u>Show Me My Location</u></li>
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
