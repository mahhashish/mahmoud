<?php

if (isset($_GET['cbstr'])) {
   $callbackstring = base64_decode(strtr($_GET['cbstr'], '-_', '+/'));
   /********* Insert custom environment handling here ***********/
   require_once($callbackstring.'/aliro.php');
   aliro::getInstance()->startup();
   /********* End of custom environment handling ***********/
}

/******** DO NOT MODIFY ***********/
require_once('phpGrid.php');     
/**********************************/
