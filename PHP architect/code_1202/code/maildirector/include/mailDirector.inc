<?php /* $Id: mailDirector.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once( dirname(__FILE__) . '/config.inc');
include_once( dirname(__FILE__) . '/mdMaildir.inc');

class mailDirector
{
	var $config		= null;
	var $default	= null;
	
	function mailDirector()
	{
	}
	
	function fatal($err)
	{
		echo '<b>Fatal Error: ' . $err;
		exit;
	}
	
	function loadConfig()
	{
		if ( defined('MD_CONFIG') && file_exists(MD_CONFIG) )
		{
			$keys = array('check','delete','name','address','alias','user','pass','pop3','smtp','inbox','sent','draft','trash');

			$this->config = array();

			/* Slurp our file in */
			$section = '';
			foreach ( @file(MD_CONFIG) AS $line )
			{
				if ( preg_match('/^[;#]+/', $line) )
				{
					continue;
				}
				elseif ( preg_match('/\[([^\]]*)\]/', $line, $match) )
				{
					$section = trim($match[1]);
					continue;
				}
				elseif ( preg_match('/\s*(.+)\s*=\s*["]?([^\"]+)["]?\s*/', $line, $match) )
				{
					$key = trim($match[1]);
					$val = trim($match[2]);

					if ( isset($conf[$section][$key]) )
					{
						if ( !is_array($conf[$section][$key]) )
						{
							$tmp = array($conf[$section][$key], $val);
							$val = $tmp;
							$conf[$section][$key] = $val;
						}
						else
						{
							$conf[$section][$key][] = $val;
						}
					}
					else
					{
						$conf[$section][$key] = $val; 
					}
				}
			}
			
			
			if ( !$conf['default']['mail'] || $conf['default']['mail'] == '' )
			{
				$this->fatal('[default].mail has not been defined, please check the config file.');
			}

			$m = $conf['default']['mail'];
			if ( !is_dir($m . '/new/') || !is_dir($m . '/cur/') || !is_dir($m . '/tmp/') )
			{
				$oMaildir = new maildir($m);
				$oMaildir->create();
			}
			
			foreach ( $conf AS $key => $array )
			{
				/* Make sure we aren't processing something named [key] */
				if ( $key == 'default' )
				{
					$this->default = $array;
					continue;
				}

				/* Go through and ONLY set the keys that we want */
				foreach ( $keys AS $k )
				{
					if ( in_array($k, array('delete','pop3','smtp','inbox','sent','draft','trash') ) )
					{
						$value = ( $array[$k] != '' ? $array[$k] : $conf['default'][$k] );
					}
					elseif ( $k == 'check' )
					{
						switch ( $array[$k] )
						{
							case 1:
								$value = 1;
								break;
								
							case '0':
								$value = 0;
								break;

							default:
								$value = $conf['default'][$k];
						}
					}
					else
					{
						$value = $array[$k];
					}

					if ( in_array($k, array('inbox','sent','draft','trash') ) )
					{
						$value = $conf['default']['mail'] . $value;
						
						if ( !is_dir($value . '/new/') || !is_dir($value . '/cur/') || !is_dir($value . '/tmp/') )
						{
							logWarning("{$value} is not a maildir! Attempting to create");
							$oMaildir = new maildir($value);
							$oMaildir->create();
						}
					}
					
					$this->config[$key][$k] = ( $value !== '' ? $value : $conf['default'][$k] );
				}
			}
		}
		else
		{
			$this->fatal('Config file could not be loaded ' . MD_CONFIG);
		}
	}

	function getConfig($account, $key)
	{
		if ( $this->config === null )
		{
			$this->loadConfig();
		}
		
		return $this->config[$account][$key];
	}

	function getDefault($key)
	{
		if ( $this->default === null )
		{
			$this->loadConfig();
		}
		
		return $this->default[$key];
	}

	function fetchMaildir($folder = '')
	{
		if ( $this->config === null )
		{
			$this->loadConfig();
		}

		$folder = str_replace($this->getDefault('mail'), '', $folder);
		
		return new mdMaildir($this->getDefault('mail'), $folder);
	}
}
?>
