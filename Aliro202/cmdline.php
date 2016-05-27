<?php

// Can be run from a command line to instigate action within Aliro

ob_start();
$_REQUEST['option'] = 'commandline';
require('index.php');
