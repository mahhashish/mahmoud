<?php

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://php.shaman.ca/curl');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	$output = curl_exec($ch);
	curl_close($ch);
	
	print "CURL OUTPUT:\n{$output}\n";

?>