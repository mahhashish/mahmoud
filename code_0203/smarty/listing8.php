<?php

include_once('debug.inc.php');
include_once('smarty.inc.php');

$user_name='John Doe';

items[]=array('name'=>'Apple', 'price'=>'10.2', 'isnew'=>false);
items[]=array('name'=>'Pear', 'price'=>'1.0', 'isnew'=>true);
items[]=array('name'=>'Pecl', 'price'=>'0.5', 'isnew'=>true);

$vars=compact('user_name', 'items');
smarty('smarty_mx.dwt', $vars);

?>