<?php

require_once('class.SqlMenu.php');

$sql_menu = new SqlMenu('php|a - sql example', 'default', 'localhost', 'menu', 'www', 'www');
$sql_menu->setTarget('info');
$sql_menu->buildMenu();
$sql_menu->displayMenu();

?>
