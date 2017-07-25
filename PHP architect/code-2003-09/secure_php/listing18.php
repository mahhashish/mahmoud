<?php

//poorauthresource.php
if (!is_numeric($_GET['res']))
	die;
$fp=fopen('res' . $_GET['res'] . '.txt', 'rb');
fpassthru($fp);
fclose($fp);

?>
