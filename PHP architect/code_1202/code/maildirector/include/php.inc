<?php

/**
 * Builds a secure file name. Removes any tainted paths
 */
function prepFile( /* func_get_args() */ )
{
	$args = func_get_args();
	$file = array_pop($args);
	$path = prepPath($args);
	
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
function prepPath( /* func_get_args() */ )
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

/* Stub for file_get_contents() in PHP CVS */
if ( !function_exists('file_get_contents') )
{
	function file_get_contents($filename, $include_path = 0)
	{
		if ( file_exists($filename) && !is_dir($filename) )
		{
			if ( $fp = fopen($filename, "rb", $include_path) )
			{
				$contents = '';
				while ( !feof($fp) && $fp )
				{
					$contents .= fread($fp, 1024);
				}
				fclose($fp);
				return $contents;
			}
		}
		
		return;
	}
}

if ( !function_exists('file_put_contents') )
{
	function file_put_contents($filename, $data, $include_path = 0)
	{
		if ( $fp = fopen($filename, "wb", $include_path) )
		{
			fwrite($fp, $data, strlen($data) );
			fclose($fp);
			return true;
		}
		return false;
	}
}

?>
