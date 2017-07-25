<?php

require_once('./class.MenuXML.php');

$xml = new MenuXML('./menu_example_1.xml');
echo "<pre>";
echo $xml->heading ."\n";
echo $xml->state ."\n";
echo $xml->type ."\n";
print_r($xml->menu);
echo "</pre>";

?>
