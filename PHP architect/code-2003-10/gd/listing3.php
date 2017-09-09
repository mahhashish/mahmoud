<?php

	header("Content-Type: image/jpeg");

	$image = imagecreatetruecolor(600,128);

	$white = imagecolorallocate($image,255,255,255);
	$black = imagecolorallocate($image,0,0,0);

	imagefilledrectangle($image,0,0,599,127,$white);

	for ($counter = 1; $counter<=5; $counter++)
		imagestring($image,$counter,10,($counter*20),"ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789",$black);

	imagejpeg($image);
	imagedestroy($image);

?>
