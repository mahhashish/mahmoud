<?php

	Header("Content-Type: image/png");

	$MAX_VALUES = 8;
	for ($cnt = 1; $cnt <= $MAX_VALUES; $cnt++) $data[$cnt] = rand(1,50);

	$image = imagecreate(300,340);

	$background = imagecolorallocate($image,240,240,240);
	$black = imagecolorallocate($image,0,0,0);
	$gray = imagecolorallocate($image,190,190,190);
	$bar = imagecolorallocate($image,255,127,0);

	imagestring($image,3,13,13,strval(max($data)),$black);

	for ($cnt = 0; $cnt < 4; $cnt++) {
		imageline($image,30,20+($cnt*75),285,20+($cnt*75),$gray);
		imagestring($image,3,13,13+($cnt*75),strval(round(max($data)/(4/(4-$cnt)))),$black);
	}

	imageline($image,30,320,285,320,$black);
	imageline($image,35,10,35,325,$black);

	for ($cnt = 1; $cnt <= $MAX_VALUES; $cnt++) {
		$height = round($data[$cnt] * (300 / max($data)));
		imagefilledrectangle($image,15+$cnt*30,320-$height,$cnt*30+35,320,$bar);
		imagerectangle($image,15+$cnt*30,320-$height,$cnt*30+35,320,$black);
		imagestring($image,3,22+$cnt*30,323,strval($cnt),$black);
	}

	imagepng($image);
	imagedestroy($image);
?>