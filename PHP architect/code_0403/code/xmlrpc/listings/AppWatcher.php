<?php

// AppWatcher.php
//		Class to implement an update availability check
//
// Author	Stuart Herbert
//		(stuart@gnqs.org)
//
// ------------------------------------------------------------------------

class AppWatcher
{
	var $szStable	= "stable";
	var $szUnstable	= "unstable";

	// ----------------------------------------------------------------
	// C O N S T R U C T O R S
	// ----------------------------------------------------------------

	function AppWatcher ()
	{
		$this->constructor();
	}

	function constructor ()
	{
		// do nothing - for future-proofing
	}

	// ----------------------------------------------------------------
	// P U B L I C   I N T E R F A C E
	// ----------------------------------------------------------------

	/**
	 * register details about one version of one application
	 *
	 * Flags are binary, and should be combined using the or
	 * operator.
	 *
	 * Values for flags include:
	 *
	 * * APPWATCHER_F_NONE - when none of the other flags will do
	 * * APPWATCHER_F_USELATESTSTABLE - users should upgrade from
	 *   this version to the latest stable version
	 * * APPWATCHER_F_USELATESTUNSTABLE - users should upgrade from
	 *   this version to the latest unstable version
	 * * APPWATCHER_F_USENAMEDVERSION - users should upgrade from
	 *   this version to the specified version
	 * * APPWATCHER_F_ISINSECURE - this version has known security
	 *   holes
	 * * APPWATCHER_F_ISUNSTABLE - this version is a release not
	 *   recommended for use on production machines
	 *
	 * Overload this method if you want to change where AppWatcher
	 * stores/retrieves information from
	 *
	 * @param	string		$a_szAppName
	 *		The name of the application
	 * @param	string		$a_szVersion
	 *		The version of the application
	 * @param	int		$a_iFlags
	 *		Flags for this version
	 * @param	array		$a_aReleaseNotes
	 *		An array of URLs for where the release notes are
	 * @param	array		$a_aDownloads
	 *		An array of download URLs for this version
	 */

	function doAddAppDetails (
		$a_szAppName, 
		$a_szVersion, 
		$a_iFlags = APPWATCHER_F_NONE, 
		$a_aReleaseNotes = null, 
		$a_aDownloads = null,
		$a_szUpgradeVersion = "n/a"
	)
	{
		$l_aRec["Version"]		= $a_szVersion;
		$l_aRec["Flags"]		= $a_iFlags;
		$l_aRec["DownloadURLs"]		= $a_aDownloads;
		$l_aRec["ReleaseNoteURLs"]	= $a_aReleaseNotes;
		$l_aRec["UpgradeTo"]		= $a_szUpgradeVersion;

		$this->aAppDetails[$a_szAppName][$a_szVersion] = $l_aRec;
	}

	/**
	 * find out whether a specified application version is up to date
	 *
	 * @param	string		$a_szAppName
	 *		The name of the application
	 * @param	string		$a_szVersion
	 *		The version number of the application
	 * @return	true if the application is up to date
	 * @return	true if we do not know anything about the application
	 *		or this version of the application
	 * @return	false if the application needs upgrading
	 */

	function getIsUpToDate($a_szAppName, $a_szVersion)
	{
		$l_szUpgrade = $this->getRecommendedUpgrade
		(
			$a_szAppName, $a_szVersion
		);
		if ($l_szUpgrade == $a_szVersion)
			return true;

		return false;
	}

	/**
	 * find out which version to upgrade a specific application version to
	 *
	 * @param	string		$a_szAppName
	 *		The name of the application
	 * @param	string		$a_szVersion
	 *		The version number of the application
	 * @return	string
	 *		The version number to upgrade to.  If $a_szVersion
	 *		is the latest version, then $a_szVersion is
	 *		returned to the caller.
	 */

	function getRecommendedUpgrade($a_szAppName, $a_szVersion)
	{
		// do we know about this app?

		$l_aDetails = $this->_getAppDetailsForVersion
		(
			$a_szAppName,
			$a_szVersion
		);

		if ($l_aDetails === null)
		{
			return $a_szVersion;
		}
		
		// yes we do
		// now, what do we know?

		if (!($l_aDetails["Flags"] & APPWATCHER_F_SUPERCEEDED))
		{
			// this version has no upgrade path yet

			return $a_szVersion;
		}

		// this version no longer latest
		
		if ($l_aDetails["Flags"] & APPWATCHER_F_USENAMEDVERSION)
		{
			// specific version listed as upgrade
			
			return $l_aDetails["UpgradeTo"];
		}
		else if ($l_aDetails["Flags"] & APPWATCHER_F_USELATESTSTABLE)
		{
			return $this->_getLatestStable($a_szAppName);
		}
		else if ($l_aDetails["Flags"] & APPWATCHER_F_USELATESTUNSTABLE)
		{
			return $this->_getLatestUnstable($a_szAppName);
		}

		// hrm ... something is wrong
		// this should be unreachable
		
		return $a_szVersion;
	}

	/**
	 * get a list of the URLs for the release notes for a
	 * named application version
	 *
	 * @param	string		$a_szAppName
	 *		The name of the application
	 * @param	string		$a_szVersion
	 *		The version of the application
	 * @return	array
	 */

