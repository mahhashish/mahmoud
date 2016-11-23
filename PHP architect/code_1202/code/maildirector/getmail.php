<?php /* $Id: getmail.php,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once('include/mailDirector.inc');
include_once('include/pop3.inc');
include_once('include/style.inc');

$oMD = new mailDirector();
$oMD->loadConfig();

/* We're going to set a long time limit for downloading BIG messages */
set_time_limit(0);

$oStyle = new style();
$oStyle->display('open.tpl');
echo '<pre>';
foreach ( $oMD->config AS $accnt => $a )
{
	if ( $a['check'] == 0 )
	{
		continue;
	}

	$pop3 		= new pop3();
	$oMaildir 	= $oMD->fetchMaildir($a['inbox']);

	if ( strpos($a['pop3'], ':') )
	{
		list($host, $port) = explode(':', $a['pop3']);
	}
	else
	{
		$host = $a['pop3'];
		$port = 110;
	}
	
	echo "<img src=\"theme/img/mailbox.gif\"> Checking mail for {$accnt} ({$a['inbox']})\n";
	
	if ( $pop3->connect($host, $port, $a['user'], $a['pass']) )
	{
		echo "\tConnected\n";
		
		$stat = $pop3->_STAT();
		
		if ( $stat['number'] > 0 )
		{
			echo "\t{$stat['number']} new messages ({$stat['size']})\n";
			
			$messages = $pop3->_LIST();
			$i = 0;
			foreach ( $messages AS $message )
			{
				echo "\t" . ++$i . ". Downloading " . $message['size'] . "...";
				flush();
				$oMessage = $oMaildir->newMessage();
				$oMessage->createTmp();
				$data = 'X-Account: ' . $accnt . "\n";
				$data .= $pop3->_RETR($message['id']);
				if ( $oMessage->store($data) )
				{
					if ( $oMessage->moveNew() )
					{
						echo ' ' . $oMessage->file;
						if ( $a['delete'] == 1 )
						{
							$pop3->_DELE($message['id']);
							echo '... deleted';
						}
					}
				}
				echo "\n";
				flush();
			}
		}
		else
		{
			echo "\tNo new mail\n";
		}
		
		echo "\tDisconnecting\n\n";
		$pop3->disconnect();
	}
	else
	{
		echo "\tFailed to connect...\n";
	}
	flush();
}

echo '<meta http-equiv="Refresh" content="360; URL=' . $_SERVER['PHP_SELF'] . '">';

echo '</pre>';
$oStyle->display('close.tpl');
?>
