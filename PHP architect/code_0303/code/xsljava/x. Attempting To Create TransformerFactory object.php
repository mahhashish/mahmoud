<?php

// Listing x.
//		Attempt to create a TransformerFactory object from PHP
//
//		Written for php|architect
//
// NOTES
//		This code will not run ;-)

$l_oIO	     = new Java("javax.xml.transform.stream.StreamSource", "fred");
$l_oFactory = new Java("javax.xml.transform.TransformerFactory");
$l_oInstance = $l_oFactory->newTransformer($l_oIO);

// With PHP 4.3.0, I get the following warning:
//
//	Warning: java.lang.IllegalArgumentException: object is not an 
//	instance of declaring class in c:\Documents and Settings\Stuart
//	\My Documents\Backed Up\phpArchitect\My Articles
//	\200301 - XSL and Java\Listings\
// 	x. Attempting To Create TransformerFactory object.php on line 13

?>