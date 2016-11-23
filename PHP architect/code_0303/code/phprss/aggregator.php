<?php

$feeds = array
(
	'http://blogs.phparch.com/b2rss.xml',
	'http://www.phparch.com/phpa.rss'
);

// Classes used internally to parse the XML
// data

class CItem
{
	var $title;
	var $description;
	var $url;
}

class CFeed
{
	var $title;
	var $url;

	var $items;

	var $currentitem;
}

// XML handlers

function ElementStarter($parser, $name, $attrs) 
{
    global $currentelement;
	 global $elements;

	 $elements[$currentelement ++] = $name;
}

function ElementEnder($parser, $name) 
{
	global $elements;
   global $currentelement;
	global $currentfeed;

	if ($name == 'ITEM')
	{
		$currentfeed->items[] = $currentfeed->currentitem;
		$currentfeed->currentitem = new CItem;
	}

	$currentelement--;
}

function DataHandler ($parser, $data)
{
	global $elements;
   global $currentelement;
	global $currentfeed;

	switch ($elements[$currentelement - 1])
	{
		case	'TITLE'	:

			if ($elements[$currentelement - 2] == 'ITEM')
				$currentfeed->currentitem->title .= $data;
			else
				$currentfeed->title = $data;

			break;

		case 	'LINK'	:
				
			if ($elements[$currentelement - 2] == 'ITEM')
				$currentfeed->currentitem->url .= $data;
			else
				$currentfeed->url .= $data;

			break;

		case 'DESCRIPTION'	:

			if ($elements[$currentelement - 2] == 'ITEM')
				$currentfeed->currentitem->description .= $data;
			else
				$currentfeed->description .= $data;

			break;
	}
}

// Feed loading function

function get_feed ($location)
{
	global $elements;
	global $currentelement;
	global $currentfeed;

	$xml_parser = xml_parser_create();

	$elements = array();
	$currentelement = 0;
	$currentfeed = new CFeed;
	$currentfeed->currentitem = new CItem;

	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
	xml_set_element_handler($xml_parser, "ElementStarter", "ElementEnder");
	xml_set_character_data_handler($xml_parser, "DataHandler");

	if (!($fp = fopen($location, "r"))) 
		return 'Unable to open location';

	while ($data = fread($fp, 4096)) 
	{
		 if (!xml_parse($xml_parser, $data, feof($fp))) 
		 	return 'XML PARSE ERROR';
	}
	xml_parser_free($xml_parser);

	return $currentfeed;
}

// Feed formatting function

function format_feed ($feed, $url)
{

	if (!is_object ($feed))
	{
	?>

		<p>
		<b>Unable to load feed at <a href="<?= $url ?>"?>
		<?= htmlentities($url) ?></a></b></p>

	<?php
	}
	else
	{
		?>

		<h1><a href="<?= $feed->url ?>">
		<?= $feed->title ?></a></h1>
		<p />

		<?php

		foreach ($feed->items as $item)
		{
		?>

			<h2><a href="<?= $item->url ?>">
			<?= htmlentities ($item->title) ?></a></h2>
			<div width=500>
			<?= htmlentities ($item->description) ?>
			<hr>
			</div>

		<?php
		}
	}
}
?>
<html>
<head>
<title>RSS Feed</title>
</head>
<body>
<?
	foreach ($feeds as $feed)
		format_feed (get_feed ($feed), $feed);
?>
</body>
</html>
