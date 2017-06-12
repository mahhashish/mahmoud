<?php

$tf_host     = 'localhost';
$tf_dbname   = 'tinyforum';
$tf_username = 'root';
$tf_password = 'LOflower';

$tf_handle = @mysql_connect($tf_host, $tf_username, $tf_password);

if(!$tf_handle){
	die('connection_faild');
}

$tf_dbselect = mysql_select_db($tf_dbname);
if(!$tf_dbselect){
	mysql_close($tf_handle);
	die('dbname faild');
}

mysql_query("SET NAMES 'utf8'");

function tf_db_close(){
	global $tf_handle;
	mysql_close($tf_handle);
}


?>