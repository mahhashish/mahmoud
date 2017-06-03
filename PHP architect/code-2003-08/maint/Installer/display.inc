<?php

require_once "Installer/Install.inc";

class InstallDisplay
{
    // Request the user to provide the DB.inc information
    function getInfo()
    {
       echo "
       <FORM method=POST action=index.php>
       <table>
    	 <tr>
    	    <td>Please select your datbase</td>
    	    <td>&nbsp</td>
    		<td><SELECT name=DBType>
    	      <OPTION value=mysql>mySQL</Option>
    		  <OPTION value=pgsql>postgreSQL</Option>
    		  <OPTION value=ibase>InterBase</Option>
    		  <OPTION value=msql>Mini SQL</Option>
    		  <OPTION value=mssql>Microsoft SQL Server</Option>
    		  <OPTION value=oci8>Oracle 7/8/8i</Option>
    		  <OPTION value=odbc>Open Database Connectivity</Option>
    		  <OPTION value=sybase>SyBase</Option>
    		  <OPTION value=ifx>Informix</Option>
    		  <OPTION value=fbsql>FrontBase</Option>
    		</SELECT>
    		</td>
    	 </tr>
    	 <tr>
    	    <td>Please select your host server</td>
    	    <td></td>
    		<td><INPUT type=text name=DBHost value=localhost></td>
    	 </tr>
    	 <tr>
    	    <td>Please enter your username</td>
    	    <td></td>
    		<td><INPUT type=text name=DBUser value=root></td>
    	 </tr>
    	 <tr>
    	    <td>Please select your password</td>
    	    <td></td>
    		<td><INPUT type=password name=DBPassword></td>
    	 </tr>
    	 <tr>
    	    <td>Please enter the database name</td>
    	    <td></td>
    		<td><INPUT type=text name=DBDatabase value=PHPaMaint></td>
    	 </tr>
    	 <tr>
    		<td><INPUT type=submit name=Run value=install></td>
    	 </tr>
    	 </table>
    	 </FORM>";
    }
    
    function HeaderInfo()
    {
       echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
       echo "\n<html>";
       echo "\n<head>";
       echo "\n<title>Installer</title>";
       echo "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"PHParchitect.css\">";
       echo "\n</head>";
       echo "\n\n<body>";
       echo "\n<h1>Welcome to the PHP|architect installer program.</h1>\n";
    
    }
    
    function FooterInfo()
    {
       echo "\n\n<hr>\n<i>Written by <a href=mailto:graeme@druknet.bt>Graeme Foster</a> 2003.</h1>\n";
       echo "</body>\n";
       echo "</html>";
    }
    
    function displayInstall()
    {
       InstallDisplay::HeaderInfo();
       if (isset($_POST['DBType']))
       {
           $DBType = $_POST["DBType"];
           $DBUser = $_POST["DBUser"];
           $DBPassword = $_POST["DBPassword"];
           $DBHost = $_POST["DBHost"];
           $DBDatabase = $_POST["DBDatabase"];
           if(Install::AddDatabase($DBType, $DBHost, $DBUser, $DBPassword, $DBDatabase))
		      Install::InstallTable();
        }
        elseif (Install::DBExists())
        {
           require "Installer/DB.inc";
           if(Install::AddDatabase($DBType, $DBHost, $DBUser, $DBPassword, $DBDatabase))
		      Install::InstallTable();
        }
        else
        {
           InstallDisplay::getInfo();
        }
        InstallDisplay::FooterInfo();
    }
}
?>