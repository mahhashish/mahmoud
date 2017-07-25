<?php

//poorauthresourcepatched.php
include('auth.inc');
$authobj = new auth($_GET['un'], $_GET['pw']);
if (!$authobj->isvalid)
	die;
if (!is_numeric($_GET['res']))
	die;
$fp=fopen('res' . $_GET['res'] . '.txt', 'rb');
fpassthru($fp);
fclose($fp);

?>