<?php

	Header("Content-Type: image/jpeg");

	$image = imagecreatefromjpeg("sample_original.jpg");

	for ($counter1 = 0; $counter1 < imagesx($image); $counter1++)
		for ($counter2 = 0; $counter2 < imagesy($image); $counter2++) {

			$rgb = imagecolorat($image,$counter1,$counter2);

			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;

			$luminance = (0.30 * $r) + (0.59 * $g) + (0.11 * $b);  // luminance

			$color = imagecolorallocate($image,$luminance,$luminance,$luminance);

			imagesetpixel($image,$counter1,$counter2,$color);

		}


	imagejpeg($image);

	imagedestroy($image);

?>