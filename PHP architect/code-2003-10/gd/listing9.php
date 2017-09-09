<?php

	require_once("listing7.php");

	Header("Content-Type: image/jpeg");

	$image = imagecreatefromjpeg("beach.jpg");

	$target = croppedimage($image,0);

	imagejpeg($target);

?>