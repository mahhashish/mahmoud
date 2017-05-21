<?php

require "XPath.class.php";

$tidy_path = '/usr/local/bin/tidy';
$tmp_dir = '/var/tmp';

class HTML_Parser
{
	var $html_file = null;
	var $xhtml_file = null;
	var $xpath = null;
	var $cleanup = false;
	
	function HTML_Parser()
	{
		// empty
	}
	
	// allows and prevents the removal of old files
	function set_cleanup($cleanup=true)
	{
		$this->cleanup = $cleanup;
	}
	
	// subclasses can reimplement this to clean up themselves,
	// as long as they make the call to parent::
	function reset_parser()
	{
		if ($this->cleanup) 
		{
			if (isset($this->xhtml_file)) 
			{
			    unlink($this->xhtml_file);
			}		    
		}
		if (isset($this->xpath)) 
		{
		    unset($this->xpath);
		}
	}
	
	function parse($html_file)
	{
		// clear the object and get ready for a new file
		$this->reset_parser();
		
		$this->html_file = $html_file;

		// make xhtml out of our html
		$this->_process();
		
		// prepare our xpath environment
		$this->xpath = &new XPath($this->xhtml_file);
	}
	
	function _process()
	{
		// get xhtml output
		exec("{$GLOBALS['tidy_path']} --force-output yes --numeric-entities yes --output-xhtml yes -q {$this->html_file} 2>/dev/null", $xhtml_output);
		
		// open xhtml output file
		$this->xhtml_file = tempnam($GLOBALS['tmp_dir'], 'xhtml_');
		if (! $fp = fopen($this->xhtml_file, 'w')) 
		{
		    die("Unable to open temp file: {$this->xhtml_file}");
		}

		// clean up our temp file when script ends
		if ($this->cleanup) 
		{
			register_shutdown_function(array(&$this, '_delete_xhtml_file'));	    
		}
		
		// dump out the xhtml output
		foreach ($xhtml_output as $line)
		{
			// add newline because exec() didn't return any
			fputs($fp, $line . "\n", strlen($line . "\n"));
		}
		
		// close xhtml output file
		fclose($fp);
	}

	// destructor-type function
	function _delete_xhtml_file()
	{
		@unlink($this->xhtml_file);
	}

	function evaluate($expression)
	{
		$results = $this->xpath->match($expression);
		
		// returned a node-set
		if (is_array($results)) 
		{
			$data = $this->get_node_set_data($results);
			return $data;
		}
		// returned a scalar
		else
		{
			return $results;
		}
	}

	// dump all node-set data into a nice little array for us
	function get_node_set_data($node_set)
	{
		$results = array();
		foreach ($node_set as $index=>$node)
		{
			$results[$index]['node'] = $node;
			$results[$index]['name'] = $this->xpath->match("name({$node})");
			$results[$index]['text'] = $this->xpath->getData($node);
			$results[$index]['attributes'] = $this->xpath->getAttributes($node);
		}
		return $results;
	}
	
	// doesn't account for <base href ... > tags
	// doesn't handle path info
	// should build into parser?
	function resolve_relative_url($url, $base)
	{
		// get the parts of the old url
		$abs_url_parts = parse_url($base);
		// get the parts of the new url
		$rel_url_parts = parse_url($url);
		
		// is not relative?
		if (! empty($rel_url_parts['host'])) 
		{
			return $url;
		}
		
		if (! empty($rel_url_parts['path'])) 
		{
			if ($rel_url_parts['path']{0} == '/')
			{
		    	return "{$abs_url_parts['scheme']}://{$abs_url_parts['host']}{$rel_url_parts['path']}";
			}
			
			$abs_path_parts = split('/', $abs_url_parts['path']);
			if (strstr(end($abs_path_parts), '.'))
			{
				array_pop($abs_path_parts);
			}
			$rel_path_parts = split('/', $rel_url_parts['path']);
			$path_parts = array_merge($abs_path_parts, $rel_path_parts);
			
			$new_abs_path = array();
			foreach ($path_parts as $part)
			{
				if ('' == $part) 
				{
					continue;    
				}
				if ('.' == $part) 
				{
					continue;    
				}
				if ('..' == $part) 
				{
					array_pop($absolute_parts);
					continue;
				}
				$new_abs_path[] = $part;
			}
			$path = join('/', $new_abs_path);
	    	return "{$abs_url_parts['scheme']}://{$abs_url_parts['host']}/{$path}";
		}	
	}
}

?>
