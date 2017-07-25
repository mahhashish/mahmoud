<?php

//envdisclosure.php
error_reporting(E_ALL);
$conn=mysql_connect('dbserver.server.com', 'user', 'pw');
$fp=fopen('/mnt/data/file1.txt', 'rb');

?>
