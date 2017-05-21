<?php

class RSS_Item
{
	var $link;
	var $title;
	var $description;
	
	function RSS_Item($link, $title='', $description='')
	{
		$this->link = $link;
		$this->title = $title;
		$this->description = $description;
	}
	
	function get_output()
	{
		$xml[] = '<item>';
		$xml[] = "<title>{$this->title}</title>";
		$xml[] = "<link>{$this->link}</link>";
		$xml[] = "<description>{$this->description}</description>";
		$xml[] = '</item>';
		return join("\r\n", $xml);
	}
}

?>