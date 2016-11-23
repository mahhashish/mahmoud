<?php /* $Id: mdMaildirMessage.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once( dirname(__FILE__) . '/maildirMessage.inc');
include_once( dirname(__FILE__) . '/message.inc');
include_once( dirname(__FILE__) . '/php.inc');

class mdMaildirMessage extends maildirMessage
{
	function mdMaildirMessage($folder = null, $file = null)
	{
		$this->maildirMessage($folder, $file);
	}
	
	function fetchMessage()
	{
		return new message( prepFile($this->folder, $this->getFilename() ) );
	}
	
	/**
	 * Returns an array of properties used for generating a message listing
	 */
	function fetchIndex()
	{
		$oMessage = new message(prepFile($this->folder, $this->getFilename() ) );

		$headers = $oMessage->getHeaders();

		$index = array('size' => $oMessage->size);
		if ( is_array($headers) )
		{
			foreach ( $headers AS $header => $value )
			{
				if ( in_array($header, array('from','date','to','subject','x-attachment') ) )
				{
					$index[$header] = $value;
				}
			}
		}
		
		foreach ( array('messageId','file','folder','state','D','F','R','S','T') AS $k )
		{
			$index[$k] = $this->{$k};
		}
		
		return $index;
	}
	
	/* --- Attachment functions --- */
	/* Stores an attachment for a composed message */
	function newAttachment($attachment, $overwrite = false)
	{
		$file = $this->_prepFile($this->folder, 'tmp', $this->messageId . ':2,A');

		if ( file_exists($file) )
		{
			$attach = $this->getMessageAttachments();
		}
		else
		{
			$attach = array();
		}
		
		if ( $overwrite ) 
		{
			$attach = $attachment;
		}
		else
		{
			$attach[] = $attachment;
		}

		if ( file_put_contents($file, serialize($attach) ) )
		{
			return count($attach);
		}

		return false;
	}

	function clearAttachment()
	{
		$file = $this->_prepFile($this->folder, 'tmp', $this->messageId . ':2,A');

		if ( file_exists($file) )
		{
			if ( !@unlink($file) )
			{
				return false;
			}
		}

		return true;
	}
	
	function getAttachments()
	{
		$file = $this->_prepFile($this->folder, 'tmp', $this->messageId . ':2,A');
		
		if ( file_exists($file) )
		{
			return unserialize( file_get_contents($file) );
		}
		
		return false;
	}
}
?>
