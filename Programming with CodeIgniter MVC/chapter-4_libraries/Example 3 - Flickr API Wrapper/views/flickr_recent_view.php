<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<div>
<H1>Flickr Recent Uploads</H1>
<p >
<table border="1" style='background-color:#b0c4de;' >
<tr><td>Photos in Poll</td><td><?=$settings['RECENT_PHOTOS']; ?></td></tr>
<tr><td>Min. Width Filter</td><td><?=$settings['DEFAULT_RES']; ?>Px</td></tr>
<tr><td>GPS Filter</td><td><?=$settings['GPS_ENABLED'] ? "With GPS" : "With/Without GPS"; ?></td></tr>
</p>

<table border="1"  style='background-color:#009900;'  >
<tr>
<th>User Uploaded</th><th>User photos Count</th><th>Photo ID</th><th>Original Size MP</th><th>Was Taken</th>
</tr>
<?PHP foreach($photos as $photo )
          { 
// get the owner id : 
   $uid = $photo['owner'];

// Get User Info :
$user_info = $this->flickr_wrapper->flickrUserInfo ($uid);



$photos 	= number_format ($user_info["photos"]);
$mp_res     = (int) ((( $photo['o_width' ] * $photo['o_height'] ) / 1000000)  +  1); 
?> 
	<tr> 
 	  <td> <?=$photo['ownername'] ?></td>
 	  <td> <?=$photos ?></td>
	  <td> <?=$photo['id'] ?></td>
	  <td> <?=$mp_res ?></td>
	  <td> <?=$photo['datetaken'] ?></td>
	</tr>
<?PHP      } ?>
	 	
</table>	
</div>
</body>
</html>