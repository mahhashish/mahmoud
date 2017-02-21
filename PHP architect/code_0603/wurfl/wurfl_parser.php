<?php
/*
 *
 * This is a VERY simple PHP script to demonstrate how you could parse the WURFL
 * and have an associative array. Once you have the array you can obtain all the
 * data you need. You will certanly need some filters and take in consideration,
 * mainly, the fall_back feature. It's not natively implemented here, that is to
 * say, if you don't know a phone's feature and it's not listed in its
 * characteristics, you (read "your software") will need to search in its parent.
 *
 * In order to let this parser work you will NEED "wurfl.xml" in the same
 * directory as the parser, otherwise you will need to change WURFL_FILE define.
 *
 * To speed up I also implemented a simple caching system, I serialize $wurfl
 * and the mtime of wurfl.xml. Then I check that mtime with wurfl's mtime, if
 * they are different I update the cache.
 * NOTE: if wurfl's mtime is older than the one stored in the cache file I
 * will not update the cache.
 * WURFL_USE_CACHE and CACHE_FILE need to be changed depending on what you want
 * to do
 *
 * Questions or comments can be sent to "Andrea Trasatti" <trasatti@bware.it>
 * Please, support this software, send any suggestion and improvement to me
 * or the mailing list and we will try to keep it updated and make it better
 * every day.
 *
 * This software is open-source and is offered AS IS, I take no responsability
 * of what you do with it or what it might cause to your applications or files
 * or anything else. You are free to use it in any way you like; I will not
 * be liable for anything related to this software.
 * License: GPL, please read the full detail on the GNU site
 *	(http://www.gnu.org/copyleft/gpl.html)
 *
 * If you like it and use it, please let me know or contact the wmlprogramming
 * mailing list: wmlprogramming@yahoogroups.com
 *
 */

// Set this true if you want to use cache. Strongly suggested
if ( !defined("WURFL_USE_CACHE") ) {
	define ("WURFL_USE_CACHE", true);
}
// Set path and name of the cache file
if ( !defined("CACHE_FILE") ) {
	define ("CACHE_FILE", "./cache.php");
}
// set path and name of wurfl file
if ( !defined("WURFL_FILE") ) {
	define ("WURFL_FILE", "./wurfl.xml");
}
$wurfl = array();
$wurfl_agents = array();

function startElement($parser, $name, $attr) {
	global $wurfl, $curr_event, $curr_device, $curr_group, $fp_cache;

	switch($name) {
		case "ver":
		case "last_updated":
		case "official_url":
		case "statement":
			//cdata will take care of these, I'm just defining the array
			$wurfl[$name]="";
			break;
		case "maintainers":
		case "maintainer":
		case "authors":
		case "author":
		case "contributors":
		case "contributor":
			if ( sizeof($attr) > 0 ) {
				// dirty trick: author is child of authors, contributor is child of contributors
				while ($t = each($attr)) {
					// example: $wurfl["authors"]["author"]["name"]="Andrea Trasatti";
					$wurfl[$name."s"][$name][$attr["name"]][$t[0]]=$t[1];
				}
			}
			break;
		case "device":
			if ( ($attr["user_agent"] == "" || ! $attr["user_agent"]) && $attr["id"]!="generic" ) {
				die("No user agent and I am not generic!! id=".$attr["id"]." HELP");
			}
			if ( sizeof($attr) > 0 ) {
				while ($t = each($attr)) {
					// example: $wurfl["devices"]["ericsson_generic"]["fall_back"]="generic";
					$wurfl["devices"][$attr["id"]][$t[0]]=$t[1];
				}
			}
			$curr_device=$attr["id"];
			break;
		case "group":
			$curr_group=$attr["id"];
			break;
		case "capability":
			if ( $attr["value"] == 'true' ) {
				$value = true;
			} else if ( $attr["value"] == 'false' ) {
				$value =  false;
			} else {
				$value = $attr["value"];
			}
			$wurfl["devices"][$curr_device][$curr_group][$attr["name"]]=$value;
			break;
		case "devices":
			// This might look useless but it's good when you want to parse only the devices and skip the rest
			$wurfl["devices"]=array();
			break;
		case "wurfl":
			// wurfl is not an event, it's just the name of the project
			break;
		case "default":
			// unknown events are not welcome
			die($name." is an unknown event<br>");
			break;
	}
}


function endElement($parser, $name) {
	global $wurfl, $curr_event, $curr_device, $curr_group;
	switch ($name) {
		case "group":
			break;
		case "device":
			break;
		case "ver":
		case "last_updated":
		case "official_url":
		case "statement":
			$wurfl[$name]=$curr_event;
			// referring to $GLOBALS to unset curr_event because unset will not destroy 
			// a global variable unless called in this way
			unset($GLOBALS['curr_event']);
			break;
		default:
			break;
	}

}

function characterData($parser, $data) {
	global $curr_event;
	if (trim($data) != "" ) {
		$curr_event.=$data;
		//echo "data=".$data."<br>\n";
	}
}

if ( WURFL_USE_CACHE && file_exists(CACHE_FILE) ) {
	include(CACHE_FILE);
}

$wurfl_stat = filemtime(WURFL_FILE);
if ($wurfl_stat <= $cache_stat) {
	// cache file is updated
	;
} else {
	$xml_parser = xml_parser_create();
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "characterData"); 
	if (!($fp = fopen(WURFL_FILE, "r"))) {
	    die("could not open XML input");
	}

	while ($data = fread($fp, 4096)) {
	    if (!xml_parse($xml_parser, $data, feof($fp))) {
		die(sprintf("XML error: %s at line %d",
			    xml_error_string(xml_get_error_code($xml_parser)),
			    xml_get_current_line_number($xml_parser)));
	    }
	}

	xml_parser_free($xml_parser);

	reset($wurfl);
	if ( WURFL_USE_CACHE ) {
		$devices = $wurfl["devices"];
		while ( $x = each ($devices) ) {
			if ( is_string($x[1]['user_agent']) ) {
				$wurfl_agents[$x[1]['user_agent']] = $x[1]['id'];
			}
		}
	}

	reset($wurfl);
	reset($wurfl_agents);
	if ( WURFL_USE_CACHE ) {
		if ( defined("WURFL_AGENT2ID_FILE") && file_exists(WURFL_AGENT2ID_FILE) && !is_writeable(WURFL_AGENT2ID_FILE) ) {
			die ('Unable to remove '.WURFL_AGENT2ID_FILE);
			return;
		}
		$cache_stat = $wurfl_stat;
		$fp_cache= fopen(CACHE_FILE, "w");
		fwrite($fp_cache, "<?php\n");
		$wurfl_to_file = urlencode(serialize($wurfl));
		$wurfl_agents_to_file = urlencode(serialize($wurfl_agents));
		$cache_stat_to_file = urlencode(serialize($cache_stat));
		fwrite($fp_cache, "\$cache_stat=unserialize(urldecode(\"". $cache_stat_to_file ."\"));\n");
		fwrite($fp_cache, "\$wurfl=unserialize(urldecode(\"". $wurfl_to_file ."\"));\n");
		fwrite($fp_cache, "\$wurfl_agents=unserialize(urldecode(\"". $wurfl_agents_to_file ."\"));\n");
		fwrite($fp_cache, "?>\n");
		fclose($fp_cache);
		if ( defined("WURFL_AGENT2ID_FILE") && file_exists(WURFL_AGENT2ID_FILE) ) {
			@unlink(WURFL_AGENT2ID_FILE);
		}
	}

} // end of the main if

?>
