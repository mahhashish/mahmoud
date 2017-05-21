<?php

$iv = 'asldekajsdkalsdasdasdalskdjasdklasdkjasdasdjaskld';
$key = 'my secret is so secret';

// This function encrypts the data passed to it

function encrypt_data ($data, $iv, $key)
{
	/* Open the cipher */
	$td = mcrypt_module_open ('rijndael-256', '', 'ofb', '');

	$iv = substr ($iv, 0, mcrypt_enc_get_iv_size($td));
	$key = substr ($key, 0, mcrypt_enc_get_key_size ($td));

	/* Intialize encryption */
	mcrypt_generic_init ($td, $key, $iv);

	/* Encrypt data */
	$encrypted = mcrypt_generic ($td, $data);

	/* Terminate encryption handler */
	mcrypt_generic_deinit ($td);
	mcrypt_module_close ($td);

	return $encrypted;
}

function stat_output()
{
	global $key;
	global $iv;
	
	// Get memory information
	
	$data = "\n\nMEMORY INFORMATION:\n";
	$data .= "===================\n\n";
	$data .= `cat /proc/meminfo` . "\n\n";
	
	// Get processor usage information
	
	$temp = explode (' ', `cat /proc/loadavg`);

	$data .= "LOAD AVERAGES:\n";
	$data .= "==============\n\n";
	$data .= "1 minute: {$temp[0]}\n";
	$data .= "5 minutes: {$temp[1]}\n";
	$data .= "1s minutes: {$temp[2]}\n";
	$data .= "Number of processes: {$temp[3]}\n";
	$data .= "(running/total)\n\n";
	
	preg_match ('/([\d]+)\s*([\d]+)\s*([\d]+)/', `cat /proc/sys/fs/file-nr`, $temp);
	
	// Get filesystem information
	
	$data .= "FILESYSTEM:\n";
	$data .= "===========\n\n";
	$data .= "# of total file handles: " . ($temp[3]) . "\n";
	$data .= "# of files open: " . ($temp[1] - $temp[2]) . "\n";
	$data .= "# of file handles available: " . ($temp[3] - $temp[1] + $temp[2]) . "\n\n\n";

	// Encrypt our results and return them
	
	echo encrypt_data ($data, $iv, $key);
}

// Output our results

stat_output();

?>