<?php

	$ch = curl_init('http://php.shaman.ca/curl/');
	curl_exec($ch);
	curl_close($ch);

?>