<?php

// sqlinject.php
$ssql="SELECT ArticleContents FROM Articles WHERE ArticleID = " .  $_GET['artid'];
$conn=mysql_connect('127.0.0.1', 'dbuser', 'dbpw');
$res=mysql_query($ssql, $conn);
while ($resarr=mysql_fetch_row($res)) {
	echo "<span id=\"article\">" . $resarr[0] . "</span>\n\";
}
mysql_close($conn);

?>
