<?php

require_once ("gpfr/gpfr.php");
require_once ("gpfr/xmlrpc/types.php");
require_once ("gpfr/xmlrpc/client.php");

class UpdateServiceClient
{
	var $oUpToDateCall 	= null;
	var $oUpToDateRes	= null;
	var $oUpdateFromCall	= null;
	var $oUpdateFromRes	= null;

	var $szServiceURL	= null;

	var $oClient		= null;

	function UpdateServiceClient ($a_szServiceURL)
	{
		$this->constructor($a_szServiceURL);
	}

	function constructor ($a_szServiceURL)
	{
		$this->oUpToDateCall = new gpfr_xmlrpc_methodcall
		(
			"UpdateService.getIsUpToDate",
			array
			(
				new gpfr_xmlrpc_stringT,
				new gpfr_xmlrpc_stringT
			)
		);
		
		$this->oUpToDateRes = new gpfr_xmlrpc_methodresponse
		(
			new gpfr_xmlrpc_booleanT
		);

		$this->oUpdateFromCall = new gpfr_xmlrpc_methodcall
		(
			"UpdateService.getUpdateFromVersion",
			array
			(
				new gpfr_xmlrpc_stringT,
				new gpfr_xmlrpc_stringT
			)
		);

		$this->oUpdateFromRes = new gpfr_xmlrpc_methodresponse
		(
			new gpfr_xmlrpc_structT
			( array
			  (
				  "Version"	=> new gpfr_xmlrpc_stringT(),
				  "ReleaseNoteURLs"	=> new gpfr_xmlrpc_arrayT
				  (
					  new gpfr_xmlrpc_stringT()
				  ),
				  "DownloadURLs"	=> new gpfr_xmlrpc_arrayT
				  (
					  new gpfr_xmlrpc_stringT()
				  ),
				  "YourVersionIsInsecure" => new gpfr_xmlrpc_booleanT()
			  )
			)
		);

		$this->szServiceURL = $a_szServiceURL;

		$this->oClient = new gpfr_xmlrpc_client();
	}

	function doGetIsUpToDate ($a_szAppName, $a_szAppVersion, &$a_Result)
	{
		$l_aParams[0] = $a_szAppName;
		$l_aParams[1] = $a_szAppVersion;

		return $this->oClient->doCallWebService
		(
			$this->szServiceURL,
			$l_aParams,
			$this->oUpToDateCall,
			$this->oUpToDateRes,
			$a_Result
		);
	}

	function doGetUpgradeFromVersion ($a_szAppName, $a_szAppVersion, &$a_Result)
	{
		$l_aParams[0]	= $a_szAppName;
		$l_aParams[1]	= $a_szAppVersion;

		return $this->oClient->doCallWebService
		(
			$this->szServiceURL,
			$l_aParams,
			$this->oUpdateFromCall,
			$this->oUpdateFromRes,
			$a_Result
		);
	}
}

?>
