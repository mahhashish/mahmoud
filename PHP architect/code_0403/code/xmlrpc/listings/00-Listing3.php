<?php

// UpdateService.php
//		Web service object.
//
// Author	Stuart Herbert
//		(stuart@gnqs.org)
//
// ------------------------------------------------------------------------

require_once ("AppWatcher.php");

class UpdateService
{
	var $oAppWatcher = null;

	/**
	 * constructor
	 *
	 * @param	AppWatcher	$a_oAppWatcher
	 *		An object containing information about released
	 *		versions of applications
	 */

	function UpdateService (&$a_oAppWatcher)
	{
		$this->oAppWatcher =& $a_oAppWatcher;
	}

	/**
	 */

	function doGetIsUptodate ($a_szAppName, $a_szAppVersion, &$a_Results)
	{
		// basic checks

		$l_oR = $this->doBasicChecks($a_szAppName, $a_szAppVersion);
		if ($l_oR->getResult() != GPFR_E_NONE)
			return $l_oR;

		// at this point, we're happy to call the underlying
		// function

		$a_Results = $this->oAppWatcher->getIsUpToDate
		(
			$a_szAppName, $a_szAppVersion
		);

		return new gpfr_result(GPFR_E_NONE);
	}

	function doGetUpdateFromVersion ($a_szAppName, $a_szAppVersion, 
					 &$a_Results)
	{
		// basic checks

		$l_oR = $this->doBasicChecks($a_szAppName, $a_szAppVersion);
		if ($l_oR->getResult() != GPFR_E_NONE)
			return $l_oR;

		// step 1 - is an upgrade available?

		if ($this->oAppWatcher->getIsUpToDate($a_szAppName, 
						      $a_szAppVersion))
		{
			return new gpfr_result(UPDATEWATCH_E_NOUPDATEAVAILABLE);
		}

		// step 2 - yes there is

		$l_szVersion = $this->oAppWatcher->getRecommendedUpgrade
		(
			$a_szAppName, $a_szAppVersion
		);

		$a_Results["Version"]		= $l_szVersion;
		$a_Results["ReleaseNoteURLs"]	
		= $this->oAppWatcher->getReleaseNotesURLs ($a_szAppName, 
							  $l_szVersion);
		$a_Results["DownloadURLs"]	
		= $this->oAppWatcher->getDownloadURLs ($a_szAppName, 
						       $l_szVersion);
		$a_Results["YourVersionIsInsecure"] 
		= $this->oAppWatcher->getIsInsecure($a_szAppName, 
						    $a_szAppVersion);

		return new gpfr_result(GPFR_E_NONE);
	}

	// ----------------------------------------------------------------
	// H E L P E R   F U N C T I O N S
	// ----------------------------------------------------------------

	function doBasicChecks ($a_szAppName, $a_szAppVersion)
	{
		// do we know about this application?

		if (!$this->oAppWatcher->getIsRegisteredApp($a_szAppName))
			return new gpfr_result(UPDATEWATCH_E_UNKNOWNAPP);

		// do we know about this version?
		if (!$this->oAppWatcher->getIsRegisteredVersion($a_szAppName, 
								$a_szAppVersion))
			return new gpfr_result(UPDATEWATCH_E_UNKNOWNVERSION);

		// all basic checks passed
		return new gpfr_result(GPFR_E_NONE);
	}
}

gpfr_error::doRegisterResult("UPDATEWATCH_E_UNKNOWNAPP");
gpfr_error::doRegisterResult("UPDATEWATCH_E_UNKNOWNVERSION");
gpfr_error::doRegisterResult("UPDATEWATCH_E_NOUPDATEAVAILABLE");

?>
