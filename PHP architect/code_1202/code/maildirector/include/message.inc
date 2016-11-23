<?php /* $Id: message.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once( dirname(__FILE__) . '/mime.inc');

class message
{
	var $file		= null;
	var $size		= 0;
	
	var $rawMessage	= null;
	var $rawHeaders	= null;

	var $headers	= null;
	var $body		= null;
	var $bodyHTML	= null;
	var $parts		= null;

	function message($file = null, $string = null)
	{
		if ( $file !== null && $file !== '' )
		{
			$this->loadFile($file);
		}
		elseif ( $string !== null && $string !== '' )
		{
			$this->loadString($string);
		}
	}

	function setBody($string)
	{
		$this->body = $string;
	}

	/**
	 * Parses the entire message, otherwise only parses on demand
	 */
	function parse()
	{
		if ( $this->rawMessage !== null )
		{
			if ( $this->rawHeaders === null )
			{
				$this->_parseHeaders();
			}
	
			if ( $this->body === null )
			{
				$this->_parseBody();
			}

			return true;
		}

		return false;
	}

	/**
	 * Takes the raw text of email and breaks it into a headers and message portions
	 */
	function _splitMessage()
	{
		$message			= strstr($this->rawMessage, "\n\n");
		$this->rawHeaders	= substr($this->rawMessage, 0, (-1)*strlen($message) );
		$this->rawMessage 	= $message;
	}
	
	/**
	 * Takes the headers and breaks them up into an array
	 */
	function _parseHeaders()
	{
		if ( $this->rawHeaders === null )
		{
			$this->_splitMessage();
		}
		
		/* Places all headers onto one line segments, RFC 822. (CRLF + LWSP combo)
		   Section 3.1.1: Long Header Fields */
		$unwrapped = preg_replace('/\r?\n[\s]+/i', ' ', $this->rawHeaders);
		
		/* Break our headers into lines */
		$lines = explode("\n", $unwrapped);
		
		foreach($lines AS $line)
		{
			$line = trim($line);
			if( $line != '')
			{
				list($f, $v) = $this->_splitHeader($line);

				switch($f)
				{
					case 'reply-to':
					case 'from':
					case 'to':
					case 'cc':
					case 'bcc':
						$v = $this->_processAddresses($v, $f);
						break;

					case 'content-type':
						if ( preg_match('/multipart/i', $v) )
						{
							$this->headers['x-attachment'] = 1;
						}
						break;
				}

				if ( $f == 'from' || $f == 'reply-to' )
				{
					$v = array_pop($v);
				}

				if ( isset($this->headers[$f]) )
				{
					$this->headers[$f] .= "\n" . $v;
				}
				else
				{
					$this->headers[$f] = $v;
				}
			}
		}
		return true;
	}
	
	function _splitHeader($string)
	{
		$j = strpos($string, ':');
		return array(	trim( strtolower( substr($string, 0, $j) ) ),
						trim( substr($string, $j+1) )
					);
	}

	function _processAddresses($string)
	{
		/*
		array(	array('email','name|email')
				);
		*/
		
		$pat = '/("?([a-z0-9\s\']+)"?\s+)?[<]?([a-z0-9\.-]+@[a-z0-9\.-]+)+[>]?,?\s?/i';
		preg_match_all($pat, $string, $tmp);

		$array = array();
		foreach ( $tmp[3] AS $c => $v )
		{
			$array[] = array($v, ( trim($tmp[2][$c]) != '' ? $tmp[2][$c] : $v ) );
		}

		return $array;
	}

	function _parseBody()
	{
		if ( $this->headers === null )
		{
			$this->_parseHeaders();
		}
		
		$this->parts 	= null;
		$this->body		= null;
		
		/* Determine if we're a multi part message */
		if( preg_match('/multipart\/mixed;\s*boundary="(.*?)"/i', $this->headers['content-type'], $tmp) )
		{
			$boundryString = $tmp[1];

			/* Put all Content-* headers onto one line */
			$this->rawMessage = preg_replace_callback("/^Content-.*:\s*.*(\r\n\s+\w)(\r\n)?/im", array(&$this, '_wrapHeadersCallback'), $this->rawMessage);
			
			/* Split our message up by boundry, */
			$boundries = explode("--{$boundryString}\n", $this->rawMessage);

			/* We don't want anything before the first boundry... */
			array_shift($boundries);
			
			/* Turn our this->parts into an array so we know we have attachments */
			$this->parts = array();
			
			foreach($boundries AS $contents)
			{
				$a			= array();

				$part		= strstr($contents, "\n\n");
				$headers	= substr($contents, 0, (-1)*strlen($part) );
				list($contents) = explode('--' . $boundryString . '--', $part);

				foreach ( explode("\n", $headers) AS $line)
				{
					if ( $f == 'content-type' || $f == 'content-disposition' )
					{
						preg_match('/(file)?name=["]?([^"]*)["]?/i', $v, $match);
						$a['name'] = $match[2];
					}
					list($f, $v) = $this->_splitHeader($line);
					$a[$f] = $v;
				}

				$a['contents'] = mime::decode($contents, $a['content-transfer-encoding']);

				/* We don't have a body yet, so we populate this with the first content-type of text */
				if ( ($this->body === null) && preg_match('|^text/([a-z]+)|i', $a['content-type'], $t) )
				{
					$this->setBody( $a['contents'] );
				}
				else
				{
					$this->parts[] = $a;
				}
			}
		}
		elseif ( preg_match('|^text/html|i', $this->headers['content-type']) )
		{
			$this->bodyHTML = $this->rawMessage;
		}
		else
		{
			$this->setBody($this->rawMessage);
		}
	}
	
	function _wrapHeadersCallback($array)
	{
		return preg_replace("/\r\n[\s]+/i", ' ', $array[0]);
	}

	/**
	 * Loads a file
	 */
	function loadFile($file)
	{
		$this->file = $file;

		if ( file_exists($file) )
		{
			$this->loadString( file_get_contents($file) );
			return true;
		}

		return false;
	}

	/**
	 * Loads a message from a raw string, changes all \r\n to \n
	 */
	function loadString($string)
	{
		/* Replaces all \r\n with just \n and all \r with \n */
		$string = str_replace("\r\n", "\n", $string);
		$string = str_replace("\r", "\n", $string);

		$this->size			= strlen($string);
		$this->rawMessage 	= $string;
		return true;
	}

	/**
	 * Returns a specific header (such as subject)
	 */
	function getHeader($header)
	{
		if ( $this->headers === null )
		{
			$this->_parseHeaders();
		}

		if ( isset($this->headers[$header]) )
		{
			return $this->headers[$header];
		}

		return false;
	}

	/**
	 * Returns an array of all headers
	 */
	function getHeaders()
	{
		if ( $this->headers === null )
		{
			$this->_parseHeaders();
		}
		
		return $this->headers;
	}
	
	/**
	 * Returns the body of the message
	 */
	function getBody()
	{
		if ( $this->body === null )
		{
			$this->_parseBody();
		}
		
		return $this->body;
	}
	
	/**
	 * Returns an array of all decoded message parts
	 */
	function getParts()
	{
		if ( $this->parts === null )
		{
			$this->_parseBody();
		}

		return $this->parts;
	}
	
	/**
	 * Returns a specific message part
	 */
	function getPart($part)
	{
		if ( $this->parts === null )
		{
			$this->_parseBody();
		}

		if ( isset($this->parts[$part]) )
		{
			return $this->parts[$part];
		}
		
		return false;
	}

	function getByContentId($contentId)
	{
		foreach ( $this->parts AS $p )
		{
			if ( $p['content-id'] == $contentId )
			{
				return $p['contents'];
			}
		}
	}
}
?>
