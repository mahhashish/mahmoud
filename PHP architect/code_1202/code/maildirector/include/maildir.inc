<?php /* $Id: maildir.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

/* This class is used when working with maildirs. (See http://cr.yp.to/proto/maildir.html)
 * It handles message functions such as setting flags, moving, deleting, etc.
 * But not deal with the message itself such as parsing. The only thing it does is return the 
 * full raw text.
 * 
 * API Example:
 *
 * $oMaildir = new maildir('/var/spool/mail/INBOX');
 * $oMaildir->getNew();						// Get's message ID's
 * $file = $oMaildir->fetchMessage($mid);	// Returns the path to a file
 *
 * // Create new folder
 * $oMaildir = new maildir('/var/spool/mail/INBOX/oof');
 * $oMaildir->create();
 */
class maildir
{
	var $folder		= '';
	
	var $folders	= null;
	
	var $new		= null;
	var $tmp		= null;
	var $cur		= null;

	/**
	 * Constructor
	 */
	function maildir($folder = null)
	{
		if ( $folder !== null )
		{
			$this->setFolder($folder);
		}
	}

	function setFolder($folder)
	{
		$this->folder 	= $folder;
	}

	/**
	 * Creates a new maildir
	 */
	function create($folder = '')
	{
		$p = $this->_prepPath($this->folder);
		$make = array(	$p,
						$this->_prepPath($p, 'new'),
						$this->_prepPath($p, 'cur'),
						$this->_prepPath($p, 'tmp')
						);
		
		$result = true;
		
		foreach ( $make AS $m )
		{
			if ( !file_exists($m) )
			{
				if ( !@mkdir($m) )
				{
					$result = false;
				}
			}
		}

		return $result;
	}
	
	/**
	 * Returns a mail dir object
	 */
	function fetch($folder)
	{
		if ( isset($this->folders[$folder]) )
		{
			return new maildir($this->_prepPath($this->folder, $folder) );
		}

		return false;
	}

	function destroy()
	{
		/* This only deletes in the current directory, they're responsible for deleteing sub folders (minus new, cur, tmp) */
		$this->_scanNew();
		$this->_scanCur();
		$this->_scanTmp();
		$this->_scanFolders();

		foreach ( array($this->new, 'new', $this->cur, 'cur', $this->tmp, 'tmp', $this->folders) AS $items )
		{
			if ( !is_array($items) )
			{
				$items = array($items);
			}
			
			foreach ( $items AS $file )
			{
				$f = $this->_prepPath($this->folder, $file);
				if ( !unlink($f) )
				{
					logError('Failed to remove: ' . $f);
				}
			}
		}
	}

	function expunge()
	{
		$c = 0;
		foreach ( $this->getMessages() AS $mid )
		{
			$oM = $this->fetchMessage($mid);
			if ( $oM->T == true )
			{
				if ( $oM->destroy() )
				{
					$c++;
				}
			}
		}
		$this->new = $this->cur = $this->tmp = null;

		return $c;
	}

	function _scanNew()
	{
		$this->new = $this->_ls('new');
		
		if ( !is_array($this->new) )
		{
			$this->new = array();
		}
	}

	function _scanCur()
	{
		$this->cur = $this->_ls('cur');

		if ( !is_array($this->cur) )
		{
			$this->cur = array();
		}
	}

	function _scanTmp()
	{
		$this->tmp = $this->_ls('tmp');

		if ( !is_array($this->tmp) )
		{
			$this->tmp = array();
		}
	}

	function _scanFolders()
	{
		$this->folders = $this->_ls('.'); 
	}

	function fetchMessage($mid)
	{
		$file = $this->getMessage($mid);
		if ( $file )
		{
			return new maildirMessage($this->folder, $file);
		}

		return false;
	}

	function getMessage($mid)
	{
		if ( $this->cur === null )
		{
			$this->_scanCur();
		}

		if ( isset($this->cur[$mid]) )
		{
			return $this->cur[$mid];
		}
		
		if ( $this->new === null )
		{
			$this->_scanNew();
		}

		if ( isset($this->new[$mid]) )
		{
			return $this->new[$mid];
		}
		
		if ( $this->tmp === null )
		{
			$this->_scanTmp();
		}
		
		if ( isset($this->tmp[$mid]) )
		{
			return $this->tmp[$mid];
		}
		
		return false;
	}

	function getMessages()
	{
		$m = array_merge(	$this->getNew(),
							$this->getCur(),
							$this->getTmp()
						);
		sort($m, SORT_STRING);
		return $m;
	}

	function getNumMessages()
	{
		return $this->getNumNew() + $this->getNumCur() + $this->getNumTmp();
	}
	
	function getNew()
	{
		if ( $this->new === null )
		{
			$this->_scanNew();
		}
		$keys = array_keys( $this->new );
		sort($keys, SORT_STRING);
		return $keys;
	}

	function getNumNew()
	{
		if ( $this->new === null )
		{
			$this->_scanNew();
		}
		return count($this->new);
	}

	function getCur()
	{
		if ( $this->cur === null )
		{
			$this->_scanCur();
		}
		$keys = array_keys($this->cur);
		sort($keys, SORT_STRING);
		return $keys;
	}

	function getNumCur()
	{
		if ( $this->cur === null )
		{
			$this->_scanCur();
		}
		return count($this->cur);
	}
	
	function getTmp()
	{
		if ( $this->tmp === null )
		{
			$this->_scanTmp();
		}
		$keys = array_keys( $this->tmp );
		sort($keys, SORT_STRING);
		return $keys;
	}

	function getNumTmp()
	{
		if ( $this->tmp === null )
		{
			$this->_scanTmp();
		}
		return count($this->tmp);
	}

	function getFolders()
	{
		if ( $this->folders === null )
		{
			$this->_scanFolders();
		}

		return $this->folders;
	}
	
	/* Returns an array of files found, no attributes have been parsed though */
	function _ls($sub = '')
	{
		if ( $sub === '.' )
		{
			$folders = array('');
			$dir = true;
		}
		else
		{
			/* Only this the folder sub */
			if ( ($sub == '') )
			{
				$sub = array('new','cur','tmp');
			}
			elseif ( !is_array($sub) )
			{
				$sub = array($sub);
			}

			$folders = array_intersect(array('cur','new','tmp'), $sub);
		}

		$ls	= array();
		foreach ( $folders AS $f)
		{
			$path = $this->_prepPath($this->folder, $f);
			
			if ( $d = @opendir($path) )
			{
				while ( ($e = readdir($d)) !== false )
				{
					/* Exclude 'hidden' files */
					if ( substr($e, 0, 1) == '.' )
					{
						continue;
					}
					
					if ( !$dir && !is_dir($path . '/' . $e) )
					{
						$mid = ( strpos($e, ':') ? substr($e, 0, strpos($e, ':')) : $e );
						$ls[$mid] = $f . '/' . $e;
					}
					/* If this directory path has a /new/' subfolder, then it's a maildir dir and we'll use it */
					elseif ( $dir && is_dir($path . '/' . $e ) )
					{
						if ( in_array($e, array('new','cur','tmp') ) )
						{
							continue;
						}
						$ls[$e] = $e;
					}
				}
				closedir($d);
			}
			else
			{
				logError('Failed to open: ' . $path);
				return false;
			}
		}
		
		return $ls;
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
