<?php
// include the init file 
require 'includes/init.php';
require 'includes/autoloader.php';
// display the main stie info
try {
       $data = new Display("main_settings");
       $siteInfo = $data->getLastRecordDESC();
       //`site_name`, `site_dcsc`, `site_tags`, `fb`, `tw`, `yt`, `rss`
       // $siteInfo['site_name'];
       
    } catch (Exception $exc) {
        echo $exc->getMessage();
    }