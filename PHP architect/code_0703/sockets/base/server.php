<?php

function stat_output()
{
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
	
	// Return the result
	
	echo $data;
}

// Simply call our function and exit

stat_output();

?>