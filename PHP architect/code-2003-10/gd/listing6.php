<?php
	$MAX_VALUES = 11;
	for ($cnt = 1; $cnt <= $MAX_VALUES; $cnt++) $data[$cnt] = rand(1,10);
	for ($cnt = 1; $cnt <= $MAX_VALUES; $cnt++) $total += $data[$cnt];

	Header("Content-Type: image/png");

	$image = imagecreate(350,300);

	$background_color = imagecolorallocate($image,255,255,255);
	$black = imagecolorallocate($image,0,0,0);

	$lastAngle = 0;

	for ($cnt = 1; $cnt <= $MAX_VALUES; $cnt++) {
		$newAngle = $lastAngle + ((360/100)*($data[$cnt]/($total/100)));

		$color = imagecolorallocate($image,rand(0,255),rand(0,255),rand(0,255));
		imagefilledarc($image, 119, 149, 200, 200, $lastAngle, $newAngle, $color,IMG_ARC_PIE);
		imagefilledrectangle($image,259,20+20*$cnt,269,32+20*$cnt,$color);
		imagestring($image,3,280,20+20*$cnt,"".round(($data[$cnt]/($total/100)),2)." %",$black);

		$lastAngle = $newAngle;
	}
	imageellipse($image,119,149,200,200,$black);

	imagepng($image);
	imagedestroy($image);
?>