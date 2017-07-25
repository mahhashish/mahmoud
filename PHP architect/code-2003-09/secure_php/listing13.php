<?php

//nopwrules.php
if (isset($_GET['newpw'])) {
	$fp=fopen('pw.txt', 'wb');
	fputs($fp, $_GET['newpw']);
	fclose($fp);
	echo "New Password Set\n";
} else {
	echo "Specify Password\n";
}

?>