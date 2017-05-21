<?php

require_once 'RSS_Item.php';

class RSS_Channel
{
	var $items = array();
	var $link;
	var $title;
	var $description;
	
	function RSS_Channel($link, $title='', $description='')
	{
		$this->link = $link;
		$this->title = $title;
		$this->description = $description;
	}
	
	function add_item($item)
	{
		$this->items[] = $item;
	}
	
	function get_output()
	{
		$xml[] = '<?xml version="1.0"?>';
		$xml[] = '<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN" "http://my.netscape.com/publish/formats/rss-0.91.dtd">';
		$xml[] = '<rss version="0.91">';
		$xml[] = '<channel>';
		$xml[] = "<title>{$this->title}</title>";
		$xml[] = "<link>{$this->link}</link>";
		$xml[] = "<description>{$this->description}</description>";
		$xml[] = '</channel>';

		foreach ($this->items as $item)
		{
			$xml[] = $item->get_output();
		}
		
		$xml[] = '</rss>';
		return join("\r\n", $xml);
	}
}

?>