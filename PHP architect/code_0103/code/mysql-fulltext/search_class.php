<?php

define("DATABASE",  'articles');
define("HOST",      'localhost');
define("USERNAME",  'root');
define("PASSWORD",  'root');
$link = mysql_connect(HOST, USERNAME, PASSWORD);
if ( !$link )
	die("Could not connect to the MySQL server");
mysql_select_db(DATABASE, $link);

class Search {
	var $stop	   	= "";
	var $start	  	= "";
	var $incr		= "";
	var $text	   	= "";
	var $res		= array();
	var $count	  	= 0;

	// Class constructor
	function Search($search_for, $start, $incr) {
		$this->start = $start;
		$this->incr = $incr;
		$this->stop = $start + $incr - 1;
		$this->text = $search_for;
		$cache = new Search_cache($this->text);
		if ( $cache->cached() ) {
			list($amount, $sql) = $cache->return_cache($start, $incr);
			$this->cache_search($amount, $sql);
		} else {
			$sql = $this->return_sql($this->text);
			$matched_ids = $this->normal_search($sql);
			if ( !empty($matched_ids) ) {
				$cache->store_cache($matched_ids);
			}
		}
	}

	// Return the correct SQL statement
	function return_sql($text) {
		$sql = '';
		if ( !empty($text) )
			$sql = " MATCH (author, story) against('$text' IN BOOLEAN MODE) ";

		if ( !empty($sql) )
			$query = " where $sql";
		return "select * from article $query order by author";
	}

	// Perform a normal search
	function normal_search($sql) {
		global $link;
		$res = mysql_query($sql, $link);
		while ( $row = mysql_fetch_array($res) ) {
			$this->count++;
			// Only return the amount we need
			if ( $this->count >= $this->start && $this->count <= $this->stop )
				$this->res_add($row);
			$matches[] = $row['id'];
		}
		if ( $this->count < $this->stop )
			$this->stop = $this->count;

		if ( is_array($matches) )
			$match_list = implode(",", $matches);
		else
			$match_list = '';
		return $match_list;
	}

	// Return the cached search results
	function cache_search($amount, $sql) {
		global $link;
		$res = mysql_query($sql, $link);
		while ( $row = mysql_fetch_array($res) ) {
			$this->res_add($row);
			$this->count++;
		}
		$this->count = $amount;
	}

	function res_add(&$row) {
		$this->res[$this->count]["id"] = $row["id"];
		$this->res[$this->count]["author"] = $row["author"];
		$this->res[$this->count]["story"] = $row["story"];
	}
}

// Manages the caching of queries
class Search_cache {
	var $cache_file 	= "";
	var $timeout		= "Ymd H";

	function Search_cache($text) {
		$this->cache_file = '/tmp/'.md5($text.date($this->timeout)).'.cache';
	}

	// Check if a query has been cached
	function cached() {
		if ( is_file($this->cache_file) )
			return true;
		else
			return false;
	}

	// If the query has been cached return the results
	function return_cache($start, $inc) {
		$start--;
		$cached = implode("", (@file($this->cache_file)));
		$arr = explode(",", $cached);
		// Amount of matches found
		$amount = count($arr);
		$result = array_splice($arr, $start, $inc);
		$sql = "select * from article where id IN (". implode(',', $result) .") order by author";
		return array($amount, $sql);
	}

	// Store the cached search ids
	function store_cache($store) {
		$fd = fopen($this->cache_file, "w+");
		fputs($fd, $store, strlen($store));
		fclose($fd);
	}
}

?>
