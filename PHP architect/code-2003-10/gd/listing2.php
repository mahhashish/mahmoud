<?php

	header("Content-Type: image/jpeg");

	$image = imagecreatetruecolor(300,200);
	$blue_color = imagecolorallocate($image,0,0,255);
	$red_color = imagecolorallocate($image,255,0,0);
	imagefilledrectangle($image,0,0,299,199,$blue_color);
	imagefilledrectangle($image,5,5,294,194,$red_color);

	imagejpeg($image);
	imagedestroy($image);

?>