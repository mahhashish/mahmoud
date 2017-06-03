<?php
require_once 'DB.php';
require_once 'Classes/Logger/Logger.inc';
include_once 'ErrorMsg.inc';

class Install
{
    
    function InfoExists()
	{
		if (Install::DBExists())
		   return Install::TableExists(Install::getDSN());
		else
			return false;
	}
    
	function DBExists()
	{
       @include "Installer/DB.inc";
    	if (isset($DBType))
		{
		   $dsn = $DBType . "://" . $DBUser . ":" . $DBPassword . "@" . $DBHost . "/" . $DBDatabase;
           $db = DB::connect($dsn);
           if  (DB::isError($db))
              return false;
		   else
		      return true;
        }
        return false;
    }

	function InstallTable()
	{
       $dsn = Install::getDSN();
       if (!Install::TableExists($dsn))
       {
		  Install::AddTable($dsn);
          Install::PopulateTable($dsn);
       }	
	}
	
    
	function getDSN()
	{
	   @include "Installer/DB.inc";
       $dsn = $DBType . "://" . $DBUser . ":" . $DBPassword . "@" . $DBHost . "/" . $DBDatabase;
       return $dsn;
	}
	
	function TableExists($dsn)
    {
       $db = DB::connect($dsn);
       if(in_array('person',$db->getListOf("tables")))
    	   return true;
       else
	      return false;
    }
    
	function AddDatabase($type
                        ,$host
                        ,$user
                        ,$password
                        ,$database
                        )
	{
    	$log = new cErrorLog("InstallMaintenance");
    	$dsn = $type . "://" . $host;
    	$db = DB::connect($dsn);
    	if  (DB::isError($db))
    	{
    	      $log->logMsg(DB_TYPE1.$type.DB_TYPE2,SEVERE);
              InstallDisplay::getInfo();
    		  return false;
    	}
    	$db->disconnect();
    	$dsn = $type . "://" . $user . ":" . $password . "@" . $host;
    	$db = DB::connect($dsn);
    	if  (DB::isError($db))
    	{
    	      $log->logMsg(DB_LOGON1.$type.DB_LOGON2.$user.DB_LOGON3 ,SEVERE);
              InstallDisplay::getInfo();
    		  return false;
    	}
    	$db->disconnect();
    	$dsn = $type . "://" . $user . ":" . $password . "@" . $host . "/" . $database;
    	$db = DB::connect($dsn);
    	if  (DB::isError($db))
    	{
           echo("Creating the database... \n");
           $dsn = $type . "://" . $user . ":" . $password . "@" . $host;
           $db = DB::connect($dsn);
    	   $sql = "CREATE DATABASE $database";
    	   $result = $db->query($sql);
    	   if (DB::isError($result))
    	   {
    	      $msg = $result->getMessage();
    	      $log->logMsg(OUCH.$msg,SEVERE);
              InstallDisplay::getInfo();
    		  return false;
    	   }
    	   echo (" ...database created<br>\n");
    	   Install::recordDetails($type, $host, $user, $password, $database);
    	}
    	$db->disconnect();
    	return true;
	}
    
	function AddTable($dsn)
	{
       $log = new cErrorLog("InstallMaintenance");
       echo "Adding the table...\n";
       if (!file_exists("Installer/Create.sql"))
       {
          $log->logMsg(CREATE_MISSING,SEVERE);
    	  return false;
       }
       $handle = fopen("Installer/Create.sql",'rb');
       if(!$handle)
       {
          $log->logMsg(CREATE_OPEN_ERR,SEVERE);
    	  return false;
       }
       $db = DB::connect($dsn);
       $SQL = fread ($handle,filesize("Installer/Create.sql"));
       $result = $db->query($SQL);
       if (DB::isError($result))
       {
          $msg = $result->getMessage();
          $log->logMsg(TABLE_ERR.$msg,SEVERE);
             InstallDisplay::getInfo();
    	  return false;
       }
       echo " ...table added<br>\n";
       return true;
	}
	
    function PopulateTable($dsn)
    {
       $log = new cErrorLog("InstallMaintenance");
       $db = DB::connect($dsn);
       $SQL = "SELECT * from Person";
       $result = $db->query($SQL);
       if ($result->numRows() == 0)
       {
          echo "Populating the table...";
          if (!file_exists("Installer/Populate.sql"))
          {
             $log->logMsg(POPULATE_MISSING,SEVERE);
    	     return false;
          }
          $handle = fopen("Installer/Populate.sql",'rb');
          if(!$handle)
          {
             $log->logMsg(POPULATE_OPEN_ERR,SEVERE);
             return false;
          }
    	  $cnt = 0;
    	  while ($SQL = fgets($handle))
    	  {
             $result = $db->query($SQL);
             if (DB::isError($result))
             {
                $msg = $result->getMessage();
                $log->logMsg(POPULATE_ERR.$msg." : ".$SQL,SEVERE);
                   InstallDisplay::getInfo();
                return false;
             }
    		 $cnt++;
          }
          echo " ...$cnt rows added<br>\n";
    	  personDisplay::ListView($dsn,"Display\\");
    	  return true;
       }
    }
    
    function recordDetails($type
                          ,$host
                          ,$user
                          ,$password
                          ,$database
                          )
    {
       $handle = fopen("Installer/DB.inc",'wb');
       if(!$handle)
       {
          $log->logMsg(INSTALLER_CREATE_ERR,SEVERE);
    	  return false;
       }
       fwrite ($handle,"<?php\n");
       fwrite ($handle,"\$DBType = \"$type\";\n");
       fwrite ($handle,"\$DBHost = \"$host\";\n");
       fwrite ($handle,"\$DBUser = \"$user\";\n");
       fwrite ($handle,"\$DBPassword = \"$password\";\n");
       fwrite ($handle,"\$DBDatabase = \"$database\";\n");
       fwrite ($handle,"?>\n");
       fclose ($handle);
    }
}
?>