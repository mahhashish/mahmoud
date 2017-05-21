<?php

require_once "HTML_Parser.php";

class HTML_Scraper extends HTML_Parser
{
	function HTML_Scraper()
	{
		parent::HTML_Parser();
	}
	
	// find instances of a tag in the document
	function get_tags($condition_type='', $condition='')
	{	
		switch ($condition_type)
		{
			case 'tag':
				$expression = "//{$condition}";
				break;
			case 'xpath':
				$expression = $condition;
				break;
			default:
				$expression = "//*";
		} // switch
		
		$tags = $this->evaluate($expression);
		return $tags;
	}
		
	function get_links($condition_type='', $condition='')
	{
		switch ($condition_type)
		{
			case 'text':
				$expression = "//a[contains(text(), '{$condition}')]";
				break;
			case 'index': 
				// fall thru
			default:
				$expression = '//a';
		} // switch

		$links = $this->get_tags('xpath', $expression);

		// used to get around a limitation in this xpath library
		//   - it won't handle "(//foo)[2]" properly
		// TODO: this is a pig - need caching or something
		if ('index' == $condition_type) 
		{
		    return array($links[$condition - 1]);
		}

		return $links;
	}

	function get_forms($condition_type='', $condition='')
	{
		switch ($condition_type)
		{
			case 'name':
				$expression = "//form[contains(@name, '{$condition}')]";
				break;
			case 'index': 
				// fall thru
			default:
				$expression = '//form';
		} // switch

		$forms = $this->get_tags('xpath', $expression);
	
		// get inputs
		$form_data = array();
		foreach ($forms as $index=>$form)
		{
			$form_data[$index] = $form;
			$form_data[$index]['fields'] = $this->_get_form_elements($form);
		}

		// used to get around a limitation in this xpath library
		//   - it won't handle "(//foo)[2]" properly
		if ('index' == $condition_type) 
		{
		    return array($form_data[$condition - 1]);
		}

		return $form_data;
	}

	function _get_form_elements($form)
	{
		$expression = "{$form['node']}//input | {$form['node']}//select | {$form['node']}//select/option | {$form['node']}//textarea";
		$fields = $this->get_tags('xpath', $expression);
		return $fields;
	}
		
	function get_images($condition_type='', $condition='')
	{
		switch ($condition_type)
		{
			case 'index': 
				// fall thru
			default:
				$expression = "//img";
				break;
				;
		} // switch
		
		$images = $this->get_tags('xpath', $expression);

		// used to get around a limitation in this xpath library
		//   - it won't handle "(//foo)[2]" properly
		if ('index' == $condition_type) 
		{
		    return array($images[$condition - 1]);
		}
		return $images;
	}
}

?>