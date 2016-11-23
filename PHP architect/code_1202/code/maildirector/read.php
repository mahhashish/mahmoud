<?php /* $Id: read.php,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once('include/mailDirector.inc');
include_once('include/style.inc');

$F = $_REQUEST['F'];
$M = $_REQUEST['M'];

$oMD 		= new mailDirector();
$oMaildir	= $oMD->fetchMaildir( urldecode($_REQUEST['F']) );
$oMessage	= $oMaildir->fetchMessage( $_REQUEST['M'] );

switch ( $_REQUEST['A'] )
{
	case 'delete':
		if ( $oMessage->T )
		{
			$oMessage->setTrash(false);
		}
		else
		{
			$oMessage->setTrash(true);
		}
		break;
		
	case 'flag':
		if ( $oMessage->F )
		{
			$oMessage->setFlag(false);
		}
		else
		{
			$oMessage->setFlag(true);
		}
		break;
	case 'move':
		break;
}

/* Returns our message object that actually get's us the message text */
$oM = $oMessage->fetchMessage();
$oM->parse();

$body = trim( $oM->bodyHTML ? $oM->bodyHTML : $oM->body );

function highlightReplies($matched)
{
	$colors = array('#606060', 'navy');
	$cc		= count($colors);
	
	$indent = explode('>', $matched[2]);
	$c = count($indent);
	$ret = '';
	
	for ( $i = 1; $i < $c; $i++ )
	{
		$ret .= '<span style="color: ' . $colors[($i % $cc)] . '">>' . $indent[$i];
	}
	
	return $ret . $matched[4] . str_repeat('</span>', $c);
}

/* Disable all html for now */
$body = htmlentities($body);
$body = nl2br($body);

$body = str_replace('&gt;', '>', $body);
$body = preg_replace_callback('/^(((>\s{0,1})+)(.*))$/im', 'highlightReplies', $body);

$body = preg_replace(	'/(-+Original Message-+)/im', 
						'<b>\\1</b>',
						$body);
$body = preg_replace(	'/(On ([a-z]{3,3}), .*)$/im', 
						'<b>\\1</b>', 
						$body);

/* Highlight links */
$body = preg_replace(	'/((http|ftp):\/\/[^<>\s]*)/im', 
						'<a href="\\0" target="_blank" style="color: #656565; text-decoration: none;">\\0</a>', 
						$body);

/* Highlight email addresses with compose link */
$body = preg_replace(	'/(mailto:)?(([^\s]+)@([a-z0-9\.-])+\.([a-z^\s]+))/im', 
						'<a href="javascript:newComposer(' . "'{$F}','','','" . '\\2\');" style="color: #656565; text-decoration: none;">\\2<a/>', 
						$body);


$oStyle = new style();
$oStyle->assign('md',		$oMessage);
$oStyle->assign('message', 	$oM);
$oStyle->assign('body',		$body);
$oStyle->assign('M',		$_REQUEST['M'] );
$oStyle->assign('F',		$_REQUEST['F'] );
$oStyle->display('read.tpl');

/*
echo '<pre>';
print_r($oMessage);
print_r($oM);
echo '</pre>';
*/
?>
