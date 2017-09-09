<?

	// our counter image will be jpeg
	Header("Content-Type: image/jpeg");

	// we need a number in $number variable
	$number = $_GET['number'];
	if ((!is_numeric($number)) || ($number < 0)) $number = 0;

	$source_image = imagecreatefromjpeg("numbers.jpg");

	$source_width = imagesx($source_image);
	$source_height = imagesy($source_image);

	// test for correct width of source image
	if (($source_width % 10) != 0) die("width of source image must be dividable by ten");

	// size of one digit in source image file
	$single_size = ($source_width / 10);

	// we need an array of x-position of each digit in source image file
	for ($counter = 0; $counter < 10; $counter++) $position[$counter] = ($counter * $single_size);

	$target_width = ($source_width / 10) * strlen(strval($number));
	$target_height = $source_height;

	$image = imagecreatetruecolor($target_width,$target_height);

	// loop for copy of each digits image data from source image onto target image
	for ($counter = 0; $counter < strlen(strval($number)); $counter++) {
		$digit = substr(strval($number),$counter,1);
		imagecopy($image,$source_image,(($counter)*$single_size),0,$position[$digit],0,$single_size,$source_height);
	}

	// generate jpeg image stream in the best quality
	imagejpeg($image,'',100);

	// free allocated memory for both images
	imagedestroy($image);
	imagedestroy($source_image);

?>