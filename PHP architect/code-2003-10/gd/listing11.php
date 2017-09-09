<?php

	require_once("croppedimagepreview.php");

	Header("Content-Type: image/jpeg");

	$image = imagecreatefromjpeg("beach.jpg");

	$target = croppedimagepreview($image,1);

	imagejpeg($target);

?>