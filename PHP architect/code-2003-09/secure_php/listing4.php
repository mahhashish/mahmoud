<?php

//phpcodeinjectpatched.php
if isset($_GET['page']) {
	$fp=fopen($_GET['page'], 'rb');
	fpassthru($fp);
	fclose($fp);
} else
	echo "Please specify a page to view\n";

?>