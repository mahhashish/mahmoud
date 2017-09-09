<?php

$source_image = imagecreatefromjpeg("photo.jpg");
$background_color = imagecolorallocate($source_image,127,127,127);
$image = imagerotate($source_image,-90,$background_color);
imagejpeg($image);

?>