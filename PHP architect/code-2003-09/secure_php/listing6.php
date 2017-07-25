<?php

//pathtraversalpatched1.php
if is_numeric($_GET['page']) {
	$fp=fopen('/var/www/files/file' . $_GET['page'] . '.txt', 'rb');
	fpassthru($fp);
	fclose($fp);
} else
	echo "Please specify a valid page number to view\n";

?>