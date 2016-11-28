<?php namespace app\lib\Support;

class Str extends \Illuminate\Support\Str {

	//Gets a feed info, parses it and returns it
	public static function parse_feed($url) {
		//First, we get our well-formatted external feed
		$feed = simplexml_load_file($url);

		//if cannot be found, or a parse/syntax error occurs, we return a blank array
		if(!count($feed)) {
			return array();
		} else {
			//If found, we return the newest five <item>s in the <channel>
			$out = array();
			$items = $feed->channel->item;

			for($i=0;$i<5;$i++) {
				$out[] = $items[$i];
			}
			//and we return the output
			return $out;
		}
	}
}