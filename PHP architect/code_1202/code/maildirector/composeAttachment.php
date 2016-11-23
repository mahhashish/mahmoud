<?php

include_once('include/mailDirector.inc');
include_once('include/style.inc');

$oMD 		= new mailDirector();
$oMaildir	= $oMD->fetchMaildir(urldecode($_REQUEST['F']));
$oMessage	= $oMaildir->fetchMessage($_REQUEST['M']);


switch ( $_REQUEST['A'] )
{
	case 'add':
		$data = file_get_contents($_FILES['attach']['tmp_name']);
		$attach = array(	'file'			=> $_FILES['attach']['name'],
							'content-type'	=> 'base64',
							'data'			=> base64_encode($data)
							);
		
		$oMessage->newAttachment($attach);
		break;
		
	case 'rm':
		$attachments = $oMessage->getAttachments();
		if ( is_array($attachments) )
		{
			$attach = array();
			foreach ( $attachments AS $id => $a )
			{
				if ( $id == $_REQUEST['rm'] )
				{
					continue;
				}

				$attach[] = $a;
			}

			$oMessage->newAttachment($attach, true);
		}
		break;
}

$oStyle = new style();
$oStyle->assign('F', $_REQUEST['F']);
$oStyle->assign('M', $_REQUEST['M']);

if ( $_REQUEST['A'] != 'form' )
{
	$oStyle->assign('attachments', $oMessage->getAttachments() );
}

$oStyle->display('composeAttachment.tpl');
?>
