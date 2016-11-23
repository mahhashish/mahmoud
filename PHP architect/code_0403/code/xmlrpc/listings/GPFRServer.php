<?php

require_once ("gpfr/gpfr.php");
require_once ("loaddata.php");
require_once ("UpdateService.php");
require_once ("gpfr/xmlrpc/types.php");
require_once ("gpfr/xmlrpc/server.php");

// ------------------------------------------------------------------------
// S E R V E R   C R E A T I O N
//
// Create the server object

$l_oServer = new gpfr_xmlrpc_server();

// ------------------------------------------------------------------------
// P R O C E S S O R   D E C L A R A T I O N
//
// The server must have an object to call to do the actual processing
// for each web service.
//
// In this case, $l_oAppWatcher is already declared in loaddata.php.

$l_oUpdateService = new UpdateService($l_oAppWatcher);

// ------------------------------------------------------------------------
// S E R V I C E   D E C L A R A T I O N
//
// For each service in turn, do the following
//
// 1. Create a methodcall object
// 2. Create the corresponding methodresponse object
// 3. Register them with the XML-RPC server

//
// SERVICE #1 :: UpdateService.getIsUpToDate
//

$l_oMethodCall = new gpfr_xmlrpc_methodcall
(
	"UpdateService.getIsUpToDate",
	array
	(
		new gpfr_xmlrpc_stringT,	// AppName
		new gpfr_xmlrpc_stringT		// AppVersion
	)
);

$l_oMethodResponse = new gpfr_xmlrpc_methodresponse
(
	new gpfr_xmlrpc_booleanT
);

$l_oServer->doRegisterService
(
	new gpfr_xmlrpc_methodtoobject
	(
		$l_oUpdateService,
		"doGetIsUpToDate",
		$l_oMethodCall,
		$l_oMethodResponse
	)
);
	
//
// SERVICE #2 :: UpdateService.getUpdateFromVersion
//

$l_oMethodCall = new gpfr_xmlrpc_methodcall
(
	"UpdateService.getUpdateFromVersion",
	array
	(
		new gpfr_xmlrpc_stringT,	// AppName
		new gpfr_xmlrpc_stringT		// AppVersion
	)
);

$l_oMethodResponse = new gpfr_xmlrpc_methodresponse
(
	new gpfr_xmlrpc_structT
	(
		array
		(
			"Version"		=> new gpfr_xmlrpc_stringT,
			"ReleaseNoteURLs" 	=> new gpfr_xmlrpc_arrayT
			(
				new gpfr_xmlrpc_stringT()
			),
			"DownloadURLs" 		=> new gpfr_xmlrpc_arrayT
			(
				new gpfr_xmlrpc_stringT()
			),
			"YourVersionIsInsecure"	=> new gpfr_xmlrpc_booleanT
		)
	)
);

$l_oServer->doRegisterService
(
	new gpfr_xmlrpc_methodtoobject
	(
		$l_oUpdateService,
		"doGetUpdateFromVersion",
		$l_oMethodCall,
		$l_oMethodResponse
	)
);

// ------------------------------------------------------------------------
// H A N D L E   I N C O M I N G   R E Q U E S T S
//
// 1. Extract XML from a data source
// 2. Feed the XML into the server
// 3. Echo the response back to the client

$l_szSource = "php://input";
$l_szXML = implode(" ", file($l_szSource));

echo $l_oServer->getXMLLeader()
	. $l_oServer->getResponseFromXML($l_szXML);

// ------------------------------------------------------------------------
// S C R I P T   E N D
// ------------------------------------------------------------------------

?>