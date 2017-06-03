<?php

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://php.shaman.ca/curl');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	$session_output = curl_exec($ch);
	$session_info = curl_getinfo($ch);
	curl_close($ch);
	
	print "CURL OUTPUT:\n{$session_output}\n";

	$session_info_output = print_r($session_info, true);
	print "CURL INFO OUTPUT:\n{$session_info_output}\n"	
?>