<?php
// include the init file 
include 'includes/init.php';
include 'includes/autoloader.php';
// display the main stie info
try {
       $data = new Display('content');
       $siteInfo = $data->getContents();
       
    } catch (Exception $exc) {
        echo $exc->getMessage();
    }