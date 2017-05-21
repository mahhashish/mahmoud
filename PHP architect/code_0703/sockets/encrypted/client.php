<?php

$iv = 'asldekajsdkalsdasdasdalskdjasdklasdkjasdasdjaskld';
$key = 'my secret is so secret';

// This function decrypts the data passed to it

function decrypt_data ($data, $iv, $key)
{
	/* Open the cipher */
	$td = mcrypt_module_open ('rijndael-256', '', 'ofb', '');

	$iv = substr ($iv, 0, mcrypt_enc_get_iv_size($td));
	$key = substr ($key, 0, mcrypt_enc_get_key_size ($td));

	/* Initialize encryption module for decryption */
	mcrypt_generic_init ($td, $key, $iv);

	/* Decrypt encrypted string */
	$decrypted = mdecrypt_generic ($td, $data);

	/* Terminate decryption handle and close module */
	mcrypt_generic_deinit ($td);
	mcrypt_module_close ($td);

	return $decrypted;
}

$hostname = 'localhost';
$port = 2134;
$timeout = 10;

// Initialize error number and error string
// Just to be on the safe side

$errno = $errstring = null;

// Open a connection to the server

$f = fsockopen ($hostname, $port, $errno, $errstring, $timeout);

if (!$f)
	die ("Unable to connect");

// Set a connection timeout, so that
// our script doesn't get stuck waiting
// for data that is never going to arrive
	
socket_set_timeout ($f, $timeout, 0);
	
// Read the data until we run out of it

while ($temp = fread ($f, 10000))
{
	$data .= $temp;
}

// Decrypt and output it

echo decrypt_data ($data, $iv, $key);

// Close the connection

fclose ($f);

?>