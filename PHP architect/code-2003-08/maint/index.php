<?php
$classPath = "Classes/Logger/";
require_once $classPath.'Logger.inc';
require_once "Installer/DB.inc";
require_once 'Installer/display.inc';
require_once 'Installer/Install.inc';
require_once 'Display/Person.inc';

$log = new cErrorLog("ErrorMaintenance");

if (Install::infoExists())
{
   require "Installer/DB.inc";
   $dsn = $DBType . "://" . $DBUser . ":" . $DBPassword . "@" . $DBHost . "/" . $DBDatabase;
   personDisplay::displayPerson($dsn, "Display\\", "PHParchitect.css", "List");
}
else
{
   InstallDisplay::displayInstall();
}


?>