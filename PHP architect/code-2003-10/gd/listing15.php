<?php

	Header("Content-Type: image/jpeg");

	$image = imagecreatefromjpeg("sample_original.jpg");

	for ($counter1 = 0; $counter1 < imagesx($image); $counter1++)
		for ($counter2 = 0; $counter2 < imagesy($image); $counter2++) {

			$rgb = imagecolorat($image,$counter1,$counter2);

			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;

			$r = 255 - $r;
			$g = 255 - $g;
			$b = 255 - $b;

			$color = imagecolorallocate($image,$r,$g,$b);

			imagesetpixel($image,$counter1,$counter2,$color);

		}


	imagejpeg($image);

	imagedestroy($image);

?>