<html>
<head>
<title>Search Results</title>
</head>
<style>
td {
	font-family:		Verdana,Arial;
	font-size:      	11px;
	color:          	#100EB3;
	background-color:	#A0BFE5;
}
</style>
<body bgcolor='#FFFFFF'>
<table width=450 border=0 cellpadding=2 cellspacing=1>
<?php

// Include the searching class
// Make sure that the . path is included in your include_path
// variable of your php.ini file.
include("./Search.inc");

// The amount of results to return
define("INCR", 	30);

// A search function to fascilitate Next results
function search($search_for, $start, $increment) {
	$results = new Search($search_for, $start, $increment);
	if ( $results->count > 0 ) {
		foreach ( $results->res as $result ) {
			echo "<tr><td valign=top>". $result['author'] ."</td>\n";
			echo "<td>". substr($result['story'], 0, 128) ." ...</td></tr>\n";
		}
		$new_start = $start + $increment;
		if ( $results->count >= $new_start ) {
			$encode_search = urlencode($search_for);
			$next = "<a href='search.php?function=search&search_for=$encode_search&start=$new_start&increment=". INCR ."'>Next results ....</a>\n";
		} else {	
			$next = '';
		}
	} else {
		echo "<tr><td>No articles where found</td></tr>";
	}
	echo "<tr><td colspan=2>$next</td></tr>\n";
}

if ( $_GET['function'] == 'search' ) {
	search($_GET['search_for'], $_GET['start'], INCR);
}

?>
</table>
</body>
</html>
