<?php

$java = new Java ("java.lang.System");
$currentTime = $java->currentTimeMillis();
echo "The current time in milliseconds since epoch is: ". $currentTime;

?>
