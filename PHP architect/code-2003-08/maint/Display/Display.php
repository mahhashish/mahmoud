<?php
require_once 'Person.inc';
include_once 'ErrorMsg.inc';

if (isset($_POST["auth"]))
{
	$auth = $_POST["auth"];
	if ($auth=="LogOn")
	{
	  $user=$_POST["Name"];
	  $password = $_POST["Password"];
	  // Very crude test
	  session_start();
	  if ($user=="Me" and $password == "Please")
	    $_SESSION['LoggedOn']=true;
	  else
	  {
	    $log = new cErrorLog("ErrorMaintenance");
        $log->logMsg(LOG_ON_FAILED,AUTH);
	  }
	}
	else
	{
	   session_start();
	   unset($_SESSION['LoggedOn']);
	}
	// If necessary change an action back to an initial action
	// Change goes back to Edit
	// Insert goes back to Add
	// Erase goes back to Delete
    if(isset($_POST["action"]))
	{
      switch($_POST["action"])
      {
         case "Change":
            $_POST["action"]="Edit";
            break;
         case "Insert":
            $_POST["action"]="Add";
            break;
         case "Erase":
            $_POST["action"]="Delete";
            break;
	  }
	}
}

if (isset($_POST["action"]))
{
	$action = $_POST["action"];
	selectAction ($action);
}
else
{
   $log = new cErrorLog("ErrorMaintenance");
   $log->logMsg(DIRECT_LINK,USER);
   require_once "../Installer/DB.inc";
   $dsn = $DBType . "://" . $DBUser . ":" . $DBPassword . "@" . $DBHost . "/" . $DBDatabase;
   personDisplay::ListView($dsn,"");
}

function selectAction($action)
{
   require_once "../Installer/DB.inc";
   $dsn = $DBType . "://" . $DBUser . ":" . $DBPassword . "@" . $DBHost . "/" . $DBDatabase;
   personDisplay::displayPerson($dsn,"","../PHParchitect.css", $action);
}

?>