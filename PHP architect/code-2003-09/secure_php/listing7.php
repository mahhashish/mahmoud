<?php

//pathtraversalpatched2.php
if isset($_GET['page']) {
	$fp=fopen(stripfilename($_GET['page']), 'rb');
	fpassthru($fp);
	fclose($fp);
} else
	echo "Please specify a page to view\n";

function stripfilename($filename) {
	$filename=str_replace('.', '', $filename);
	return str_replace('/', '', $filename);
}

?>