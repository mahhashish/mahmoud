<?php
/**
 * establish database connection
 *
 * @author	Jason E. Sweat
 * @since	2002-12-17
 */
error_reporting(E_ALL);

require_once('adodb/adodb.inc.php');
define('MYSQL_DT_FMT', '%Y-%m-%d');

$conn = &ADONewConnection('mysql');
//$conn->debug=true;
$conn->Connect('localhost', 'phpa', 'phpapass', 'phpa');
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

?>
