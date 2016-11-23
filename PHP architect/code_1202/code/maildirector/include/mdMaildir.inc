<?php /* $Id: mdMaildir.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once( dirname(__FILE__) . '/maildir.inc');
include_once( dirname(__FILE__) . '/mdMaildirMessage.inc');

class mdMaildir extends maildir
{
	var $root		= '';
	var $path		= '';
	var $name		= '';

	function mdMaildir($root, $folder = '')
	{
		$this->maildir( $this->_prepPath($root,$folder) );
		
		/* We have a folder name and a root also */
		$this->root = $root;
		$this->path	= $folder;
		$this->name	= $folder;

		/* Load the name for this folder */
		$this->getName();
	}

	function getName()
	{
		$file = $this->_prepFile($this->folder, '.name');
		if ( file_exists($file) )
		{
			$this->name = file_get_contnets($file);
		}

		return $this->name;
	}

	function setName($name)
	{
		$file = $this->_prepFile($this->folder, '.name');
		if ( file_put_contents($file, $name) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Override the fetch function
	 */
	function fetch($folder)
	{
		return new mdMaildir($this->root, $this->_prepPath($this->path, $folder));
	}

	function fetchMessage($mid)
	{
		$file = $this->getMessage($mid);
		if ( $file )
		{
			return new mdMaildirMessage($this->folder, $file);
		}

		return false;
	}	

	function newMessage()
	{
		return new mdMaildirMessage($this->folder);
	}
}
?>
