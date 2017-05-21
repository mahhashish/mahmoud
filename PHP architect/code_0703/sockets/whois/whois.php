<?php

// Initialize the error number and error string
// Just to be on the safe side

$errno = $errstr = null;

// Open a connection to the whois server
// NOTE: you must be aware of the terms of use
//       in order to use this service!

$f = fsockopen('whois.internic.net', getservbyname ('whois', 'tcp'), $errno, $errstr, 10);

if (!$f)
	die ("Unable to connect. Error $errno - $errstr\n");
	
// Set a connection timeout, so that
// our script doesn't get stuck waiting
// for data that is never going to arrive
	
socket_set_timeout ($f, 2, 0);

// Send the domain name to the server, followed
// by a linefeed.

fwrite ($f, $_SERVER['argv'][1] . "\n");
	
// Now read through the results until
// the connection is severed.

while ($data = fread ($f, 10000))
	echo $data;
	
// Close the connection
	
fclose ($f);

?>