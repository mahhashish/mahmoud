<?php

// docXP.xslt.php
//		Class to support processing XML files through the
//		built-in PHP XSLT interface
//
//		Part of the docXP project
//
// Author	Stuart Herbert
//		(stuart@gnqs.org)
//
// Copyright	(c) 2002 Stuart Herbert
//		Released under v2 of the GNU GPL
//
// ------------------------------------------------------------------------

/**
 * @package	docXP::xslt
 */

class docXP_sablotron
{
	/**
	 *
	 * If the XSLT transform fails, the code will die()
	 *
	 * @param	string		$a_szOutputFile		in
	 *		Name of the file to write to
	 * @param	string		$a_szXsltFile		in
	 *		Name of the XSLT file to perform the transform with
	 * @param	array		$a_szXML		in
	 *		The XML text to be transformed
	 * @return	nothing
	 *
	 * @author	Stuart Herbert <stuart@gnqs.org>
	 * @project	docXP
	 * @since	docXP 1.0
	 * @lastchanged	docXP 1.0
	 * @package	docXP
	 */

	function doProcessXslt ($a_szOutputFile, $a_szXslt, $a_szXML)
	{
		echo "Using Sablotron\n";
		$l_aArgs = array("/_xml" => $a_szXML, "/_xsl" => $a_szXslt);

		$h = xslt_create();
		$l_szResult = xslt_process($h, "arg:/_xml", "arg:/_xsl", NULL, $l_aArgs);
		if ($l_szResult)
		{
			docXP_fileutils::doOutputString($a_szOutputFile, $l_szResult);
			xslt_free($h);
			return true;
		}

		die ("XSLT failed - error is " . xslt_errno($h) . "::" . xslt_error($h));
	}
}

?>