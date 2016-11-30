<?php

date_default_timezone_set('Africa/Nairobi');

	try{

	$username = 'root';
	$password = 'LOflower';
	$database = 'session_db';
	$server   = 'localhost';

	$con = new pdo("mysql:server=$server;dbname=$database",$username,$password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

   }catch(Exception $e)
    
     {


	echo  '<center><b>Connectivity Issue. Please Contact ICT.</b></center>';
     file_put_contents('errors/error.log' , date('y-m-d h:i:sa').$e->getmessage(), FILE_APPEND|LOCK_EX);

       
       }




?>