	function getReleaseNotesURLs ($a_szAppName, $a_szVersion)
	{
		// what do we know about this app?

		$l_aDetails = $this->_getAppDetailsForVersion
		(
			$a_szAppName, $a_szVersion
		);
		if ($l_aDetails === null)
			return array();

		// return the release note URLs

		return $l_aDetails["ReleaseNoteURLs"];
	}

	/**
	 * get a list of the URLs for the downloads for a
	 * named application version
	 *
	 * @param	string		$a_szAppName
	 *		The name of the application
	 * @param	string		$a_szVersion
	 *		The version of the application
	 * @return	array
	 */

	function getDownloadURLs ($a_szAppName, $a_szVersion)
	{
		// what do we know about this app?

		$l_aDetails = $this->_getAppDetailsForVersion
		(
			$a_szAppName, $a_szVersion
		);
		if ($l_aDetails === null)
			return array();

		// return the download URLs

		return $l_aDetails["DownloadURLs"];
	}

	/**
	 * check to see whether an application has been identified as having
	 * security problems or not
	 *
	 * @param	string		$a_szAppName
	 *		The name of the application
	 * @param	string		$a_szVersion
	 *		The version of the application
	 * @return	true if there are security problems with this
	 *		version of the application
	 * @return	false if all is well
	 */

	function getIsInsecure ($a_szAppName, $a_szVersion)
	{
		// do we know about this app?

		$l_aDetails = $this->_getAppDetailsForVersion
		(
			$a_szAppName, $a_szVersion
		);
		
		// if we don't know about this app, it's not for
		// us to tell the world about security

		if ($l_aDetails === null)
			return false;

		// what do the tealeaves say?

		if ($l_aDetails["Flags"] & APPWATCHER_F_ISINSECURE)
			return true;

		// the tealaves say nay

		return false;
	}

	/**
	 * tell AppWatcher which is the stable version of a named app
	 *
	 * @param	string		$a_szAppName
	 *		The name of the application
	 * @param	string		$a_szVersion
	 *		The version of the application
	 *
	 * @return	true
	 */

	function setStableVersion ($a_szAppName, $a_szVersion)
	{
		return $this->_setVersion
		(
			$this->szStable,
			$a_szAppName, 
			$a_szVersion
		);
	}

	/**
	 * tell AppWatcher which is the unstable version of a named app
	 *
	 * @param	string		$a_szName
	 *		The name of the application
	 * @param	string		$a_szVersion
	 *		The version of the application
	 * @return	true
	 */

	function setUnstableVersion ($a_szAppName, $a_szVersion)
	{
		return $this->_setVersion
		(
			$this->szUnstable,
			$a_szAppName,
			$a_szVersion
		);
	}

	function getIsRegisteredApp ($a_szAppName)
	{
		if (!@isset($this->aAppDetails[$a_szAppName]))
			return false;

		return true;
	}

	function getIsRegisteredVersion ($a_szAppName, $a_szVersion)
	{
		if (!@isset($this->aAppDetails[$a_szAppName][$a_szVersion]))
			return false;

		return true;
	}

	// ----------------------------------------------------------------
	// P R I V A T E   F U N C T I O N S
	// ----------------------------------------------------------------

	function _setVersion ($a_szType, $a_szAppName, $a_szVersion)
	{
		if (!isset($this->aAppDetails[$a_szAppName]))
			return false;

		if (!isset($this->aAppDetails[$a_szAppName][$a_szVersion]))
			return false;

		$this->aAppDetails[$a_szAppName][$a_szType] =& $this->aAppDetails[$a_szAppName][$a_szVersion];
		return true;
	}

	function _getAppDetailsForVersion ($a_szAppName, $a_szVersion)
	{
		if ((isset($this->aAppDetails[$a_szAppName])) and
		    (isset($this->aAppDetails[$a_szAppName][$a_szVersion])))
			return $this->aAppDetails[$a_szAppName][$a_szVersion];

		return null;
	}

	function _getLatestStable ($a_szAppName)
	{
		if (isset($this->aAppDetails[$a_szAppName]) and
		    isset($this->aAppDetails[$a_szAppName][$this->szStable]))
			return $this->aAppDetails[$a_szAppName][$this->szStable]["Version"];

		return null;
	}

	function _getLatestUnstable ($a_szAppName)
	{
		if (isset($this->aAppDetails[$a_szAppName]) and
		    isset($this->aAppDetails[$a_szAppName][$this->szUnstable]))
			return $this->aAppDetails[$a_szAppName][$this->szUnstable]["Version"];

		return null;
	}
}

// ------------------------------------------------------------------------

// Flags used by the AppWatcher

define("APPWATCHER_F_NONE",			0x00);
define("APPWATCHER_F_USELATESTSTABLE",		0x01);
define("APPWATCHER_F_USENAMEDVERSION", 		0x02);
define("APPWATCHER_F_USELATESTUNSTABLE", 	0x04);
define("APPWATCHER_F_ISINSECURE",		0x08);
define("APPWATCHER_F_ISUNSTABLE",		0x10);

// always update APPWATCHER_F_LAST to equal the last flag set in the
// sequence

define("APPWATCHER_F_LAST",			0x10);
define("APPWATCHER_F_ALL", (APPWATCHER_F_LAST * 2) -1);

// some common combinations

define
(
	"APPWATCHER_F_SUPERCEEDED",	
	APPWATCHER_F_USELATESTSTABLE 
	| APPWATCHER_F_USENAMEDVERSION 
	| APPWATCHER_F_USELATESTUNSTABLE
);

?>
