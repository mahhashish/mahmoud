<?php
// Multi Two part messages include the data base type between these messages
define("DB_TYPE1","I can't find the ");
define("DB_TYPE2"," database. Please make sure that it has been installed on the server");
define("DB_LOGON1","I can't log on to the ");
define("DB_LOGON2"," database. Please make sure that the user name ");
define("DB_LOGON3"," has been created.");

define("OUCH","Something didn't go to plan. "); // add the error message
define("CREATE_MISSING","Sorry but the required file Installer/Create.sql is missing.");
define("CREATE_OPEN_ERR","Sorry but the required file Installer/Create.sql cannot be opened.");
define("TABLE_ERR","Tried to create the table, but oops: ");

define("POPULATE_MISSING","Sorry but the required file Installer/Populate.sql is missing.");
define("POPULATE_OPEN_ERR","Sorry but the required file Installer/Populate.sql cannot be opened.");
define("POPULATE_ERR","Tried to populate the table, but oops: ");

define("INSTALLER_CREATE_ERR","Failed to create the file Installer/DB.inc, sorry.");
?>