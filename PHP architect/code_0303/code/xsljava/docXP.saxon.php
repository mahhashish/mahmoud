<?php

// docXP.saxon.php
//		Class to support processing XML files through the
//		external Saxon XSLT processor
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

class docXP_saxon
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
		// we need to create a temporary file, then run the
		// external Saxon software

		$fp = fopen("docXP.tmp.xml", "w+");
		fwrite($fp, $a_szXML, strlen($a_szXML));
		fclose($fp);

		$fp = fopen("docXP.tmp.xsl", "w+");
		fwrite($fp, $a_szXslt, strlen($a_szXslt));
		fclose($fp);

		$l_szOutput = preg_replace("|&|", "\&", $l_szOutput);

		$l_szOutput = shell_exec("c:/php/saxon/saxon.exe -o " . $a_szOutputFile . " docXP.tmp.xml docXP.tmp.xsl");

		unlink("docXP.tmp.xml");
		unlink("docXP.tmp.xsl");

		return true;
	}
}

?>