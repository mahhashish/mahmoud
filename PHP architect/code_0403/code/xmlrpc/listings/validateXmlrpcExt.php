<?php

// phpa/xmlrpc/validateXmlrpcExt.php
//		Code to pass the XMLRPC.com validation suite, using PHP's
//		XML-RPC Extension
//
//		Originally written for php|architect
//
// Author	Stuart Herbert
//		(stuart@gnqs.org)
//
// ------------------------------------------------------------------------

$l_szXML = implode(" ", file("php://input");

function arrayOfStructsTest ()
{
}

function countTheEntities ()
{
}

function easyStructTest ()
{
}

function echoStructTest ()
{
}

function manyTypesTest ()
{
}

function moderateSizeArrayCheck ()
{
}

function nestedStructTest ()
{
}

function simpleStructReturnTest ()
{
}

$l_hServer = xmlrpc_server_create();

$l_aMethods = array
(
	"arrayOfStructsTest",
	"countTheEntities",
	"easyStructTest",
	"echoStructTest",
	"manyTypesTest",
	"moderateSizeArrayCheck",
	"nestedStructTest",
	"simpleStructReturnTest"
);

foreach ($l_aMethods as $l_szMethod)
{
	xmlrpc_server_register_method
	(
		"validator1." . $l_szMethod,
		$l_szMethod
	);
}

xmlrpc_server_call_method($l_hServer, $l_szXML, null);

xmlrpc_server_destroy($l_hServer);

?>