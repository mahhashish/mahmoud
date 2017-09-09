<?

	// our counter image will be jpeg
	Header("Content-Type: image/jpeg");

	// we need a number in $number variable
	$number = $_GET['number'];
	if ((!is_numeric($number)) || ($number < 0)) $number = 0;

	$target_width = (10 * strlen(strval($number)))+2;
	$target_height = 20;

	$image = imagecreatetruecolor($target_width,$target_height);

	$blue = imagecolorallocate($image,0,0,127);
	$white = imagecolorallocate($image,255,255,255);

	imagefilledrectangle($image,0,0,$target_width-1,$target_height-1,$blue);

	// loop for copy of each digits image data from source image onto target image
	for ($counter = 0; $counter < strlen(strval($number)); $counter++) {
		$digit = substr(strval($number),$counter,1);
		imagestring($image,5,2+$counter*10,2,$digit,$white);
	}

	// generate jpeg image stream in the best quality
	imagejpeg($image,'',100);

	// free allocated memory for both images
	imagedestroy($image);
	imagedestroy($source_image);

?>