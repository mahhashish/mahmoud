<?php

require 'HTML_Scraper.php';

// NOTE: Make sure the cookie jar file is writable
class Scout extends HTML_Scraper
{
	var $url;
	var $form_method;
	var $parameters = array();
	var $submit_buttons = array();
	var $user_agent = "Scout/0.01";
	
	function Scout()
	{
		parent::HTML_Scraper();
	}
	
	// set the values for all parameters (effectively replacing them)	
	function set_all_parameters($parameters=array())
	{
		$this->parameters = $parameters;
	}
	
	// set the value for a particular parameter
	function set_parameter($parameter, $value)
	{
		$this->parameters[$parameter] = $value;
	}
	
	// remove a particular parameter from the parameter list
	function unset_parameter($parameter)
	{
		if (isset($this->parameters[$parameter])) 
		{
		    unset($this->parameters[$parameter]);
		}
	}
	
	// set the effective user agent string (emulate browsers)
	function set_user_agent($user_agent)
	{
		$this->user_agent = $user_agent;
	}
	
	// submit button wrapper for request()
	function submit($button=null)
	{
		if (isset($button))
		{
			if (! isset($this->submit_buttons[$button]))
			{
				return false;
			}
			$this->parameters[$this->submit_buttons[$button]] = $button;
		}
		$this->request();
	}
	
	// TODO: doesn't support building own requests, specifying
	// custom headers
	function request($url=null, $method=null)
	{
		// these may have been set by select_form() or select_link()
		if (isset($url)) 
		{
			$this->url = $url;
		}
		if (isset($method)) 
		{
			$this->form_method = $method;
		}

		// initialize the curl session
		$ch = curl_init();
		// returns the headers
		curl_setopt($ch, CURLOPT_HEADER, 1);
		// returns the page contents
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
		// set the apparent user agent
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);   
		// make curl follow redirects
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		// set cookie location
		curl_setopt($ch, CURLOPT_COOKIEJAR, "{$GLOBALS['tmp_dir']}/scout_cookies.txt");		
		curl_setopt($ch, CURLOPT_COOKIEFILE, "{$GLOBALS['tmp_dir']}/scout_cookies.txt");		

		// aggregate variables to send
		$query_string = '';
		$this->form_method = strtolower($this->form_method);
		if ('post' == $this->form_method)
		{
			if (count($this->parameters) > 0) 
			{
				// passing array allows us to upload files
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->parameters);   
			}
		}
		else if ('get' == $this->form_method) 
		{
			$parameters = array();
			foreach ($this->parameters as $key=>$value)
			{
				$parameters[] = urlencode($key) . '=' . urlencode($value);
			}			
			$query_string = '?' . join('&', $parameters);
		}
	
		// specify the target
		curl_setopt($ch, CURLOPT_URL, $this->url . $query_string);		

		// get a file descriptor for placing the output in
		$result_file = tempnam($GLOBALS['tmp_dir'], 'html_');
		if (! $fp = fopen($result_file, 'w')) 
		{
			die("Couldn't open curl result file ({$result_file})");
		}
		curl_setopt($ch, CURLOPT_FILE, $fp);		
		
		// clean up our temp file when script ends
		if ($this->cleanup) 
		{
			register_shutdown_function(array(&$this, '_delete_html_file'));
		}
		
		if (! curl_exec($ch)) 
		{
			die("Couldn't open URL ({$this->url}}");
		}
		
		// get the URL that was actually used (after any location headers)
		// and strip off any query string data
		$this->url = preg_replace('/\?.*$/', '', curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		fclose($fp);
		
		$this->parse($result_file);

		return $status;		
	}
	
	// destructor-type function
	function _delete_html_file()
	{
		@unlink($this->html_file);
	}
	
	// takes the fields from the form and loads the parameter array 
	// with their values.  These values can then be overridden
	function select_form($condition_type='index', $condition=1)
	{
		// get_forms() returns an array, but we're always only getting one
		$forms = $this->get_forms($condition_type, $condition);
		
		if (count($forms) == 0) 
		{
		    return false;
		}
		
		$form = end($forms);
	
		// if no action specified, use the current url
		if (isset($form['attributes']['actions'])) 
		{
			$this->url = $this->resolve_relative_url($form['attributes']['action'], $this->url);
		}
		
		// if no method specified, use 'get'
		$this->form_method = 'get';
		if (isset($form['attributes']['method'])) 
		{
			$this->form_method = $form['attributes']['method'];
		}
		
		// clear current params
		$this->parameters = array();
		$this->submit_buttons = array();

		if (isset($form['fields'])) 
		{
			foreach ($form['fields'] as $field)
			{
				// this doesn't handle array names for elements properly
				//   ie. <select name="choices[]">...</select>
				if ('input' == $field['name'])
				{
					// submit buttons
					if ($field['attributes']['type'] == 'submit') 
					{
					    $this->submit_buttons[$field['attributes']['value']] = $field['attributes']['name'];
					}		
					// checkboxes
					else if (($field['attributes']['type'] == 'checkbox') 
								&& (isset($field['attributes']['checked'])))
					{
					    $this->parameters[$field['attributes']['name']] = $field['attributes']['value'];
					}
					// radio buttons
					else if (($field['attributes']['type'] == 'radio') 
								&& (isset($field['attributes']['checked'])))
					{
					    $this->parameters[$field['attributes']['name']] = $field['attributes']['value'];					
					}
					// everything else
					else 
					{
					    $this->parameters[$field['attributes']['name']] = $field['attributes']['value'];
					}
				}
				else if ('textarea' == $field['name']) 
				{
					$this->parameters[$field['attributes']['name']] = $field['text'];
				}
				// select??
				else if ('select' == $field['name'])
				{
					// not yet implemented - need to come up with good way to handle
					// options, and allow selection of options.
					
					// in the meantime, must specify this parameter manually using set_parameter()
				}
			}
		}
		return $form;
	}

	// takes a link and treats it like a 'get' form, loading the parameter array 
	// with the link values.  These values can then be overridden
	function select_link($condition_type='index', $condition=1)
	{
		// get_links() returns an array, but we're always only getting one
		$links = $this->get_links($condition_type, $condition);
		
		if (count($links) == 0) 
		{
		    return false;
		}

		$link = end($links);

		$link_parts = parse_url($link['attributes']['href']);
		
		$host = '';
		if (isset($link_parts['host'])) 
		{
		    $host = "{$link_parts['scheme']}://{$link_parts['host']}/";
		}
		$url = "{$host}{$link_parts['path']}";
		$this->url = $this->resolve_relative_url($url, $this->url);
		
		$this->form_method = 'get';
		
		// clear current params
		$this->parameters = array();
		$this->submit_buttons = array();
		
		if (isset($link_parts['query'])) 
		{
		    $pairs = preg_split('/&amp;/', $link_parts['query']);
			foreach ($pairs as $pair)
			{
				list($name, $value) = split('=', $pair);
				$this->parameters[$name] = $value;
			}
		}

		return $link;
	}
	
	// get the URL being used, or that was used
	function get_url()
	{
		return $this->url;
	}
}

?>