<?php
	
	$word = 'mitigate';

	$ch = curl_init("dict://dict.org/d:{$word}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);
	curl_close($ch);
	
	print "CURL OUTPUT:\n{$output}\n";

?>

