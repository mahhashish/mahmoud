<?php

//nopwrulespatched.php
if (ispwvalid($_GET['newpw'])) {
	$fp=fopen('pw.txt', 'wb');
	fputs($fp, $_GET['newpw']);
	fclose($fp);
	echo "New Password Set\n";
} else {
	echo "Specify Password\n";
}

function ispwvalid($pw) {
	// ensure that pw is 6 chars in length and contains numbers
	if (strlen($pw) < 6)
		return false;
	for ($i=0; $i<9; $i++) {
		if (substr_count($pw, $i) > 0)
			return true;
	}
	return false;
}

?>
