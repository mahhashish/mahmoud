<?php
	
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	Here we will include the Gateway.php file thanks
	to our friends at the AMFPHP Project
  -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */

	include "../flashservices/app/Gateway.php";

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	Here we will create a new Gateway object that will
	handle passing data to and from Flash and PHP
  -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */

	$gateway = new Gateway();
	$gateway->setBaseClassPath("services/");
 	$gateway->service();
?>
