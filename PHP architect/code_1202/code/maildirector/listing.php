<?php /* $Id: listing.php,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once('include/mailDirector.inc');
include_once('include/style.inc');

$folder		= urldecode( $_REQUEST['F'] );
$offset		= ( $_REQUEST['offset'] ? $_REQUEST['offset'] : 0 );
$size		= 100;

$oMD 		= new mailDirector();
$oMaildir	= $oMD->fetchMaildir($folder);

/* Do we need to expunge? */
if ( $_REQUEST['A'] == 'expunge' )
{
	$expunge = $oMaildir->expunge();
}

/* Limit number of emails to show */
$listing	= $oMaildir->getMessages();
$listing	= array_reverse($listing);
$listing	= array_slice($listing, $offset, $size);

$messages = array();
foreach ( $listing AS $mid )
{	
	$oMessage = $oMaildir->fetchMessage( $mid );
	$messages[$mid] = $oMessage->fetchIndex();
}

/* Display the listing */
$oStyle = new style();

$total = $oMaildir->getNumMessages();

$oStyle->assign('expunge',	$expunge);
$oStyle->assign('total',	$total);
$oStyle->assign('size',		$size);
$oStyle->assign('next',		$offset+$size);
$oStyle->assign('prev', 	$offset-$size);
$oStyle->assign('end', 		($total - $size) );

$oStyle->assign('folder',	$oMaildir);
$oStyle->assign('messages', $messages);
$oStyle->display('listing.tpl');
?>
