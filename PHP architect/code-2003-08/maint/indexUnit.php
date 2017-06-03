<?php
$classPath = "Classes/Logger/";
require_once $classPath.'Logger.inc';
require_once 'unitTest.inc';

$log = new cErrorLog("ErrorMaintenance");
addTestCases($log);
?>