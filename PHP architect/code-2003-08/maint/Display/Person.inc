<?php
  require_once "DB.php";
  @include_once '../Classes/Logger/Logger.inc';  
  @include_once 'Classes/Logger/Logger.inc';  
  include_once 'ErrorMsg.inc';
  
  class personDisplay
  {

    function HeaderInfo($CSS)
    {
	   session_start();
       echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
       echo "\n<html>";
       echo "\n<head>";
       echo "\n<title>Person Details</title>";
       echo "\n<link rel=\"stylesheet\" type=\"text/css\" href=$CSS>";
       echo "\n</head>";
       echo "\n\n<body>";
       echo "\n<h1>Welcome to the PHP|architect<br>Maintaining Code - test program.</h1>\n";
    
    }
    
    function FooterInfo()
    {
       echo "\n\n<hr width=220 align=left>\n<i>Written by <a href=mailto:graeme@druknet.bt>Graeme Foster</a> 2003.</h1>\n<hr width=220 align=left>";
	   $action = $_POST["action"];
	   if (isset($_GET["ID"]))
	     $id = $_GET["ID"];
       if (isset($id))
	     echo "<form action=Display.php?ID=$id method=POST>\n";
	   else
	     echo "<form action=Display.php method=POST>\n";
	   if (isset($_SESSION['LoggedOn']))
	   {
		 echo "<input type=hidden name=action value=$action>";
         echo "<input type=submit name=auth value=LogOut>";
		 echo "</form>";
	   }
	   else
	   {
         echo "<table rules=rows>\n";
   	     echo "<tr>
  <td width=40 class=logon>User Name<input type=hidden name=action value=$action></td>
  <td width=40><input type=text size=10 name=Name></td>
  <td width=40 class=logon>Password</td>
  <td width=40><input type=password size=10 name=Password></td>
</tr>
<tr>
  <td width=40 align=right><input type=submit name=auth value=LogOn></td>
</tr>
</table>";
	   }
       echo "</form>";
       echo "</body>\n";
       echo "</html>";
    }
	
	function displayPerson($dsn,$path, $CSS, $type)
	{
	  personDisplay::HeaderInfo($CSS);
      switch($type)
      {
         case "Change":
            personDisplay::Change($dsn,$_GET["ID"]
			                     ,$_POST["given"]
								 ,$_POST["family"]);
            break;
         case "Insert":
            personDisplay::Insert($dsn
			                     ,$_POST["given"]
								 ,$_POST["family"]);
            break;
         case "Erase":
            personDisplay::Erase($dsn,$_GET["ID"]);
            break;
         case "List":
            personDisplay::ListView($dsn,$path);
            break;
         case "View":
            personDisplay::DetailView($dsn,$_GET["ID"]);
            break;
         case "Edit":
            personDisplay::DetailEdit($dsn,$_GET["ID"]);
            break;
         case "Add":
            personDisplay::DetailAdd($dsn);
            break;
         case "Delete":
            personDisplay::DetailDelete($dsn,$_GET["ID"]);
            break;
         case "Find":
            personDisplay::ListFind($dsn,"");
            break;
       }
	  personDisplay::FooterInfo();
	}
	
	function ListView($dsn, $path)
	{
      $db = DB::connect($dsn);
	  $SQL = "SELECT * from person";
	  $result = $db->query($SQL);
	  echo "<table rules=rows>\n";
	  while ($row = $result->fetchRow())
	  {
        echo "<form action={$path}Display.php?ID=$row[0] method=POST>
<tr>
  <td width=80>". $row[1] . "</td>
  <td width=80>". $row[2] . "</td>
  <td width=80 align=right><input type=submit name=action value=View></td>
  <td width=60 align=right><input type=submit name=action value=Edit></td>";
       if (isset($_SESSION['LoggedOn']))
	   {
         echo "<td width=80 align=right><input type=submit name=action value=Delete></td>";
	   }
	   echo "</tr>\n</form>";
	  }
	  $result->free();
	  echo "</table>
  <form action={$path}Display.php? method=POST>
  <td width=60 align=right><input type=submit name=action value=Add></td>
  <td width=60 align=right><input type=submit name=action value=Find></td>
</form>";
	}
    
	function ListFind($dsn)
	{
	   if (isset($_POST['needle']))
	   {
	      personDisplay::displayFind($dsn,$_POST['needle']);
	   }
	   else
	   {
	      personDisplay::getNeedle();
	   }
	}
	
	function DetailView($dsn, $id)
	{
	  $db = DB::connect($dsn);
	  $SQL = "SELECT * from person where ID=$id";
	  $result = $db->query($SQL);
	  echo "<table rules=rows>\n";
	  $row = $result->fetchRow();
      $label = "Given Name";
  	  $Name = $row[1];
   	  echo "<tr>
  <td width=120 class=title>$label</td>
  <td width=80 class=body>$Name</td>
</tr>\n";
      $label = "Family Name";
   	  $Name = $row[2];
   	  echo "<tr>
  <td width=120 class=title>$label</td>
  <td width=80 class=body>$Name</td>
</tr>\n";
	  echo "</table>";
      echo "<form action=Display.php?ID=$id method=POST>\n";
	  echo "<table>\n<tr>
  <td width=40 align=right><input type=submit name=action value=List></td>
  <td width=40 align=right><input type=submit name=action value=Edit></td>
  <td width=40 align=right><input type=submit name=action value=Add></td>
  <td width=40 align=right><input type=submit name=action value=Delete></td>";
      echo "</tr>\n</table>\n</form>";
	  $result->free();
    }

	function DetailEdit($dsn, $id)
	{
	  $db = DB::connect($dsn);
	  $SQL = "SELECT * from person where ID=$id";
	  $result = $db->query($SQL);
	  echo "<table rules=rows>\n";
	  $row = $result->fetchRow();
	  echo "<form action=Display.php?ID=$id method=POST>\n";
      $label = "Given Name";
  	  $Name = $row[1];
   	  echo "\t\t<tr><td width=120 class=title>$label</td>
  <td width=80 class=body><input type=text name=given value=$Name></td></tr>\n";
      $label = "Family Name";
   	  $Name = $row[2];
   	  echo "\t\t<tr><td width=120 class=title>$label</td>
  <td width=80 class=body><input type=text name=family value=$Name></td></tr>
  <tr><td width=40 align=right><input type=submit name=action value=Change></td>
  </tr>
</form>
</table>";
      echo "<form action=Display.php?ID=$id method=POST>\n";
	  echo "<table>
<tr>
  <td width=40 align=right><input type=submit name=action value=List></td>
  <td width=40 align=right><input type=submit name=action value=View></td>
  <td width=40 align=right><input type=submit name=action value=Add></td>
  <td width=40 align=right><input type=submit name=action value=Delete></td>
</tr>\n</table>\n</form>";
	  $result->free();
    }

	function DetailAdd($dsn)
	{
	  echo "<table rules=rows>\n";
	  echo "<form action=Display.php method=POST>\n";
      $label = "Given Name";
   	  echo "\t\t<tr><td width=120 class=title>$label</td>
  <td width=80 class=body><input type=text name=given></td></tr>\n";
      $label = "Family Name";
   	  echo "\t\t<tr><td width=120 class=title>$label</td>
  <td width=80 class=body><input type=text name=family></td></tr>
  <tr><td width=40 align=right><input type=submit name=action value=Insert></td>
  </tr>
</form>
</table>";
      echo "<form action=Display.php method=POST>\n";
	  echo "<table>
<tr>
  <td width=40 align=right><input type=submit name=action value=List></td>
</tr>\n</table>\n</form>";
    }

	function DetailDelete($dsn, $id)
	{
	   if (isset($_SESSION['LoggedOn']))
	   {
    	  $db = DB::connect($dsn);
    	  $SQL = "SELECT * from person where ID=$id";
    	  $result = $db->query($SQL);
    	  echo "<table rules=rows>\n";
    	  $row = $result->fetchRow();
          $label = "Given Name";
      	  $FName = $row[1];
       	  echo "<tr>
  <td width=120 class=title>$label</td>
  <td width=80 class=body>$FName</td>
</tr>\n";
          $label = "Family Name";
       	  $SName = $row[2];
       	  echo "<tr>
  <td width=120 class=title>$label</td>
  <td width=80 class=body>$SName</td>
</tr>\n";
    	  echo "</table>";
    	  echo "If you really want to delete 
<font class=hilite>$FName $SName</font> then press the <b>Erase</b> button.";
          echo "<form action=Display.php?ID=$id method=POST>\n";
    	  echo "<table>\n<tr>
  <td width=40 align=right><input type=submit name=action value=Erase></td></tr>
<tr>
  <td width=40 align=right><input type=submit name=action value=List></td>
  <td width=40 align=right><input type=submit name=action value=View></td>
  <td width=40 align=right><input type=submit name=action value=Add></td>
  <td width=40 align=right><input type=submit name=action value=Edit></td>
</tr>\n</table>\n</form>";
	    $result->free();
	  }
      else
	  {
         $log = new cErrorLog("ErrorMaintenance");
	     $log->logMsg(LOG_ON_TO_DELETE,AUTH);
         personDisplay::ListView($dsn,"");     
	  }
    }

    function Change ($dsn, $id, $given, $family)
    {
	   if (personDisplay::validate ($given, $family))
	   {
          $db = DB::connect($dsn);
	      $SQL = "UPDATE person SET FamilyName=\"$family\", GivenName=\"$given\" WHERE ID=$id";
	      $result = $db->query($SQL);
		  personDisplay::DetailView($dsn,$id);
	   }
	   else
	      personDisplay::DetailEdit($dsn,$id);
	}
  
    function Insert ($dsn, $given, $family)
    {
	   if (personDisplay::validate ($given, $family))
	   {
          $db = DB::connect($dsn);
	      $SQL = "INSERT INTO person (ID, GivenName, FamilyName) VALUES (NULL, \"$given\", \"$family\")";
	      $result = $db->query($SQL);
		  if (stristr($dsn,"mysql"))
		  {	  
		     $SQL = "SELECT LAST_INSERT_ID()";
		     $result = $db->query($SQL);
	         $row = $result->fetchRow();
		     personDisplay::DetailView($dsn,$row[0]);
		  }
		  else
		     personDisplay::ListView($dsn,"");
	   }
	   else
	      personDisplay::DetailAdd($dsn);
	}

    function Erase($dsn, $id)
	{
	   if (isset($_SESSION['LoggedOn']))
	   {
          $db = DB::connect($dsn);
	      $SQL = "DELETE FROM person WHERE ID=$id";
	      $result = $db->query($SQL);
	   }
	   else
	   {
          $log = new cErrorLog("ErrorMaintenance");
	      $log->logMsg(LOG_ON_TO_DELETE,AUTH);
	   }
       personDisplay::ListView($dsn,"");     
	}
    
	function validate ($given, $family)
	{
       $log = new cErrorLog("ErrorMaintenance");
	   $valid = true;
	   if ($given == null)
	   {
	      $log->logMsg(BLANK_GNAME,VALID);
		  $valid = false;
	   }
	   if (preg_match("<\d>",$given))
	   {
	      $log->logMsg(NUMBER_IN_GNAME,VALID);
		  $valid = false;
	   }
	   if (preg_match("<\d>",$family))
	   {
	      $log->logMsg(NUMBER_IN_FNAME,VALID);
		  $valid = false;
	   }
	   return $valid;
	}

	function displayFind($dsn, $needle)
	{
      $db = DB::connect($dsn);
      $SQL = "SELECT * FROM person WHERE FamilyName LIKE \"%$needle%\" OR GivenName LIKE \"%$needle%\"";
	  $result = $db->query($SQL);
	  if (DB::isError($result) || $result->numRows()==0)
	  {
         $log = new cErrorLog("ErrorMaintenance");
	     $log->logMsg(NO_MATCH."$needle",SQL);	  
	  }
	  else
	  {
	    echo "<table rules=rows>\n";
	    while ($row = $result->fetchRow())
	    {
          echo "<form action=Display.php?ID=$row[0] method=POST>
<tr>
  <td width=80>". $row[1] . "</td>
  <td width=80>". $row[2] . "</td>
  <td width=80 align=right><input type=submit name=action value=View></td>
  <td width=60 align=right><input type=submit name=action value=Edit></td>";
          if (isset($_SESSION['LoggedOn']))
	      {
            echo "<td width=80 align=right><input type=submit name=action value=Delete></td>";
	      }
	      echo "</tr>\n</form>";
	    }
      }
	  $result->free();
	  echo "</table>
  <form action=Display.php method=POST>
  <td width=60 align=right><input type=submit name=action value=Add></td>
  <td width=60 align=right><input type=submit name=action value=Find></td>
</form>";
	}

	function getNeedle()
	{
	  echo "<table rules=rows>\n";
	  echo "<form action=Display.php method=POST>\n";
      $label = "Name";
   	  echo "\t\t<tr><td width=120 class=title>$label</td>
  <td width=80 class=body><input type=text name=needle></td></tr>
  <tr><td width=40 align=right><input type=submit name=action value=Find></td>
  </tr>
</form>
</table>";
      echo "<form action=Display.php method=POST>\n";
	  echo "<table>
<tr>
  <td width=40 align=right><input type=submit name=action value=List></td>
</tr>\n</table>\n</form>";
    }

  } // end class personDisplay

?>