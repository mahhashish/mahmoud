<?php /* $Id: readAttachment.php,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once('include/mailDirector.inc');
include_once('include/style.inc');

$oMD 		= new mailDirector();
$oMaildir	= $oMD->fetchMaildir( urldecode($_REQUEST['F']) );
$oMessage	= $oMaildir->fetchMessage($_REQUEST['M']);

$oM = $oMessage->fetchMessage();
$oM->parse();

$att = $oM->getPart($_REQUEST['id']);
if ( $att['content-type'] )
{
	header('Content-type: ' . $att['content-type']);
}
if ( $att['content-disposition'] )
{
	header('Content-disposition: ' . $att['content-disposition']);
}

echo $att['contents'];
?>
