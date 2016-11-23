<?php

/*
* This is a snipit of the Volunteers module developed to satisfy a 
* specific requirement for Diysearch that was not covered by PHPNuke's
* core set of modules
*/

// This is typically the first line in most modules for security reasons
if (!eregi("modules.php", $PHP_SELF)) {
    die ("You can't access this file directly...");
}

// this is essentially the Nuke "kernel" and defines global variables
// and establishes the environment in which Nuke runs.
require_once("mainfile.php");


function entry() {
	OpenTable();
	
	// show form to allow users to signup to be volunteers	

	CloseTable();
}

function add_volunteer () {
	global $dbi;
	
	// do insert of user information	
	
}

// include our theme header
include("header.php");

/*
* Our main operation switch statement which determins which
* function to call
*/
switch ($op) {
	case "add":
		add_volunteer();
		break;
	default:
		entry();
		break;
}

// include our theme footer
include("footer.php");
?>