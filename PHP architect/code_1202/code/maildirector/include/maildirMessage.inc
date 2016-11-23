<?php /* $Id: maildirMessage.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

class maildirMessage
{
	var $messageId	= '';		/* message id */
	var $file		= '';		/* filename */
	var $folder		= '';		/* /path/to/inbox */
	var $state		= 'new';	/* new,cur,tmp */
	var $size		= 0;

	var $D			= false;
	var $F			= false;
	var $R			= false;
	var $S			= false;
	var $T			= false;
	
	/**
	 * Folder is /path/to/maildir and File is [new|cur|tmp]/messageId:1,S
	 */
	function maildirMessage($folder = null, $file = null)
	{
		if ( $folder !== null )
		{
			$this->setFolder($folder);
		}

		if ( $file !== null )
		{
			$this->setFile($file);
		}
	}

	function setFolder($folder)
	{
		$this->folder = $folder;
	}

	function setFile($file)
	{
		$s = strpos($file, '/');
		$this->state		= substr($file, 0, $s);
		$this->file			= substr($file, $s + 1);
		
		list($this->messageId) = explode(':', substr($file, $s + 1) );
		
		$this->parseInfo();
	}
	
	function getFilename()
	{
		return $this->state . '/' . $this->file;
	}

	function parseInfo()
	{
		$c = strrpos($this->file, ':');
		foreach ( array('D','F','R','S','T') AS $i )
		{
			$this->{$i} = ( strpos($this->file, $i, $c) ? true : false );
		}
	}

	
	function createTmp()
	{
		$this->state = 'tmp';
		return $this->create();
	}
	
	/**
	 * Creates a new message
	 * ->create();
	 * ->store('string');
	 */
	function create()
	{
		$this->messageId 	= $this->makeId();
		$this->file 		= $this->messageId . $this->_getInfoString();
		$file = $this->_prepFile($this->folder, $this->state, $this->file);
		
		if ( !file_exists($file) )
		{
			if ( touch($file) )
			{
				return $this->file;
			}
		}

		return false;
	}
	
	function destroy()
	{
		$file = $this->_prepFile($this->folder, $this->state, $this->file);

		if ( @unlink($file) )
		{
			return true;
		}

		return false;
	}
	
	/**
	 * Writes a string to a message
	 */
	function store($message)
	{
		if ( file_put_contents( $this->_prepFile($this->folder, $this->state, $this->file), $message ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Returns the message from a string
	 */
	function fetch()
	{
		$file = $this->_prepFile($this->folder, $this->state, $this->file);
		if ( file_exists($file) )
		{
			return file_get_contents($file);
		}

		return false;
	}

	function setDraft($bool = true)
	{
		$this->D = ($bool ? true : false);
		return $this->_writeInfo();
	}
	
	function setFlag($bool = true)
	{
		$this->F = ($bool ? true : false);
		return $this->_writeInfo();
	}
	
	function setReply($bool = true)
	{
		$this->R = ($bool ? true : false);
		return $this->_writeInfo();
	}
	
	function setSeen($bool = true)
	{
		$this->S = ($bool ? true : false);
		return $this->_writeInfo();
	}
	
	function setTrash($bool)
	{
		$this->T = ($bool ? true : false);
		return $this->_writeInfo();
	}

	function _writeInfo()
	{
		$file	= $this->_prepFile($this->folder, $this->state, $this->file);
		$newF	= $this->messageId . $this->_getInfoString();
		$new	= $this->_prepFile($this->folder, $this->state, $newF);

		if ( @rename($file, $new) )
		{
			$this->file = $newF;
			return true;
		}
		
		return false;
	}

	/**
	 * Moves the message to the new folder
	 */
	function moveNew()
	{
		return $this->moveFolder($this->folder, 'new');
	}
	
	function moveCur()
	{
		return $this->moveFolder($this->folder, 'cur');
	}
	
	function moveTmp()
	{
		return $this->moveFolder($this->folder, 'tmp');
	}

	function moveFolder($folder, $state = null)
	{
		$file	= $this->_prepFile($this->folder, $this->state, $this->file);
		$state 	= ( $state === null ? $this->state : $state );
		$new 	= $this->_prepFile(	$folder, $state, $this->file );

		if ( @rename($file, $new) )
		{
			$this->folder	= $folder;
			$this->state	= $state;
			return true;
		}
		else
		{
			logError("Failed to move: {$file} to {$new}");
		}
		
		return false;
	}


	function _getInfoString()
	{
		$info = ':1,';
		foreach ( array('D','F','R','S','T') AS $i )
		{
			if ( $this->{$i} === true )
			{
				$info .= $i;
			}
		}

		if ( $info == ':1,' )
		{
			$info = '';
		}

		return $info;
	}
	
	/**
	 * Makes a message id
	 */
	function makeId()
	{
		global $_myMaildirId, $_myMaildirHost;

		if ( $_myMaildirHost == '' )
		{
			list($_myMaildirHost) = explode('.', gethostbyaddr('127.0.0.1') );
		}
		
		return sprintf('%s.%s_%s.%s', time(), getmypid(), ++$_myMaildirId, $_myMaildirHost );
	}

	/**
	 * Builds a secure file name. Removes any tainted paths
	 */
	function _prepFile( /* func_get_args() */ )
	{
		$args = func_get_args();
		$file = array_pop($args);
		$path = $this->_prepPath($args);

		$file = str_replace('//', '', $file);
		$file = str_replace('../', '', $file);
		
		if ( substr($file, 0, 1) == '/' )
		{
			$file = substr($file, 1);
		}
		
		return $path . $file;
	}

	/**
	 * This auto-detects the args
	 */
	function _prepPath( /* func_get_args() */ )
	{
		$path = '';
		$part = array();
		$args = func_get_args();

		/* We can take an array as our first parameter */
		foreach ( $args AS $a )
		{
			if ( is_array($a) )
			{
				foreach ( $a AS $p )
				{
					$part[] = $p;
				}
			}
			else
			{
				$part[] = $a;
			}
		}
		
		foreach ( $part AS $p )
		{
			$p = str_replace('../', '', $p);
			$p = str_replace('./', 	'', $p);
			$p = str_replace('//', 	'/', $p);
			
			if ( substr($p, 0, 1) != '/' )
			{
				$p = '/' . $p;
			}

			if ( substr($p, -1) == '/' )
			{
				$p = substr($p, 0, -1);
			}
			
			$path .= $p;
		}
		
		if ( substr($path, -1) != '/' )
		{
			$path .= '/';
		}
		
		return $path;
	}
}
?>
