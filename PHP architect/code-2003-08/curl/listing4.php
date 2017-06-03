<?php

    $ch = curl_init('http://php.shaman.ca/curl/protected/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, 'testuser:testpass');
    curl_setopt($ch, CURLOPT_COOKIEJAR, '/var/tmp/phpa_cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/var/tmp/phpa_cookies.txt');
    curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'var1' => 'data1',
                                                'var2' => '@listing4.php'));
    $output = curl_exec($ch);
    curl_close($ch);
    print "FIRST CURL OUTPUT:\n{$output}\n";

    $ch = curl_init('http://testuser:testpass@php.shaman.ca/curl/protected/?var3=data3');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, '/var/tmp/phpa_cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/var/tmp/phpa_cookies.txt');
    $output = curl_exec($ch);
    curl_close($ch);
	print "SECOND CURL OUTPUT:\n{$output}\n";

?>