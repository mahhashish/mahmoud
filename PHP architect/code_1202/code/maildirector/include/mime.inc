<?php /* $Id: mime.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

/**
 * Intended to be called statically, mime::
 */
class mime
{
	function encode($contents, $encoding)
	{
		$encoding = trim($encoding);
		switch( strtolower($encoding) )
		{
			case 'base64':
				return mime::base64Encode($contents);
				
			case 'quoted-printable':
				return mime::quotedPrintableEncode($contents);
				
			default: return $contents;
		}
	}
	
	function decode($contents, $encoding)
	{
		$encoding = trim($encoding);
		switch( strtolower($encoding) )
		{
			case 'base64':
				return mime::base64Decode($contents);
				
			case 'quoted-printable':
				return mime::quotedPrintableDecode($contents);

			default: return $contents;
		}
	}
	
	function base64Encode($data)
	{
		return base64_encode($data);
	}
	
	function base64Decode($data)
	{
		$data = str_replace("\r", '', $data);
		$data = str_replace("\n", '', $data);
		$data = trim($data);
		return base64_decode($data);
	}
	
	function quotedPrintableEncode($data)
	{
		// Requires IMAP
		return imap_qprint($data);
	}
	
	function quotedPrintableDecode($data)
	{
		return quoted_printable_decode($data);
	}
	
	function utf7Encode($data)
	{
		return imap_uft7_encode($data);
	}
	
	function utf7Decode($data)
	{
		return imap_uft7_decode($data);
	}
}
?>
