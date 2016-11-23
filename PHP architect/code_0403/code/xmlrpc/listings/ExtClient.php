<?php

require_once ("gpfr/gpfr.php");
require_once ("gpfr/http/post.php");

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
		$this->constructor ($a_szServiceURL);
	}

	function constructor ($a_szServiceURL)
	{
		$this->szServiceURL = $a_szServiceURL;
	}

	function doGetIsUpToDate ($a_szAppName, $a_szAppVersion, &$a_Result)
	{
		$l_aParams[0] = $a_szAppName;
		$l_aParams[1] = $a_szAppVersion;

		$l_szXML = xmlrpc_encode_request
		(
			"UpdateService.getIsUpToDate",
			$l_aParams
		);

		return $this->doCallWebService
		(
			$this->szServiceURL,
			$l_szXML,
			$a_Result
		);
	}

	function doGetUpgradeFromVersion ($a_szAppName, $a_szAppVersion, &$a_Result)
	{
		$l_aParams[0]	= $a_szAppName;
		$l_aParams[1]	= $a_szAppVersion;

		$l_szXML = xmlrpc_encode_request
		(
			"UpdateService.getUpdateFromVersion",
			$l_aParams
		);

		return $this->doCallWebService
		(
			$this->szServiceURL,
			$l_szXML,
			$a_Result
		);
	}

	function doCallWebService ($a_szURL, $l_szXML, &$a_Result)
	{	
		// create the headers

		$l_aHeaders = array
		(
			"User-Agent: GPFR/XML-RPC",
			"Content-type: text/xml"
		);

		// make the call

		$l_oPost = new gpfr_http_post();
		$l_oR = $l_oPost->doRawPost
		(
			$a_szURL, $l_aHeaders, $l_szXML, $l_szResults
		);
		if ($l_oR->getResult() != GPFR_E_NONE)
			return $l_oR;

		$a_Result = xmlrpc_decode ($l_szResults);

		return new gpfr_result(GPFR_E_NONE);
	}
}

?>
