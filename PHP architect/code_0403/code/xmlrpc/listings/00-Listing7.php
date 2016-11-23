<?php

// ------------------------------------------------------------------------
// P R O C E S S O R   D E F I N I T I O N
//
// We need an object to handle the actual work for each web service
// $L_oAppWatcher is an object declared in loaddata.php

require_once ("loaddata.php");
require_once ("UpdateService.php");

$l_oUpdateService = new UpdateService($l_oAppWatcher);

// ------------------------------------------------------------------------
// S E R V E R   D E F I N I T I O N
//
// By telling the XML-RPC Extension to send all web services through this
// simple dispatchMethods function, we can completely re-use the web service
// originally created for GPFR's XML-RPC library

require_once ("gpfr/prepend.php");

/**
 * handles web services for the XML-RPC Extension's server
 *
 * This function is called by the XML-RPC Extension's server.  It
 * calls the correct function on our UpdateService object.
 *
 * This is an example of how services written for the GPFR XML-RPC
 * library can also be re-used with the XML-RPC Extension in the
 * future - without having to re-write the service!
 *
 * @param	string	$a_szMethodName
 *		Contains the name of the web service that has been
 *		called
 *
 * @param	array	$a_aData
 *		Contains the parameters to the web service
 *
 * @param	array	$a_aUserData
 *		Not used in this example
 *
 * @return	array
 *		The data to send back to the client
 */

function dispatchMethods ($a_szMethodName, $a_aData, $a_aUserData)
{
	// translate the name of the web service into the name of
	// the method to be called

	$l_szMethod = str_replace
	(
		"UpdateService.get", "doGet", $a_szMethodName
	);

	// next step is to convert the contents of the XML-RPC Server's
	// array into individual parameters for calling the method

	$l_iParams  = count($a_aData);

	$l_szEval = "\$l_oUpdateService->" 
	. $l_szMethod
	. "(";

	for ($l_i = 0; $l_i < $l_iParams; $l_i++)
	{
		$l_szEval .= "\$a_aData[$l_i], ";
	}

	$l_szEval .= "\$l_Result);";

	// we are all set.
	// let's call it

	eval($l_szEval);

	// return the result

	return $l_Result;
}

//
// Create a server, to register the web services with
//

$l_hServer = xmlrpc_server_create();

//
// list of web services
// add any new ones to this array
//

$l_aMethods = array
(
	"getIsUpToDate",
	"getUpgradeFrom"
);

//
// register each of our web services
// this ensures they all go through our nifty dispatch routine
//

foreach ($l_aMethods as $l_szMethod)
{
	xmlrpc_server_register_method
	(
		$l_hServer,
		"UpdateService." . $l_szMethod,
		"dispatchMethods"
	);
}

// ------------------------------------------------------------------------
// H A N D L E   T H E   R E Q U E S T
//
// 1. Obtain the XML containing the method call data
// 2. Pass this into our XML-RPC server
// 3. Echo the resulting method response back to the client

$l_szSource = "php://input";
$l_szXML = implode(" ", file($l_szSource));

echo xmlrpc_server_call_method($l_hServer, $l_szXML, null);

// ------------------------------------------------------------------------
// D E S T R O Y   T H E   S E R V E R
//
// The server is a PHP resource, and must be destroyed once we're done

xmlrpc_server_destroy($l_hServer);

// ------------------------------------------------------------------------
// S C R I P T   E N D
// ------------------------------------------------------------------------

?>
