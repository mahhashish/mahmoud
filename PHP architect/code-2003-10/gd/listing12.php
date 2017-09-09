<?php

	Header("Content-Type: image/jpeg");

	$image = imagecreatefromjpeg("beach.jpg");

	$image2 = imagecreatetruecolor(round(imagesx($image)/2),round(imagesy($image)/2));
	imagecopyresampled($image2,$image,0,0,0,0,round(imagesx($image)/2),round(imagesy($image)/2),imagesx($image),imagesy($image));

	imagejpeg($image2);

?>