<?php
// Written by Jonathan Sharp, August 15th, 2001

class pop3
{
	var $sock;
	var $errno;
	var $errstr;
	
	var $responseno;
	var $responsestr;
	
	var $hostname;
	var $port;
	var $greeting;
	var $username;
	var $password;
	
	var $log;
	var $sessionlog;

	var $completelog;
	var $completesessionlog;

	var $buffer;

	function connect($hostname, $port = 110, $username = false, $password = false, $apop = false)
	{
		$this->hostname = $hostname;
		$this->port		= $port;
		$this->username	= $username;
		$this->password	= $password;
		
		$this->sock = fsockopen($hostname, $port, $this->errno, $this->errstr, 30);
		if( !$this->sock )
		{
			$this->_log("Failed to connect to: $hostname:$port\n");
			$this->_log("Socket Error: {$this->errno}: {$this->errstr}\n");
			return false;
		}
		else
		{
			$this->greeting = $this->_readLine();
			$this->_log("Connected to: $hostname:$port\n");
			
			if( $apop == true OR $apop == 1 )
			{
				if( $this->_APOP() )
				{
					$this->_log("Authentication succeeded, APOP...\n");
					return true;
				}
				else
				{
					$this->_log("Authentication failed, APOP, attempting USER auth...\n");
				}
			}
			
			if( $this->_USER() )
			{
				if( $this->_PASS() )
				{
					$this->_log("Authentication succeeded, USER...\n");
					return true;
				}
				else
				{
					$this->_log("Authentication failed: Quitting, disconnected...\n");
					$this->_QUIT();
					$this->disconnect();
					return false;
				}
			}
			else
			{
				$this->_log("Authentication failed: Quitting, disconnected...\n");
				$this->_QUIT();
				$this->disconnect();
				return false;
			}
		}
	}

	function disconnect()
	{
		$this->_QUIT();
		fclose($this->sock);
	}

	/*
		This function still needs some work...
	*/
	function _APOP($username = false, $password = false)
	{
		$username 	= ( !$username ? $this->username : $username );
		$password	= ( !$password ? $this->password : $password );
		
		//eregi('<([^>]*)>', $this->greeting, $tmp);
		$digest_string = "<{$this->greeting}>{$password}";
		$digest = md5($digest_string);
		$this->_send("APOP {$username} {$digest}\r\n");
		return $this->_ok();
	}

	function _STAT()
	{
		$this->_send("STAT\r\n");
		if( $this->_ok() )
		{
			list($tmp, $number, $size) = explode(' ', trim( $this->_getBuffer() ) );
			return array('number' => $number, 'size' => $size);
		}
		else
		{
			return false;
		}
	}

	function _USER($username = false)
	{
		$username = ( !$username ? $this->username : $username);
		$this->_send("USER {$username}\r\n");
		return $this->_ok();
	}

	function _PASS($password = false)
	{
		$password = ( !$password ? $this->password : $password);
		$this->_send("PASS {$password}\r\n");
		return $this->_ok();
	}

	function _LIST($msg = '')
	{
		$messages = array();
		$this->_send("LIST {$msg}\r\n");
		$buffer = trim($this->_readMultiline());
		$lines = explode("\n", $buffer);
		if( $this->_ok($lines[0]) )
		{
			for($i = 1; $i < count($lines); $i++)
			{
				$tmp = explode(' ', trim($lines[$i]) );
				$messages[] = array('id' => $tmp[0], 'size' => $tmp[1]);
			}
			return $messages;
		}
		else
		{
			return false;
		}
	}
	
	function _UIDL($msg = '')
	{
		$messages = array();
		$this->_send("UIDL {$msg}\r\n");
		$buffer = trim( $this->_readMultiline() );
		$lines = explode("\n", $buffer);
		if( $this->_ok($lines[0]) )
		{
			for($i = 1; $i < count($lines); $i++)
			{
				$tmp = explode(' ', trim($lines[$i]) );
				$messages[] = array('id' => $tmp[0], 'uidl' => $tmp[1]);
			}
			return $messages;
		}
		else
		{
			return false;
		}
	}

	function _RETR($id)
	{
		$this->_send("RETR {$id}\r\n");
		if( $this->_ok() )
		{
			$message = $this->_readMultiLine();
			return $message;
		}
		else
		{
			return false;
		}
	}

	function _DELE($id)
	{
		$this->_send("DELE {$id}\r\n");
		return $this->_ok();
	}

	function _NOOP()
	{
		$this->_send("NOOP\r\n");
		return $this->_ok();
	}

	function _RSET()
	{
		$this->_send("RSET\r\n");
		return $ths->_ok();
	}
	
	function _QUIT()
	{
		$this->_send("QUIT\r\n");
		return $this->_ok();
	}
	
/* -- Here are the rest of the object functions */

	function _log($string)
	{
		$this->log .= $string;
	}

	function _logSession($string)
	{
		$this->sessionlog .= $string;
	}

	function _send($string)
	{
		$this->_logSession("S: [$string]\n");
		fputs($this->sock, $string);
	}

	function _readLine()
	{
		$buff = '';
		
		do
		{
			$buff .= fgets($this->sock, 128);
		}
		while( substr($buff, -2, 2) != "\r\n" );

		// Don't trim this!
		$this->buffer = $buff;
		$this->_logSession("R: [$buff]\n");
		return $this->buffer;
	}

	function _readMultiLine()
	{
		do
		{
			$buff .= fgets($this->sock, 128);
		}
		while( substr($buff, -5, 5) != "\r\n.\r\n" );
		
		$this->_logSession("R: [$buff]\n");
		
		$this->buffer = substr($buff, 0, -3);
		return $this->buffer;
	}

	function _ok($line = false)
	{
		if( !$line )
		{
			$this->_readLine();
			$line = $this->buffer;
		}
		
		if( substr($line, 0, 1) == '+' )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function _getBuffer()
	{
		return $this->buffer;
	}

	function _logErrors()
	{
		$this->_log("Error: {$this->responseno}: {$this->responsestr}\n");
	}
	
	function getSession()
	{
		return $this->sessionlog;
	}
	
	function getSessions()
	{
		return $this->completesessionlog;
	}

	function getLog()
	{
		return $this->log;
	}

	function getLogs()
	{
		return $this->completelog;
	}
}
?>
