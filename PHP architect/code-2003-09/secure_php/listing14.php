<?php

//basicauth.php
if ($PHP_AUTH_USER != "user" || $PHP_AUTH_PW !="pass") {
	header('WWW-Authenticate: Basic realm="server.com"');
	header("HTTP/1.0 401 Unauthorized");
else {
	echo "Successful";
}

?>

