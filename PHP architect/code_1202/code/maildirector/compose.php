<?php /* $Id: compose.php,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once('include/mailDirector.inc');
include_once('include/style.inc');

$F = $_REQUEST['F'];
$R = $_REQUEST['R'];
$A = $_REQUEST['A'];
$T = $_REQUEST['T'];

$oStyle		= new style();

$oMD		= new mailDirector();
$oMaildir	= $oMD->fetchMaildir($_REQUEST['F']);

/* We're sending mail!? */
if ( $A == 'send' )
{
	$oMessage = $oMaildir->fetchMessage($_POST['M']);
	
	include_once('include/mailer.inc');
	include_once('include/message.inc');

	$oMailer = new mailer();

	/* Start building our message */
	$oMailer->Body 	= $_POST['body'];
	$oMailer->Subject	= $_POST['subject'];

	/* Process Addresses TO send to */
	foreach ( message::_processAddresses($_POST['to']) AS $addr )
	{
		$oMailer->AddAddress($addr[0], $addr[1]);
	}
	
	/* Process Addresses CC send to */
	foreach ( message::_processAddresses($_POST['cc']) AS $addr )
	{
		$oMailer->AddCC($addr[0], $addr[1]);
	}

	/* Process Addresses BCC send to */
	foreach ( message::_processAddresses($_POST['bcc']) AS $addr )
	{
		$oMailer->AddBCC($addr[0], $addr[1]);
	}
	
	/* Add any attachments */
	$attachments = $oMessage->getAttachments();
	if ( is_array($attachments) )
	{
		foreach ( $attachments AS $attach )
		{
			$oMailer->AddStringAttachment(base64_decode($attach['data']), $attach['file']);
		}
	}

	/* Setup who this message is from */
	list($account, $email) = explode('#', $_POST['from']);

	$oMailer->From		= $email;
	$oMailer->FromName 	= $oMD->getConfig($account, 'name');

	/* Set host to send mail */
	$oMailer->Host		= $oMD->getConfig($account, 'smtp');

	if ( $oMailer->Send() )
	{
		if ( is_array($attachments) )
		{
			//$oMessage->clearAttachments();
		}

		/* Now copy the message to the sent folder */
		$oMessage->store( $oSendmail->Message );

		$sent = $oMD->getConfig($account, 'sent');
		$oMessage->moveFolder( $sent );
		echo "Message {$_POST['M']} has been sent and a copy is in {$sent}\n";
		exit;
	}
	else
	{
		echo "Failed to send message!";
		echo '<pre>';
		print_r($oMailer);
	}
}

/* Setup our from OPTION */
$from = array();
foreach ( $oMD->config AS $accnt => $a )
{
	$from[$accnt][] = array(	'name'	=> $a['name'],
								'email'	=> $a['address']
								);
	if ( $a['alias'] )
	{
		if ( !is_array($a['alias']) )
		{
			$a['alias'] = array($a['alias']);
		}

		foreach ( $a['alias'] AS $alias )
		{
			$from[$accnt][] = array('name' => $a['name'], 'email' => $alias);
		}
	}
}

$oStyle->assign('from', $from);

if ( !$oMessage )
{
	$oMessage 	= $oMaildir->newMessage();
	$mid 		= $oMessage->createTmp();
	$oMessage->store('Subject: Temporary message');
}
else
{
	$mid = $_POST['M'];
}

if ( $T )
{
	$oStyle->assign('to', $T);
}

switch ( $A )
{
	case 'reply':
		$oMessageTmp = $oMaildir->fetchMessage( $R );
		$oM = $oMessageTmp->fetchMessage();
		$oM->parse();

		/* Setup our subject */
		$subject = $oM->getHeader('subject');
		if ( !preg_match('|^re:|i', $subject) )
		{
			$subject = 'Re: ' . $subject;
		}
		$oStyle->assign('subject',	$subject);

		/* Setup our To: */
		
		$to = $oM->getHeader('reply-to');
		if ( !$to )
		{
			$to = $oM->getHeader('from');
		}

		if ( $to[1] !== $to[0] )
		{
			$oStyle->assign('to', sprintf('%s <%s>', $to[1], $to[0]) );
		}
		else
		{
			$oStyle->assign('to', $to[0]);
		}

		/* Quote the message we're going to reply with */
		$body = "\n\n\n";
		$body .= sprintf("On %s %s wrote:\n", $oM->getHeader('date'), $to[1]);
		$body .= preg_replace('|^|m', '> ', $oM->body);
		
		$oStyle->assign('body', $body);
		break;

	case 'forward':
		/* Attach the old message */
		
		$oMessageTmp = $oMaildir->fetchMessage($R);
		$oM = $oMessageTmp->fetchMessage();
		
		$attach = array( 	'file' 			=> $R,
							'content-type' 	=> 'mime/message',
							'data'			=> $oM->rawMessage
						);

		$oMessage->newAttachment($attach);
		break;
}

$oStyle->assign('F', $F);
$oStyle->assign('M', $mid);
$oStyle->display('compose.tpl');
?>
