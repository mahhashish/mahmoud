<?php
require_once('./class.Menu.php');
$menu = new Menu('menu_example_1.xml', 'default');
$menu->setTarget('info');
$menu->buildMenu();
$menu->displayMenu();
?>
