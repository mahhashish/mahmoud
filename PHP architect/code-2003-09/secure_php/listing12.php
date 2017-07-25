<?php

//trustedvaluesformhandlerpatched.php
session_start();
if (!isset($_SESSION['productid']) || !isset($_GET['cc']))
	die;
// Call me paranoid, but sanity check session variable
if (!isnumeric($_SESSION['productid']))
	die;
$ssql="SELECT Price FROM Products WHERE ProductID = " .  $_SESSION['productid'];
$conn=mysql_connect('127.0.0.1', 'dbuser', 'dbpw');
$res=mysql_query($ssql, $conn);
$resarr=mysql_fetch_row($res);
mysql_close($conn);
mail("billing@server.com", "New Bill", "Bill card " . $_GET['cc'] .. "\nFor amount: \$" . $resarr[0]);
echo "Order placed\n";

?>