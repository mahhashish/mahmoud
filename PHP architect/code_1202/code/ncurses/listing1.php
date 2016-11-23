#!/usr/local/bin/php

<?php

error_reporting(E_ALL);

// Initialize ncurses

if (ncurses_init())
	die ("Unable to initialize ncurses!");

// Initialize colour, if available

if (ncurses_has_colors())
{
	// Initialize a simple colour pair
	// And select it

	ncurses_start_color();

	ncurses_init_pair (1, NCURSES_COLOR_RED, NCURSES_COLOR_CYAN);
	ncurses_color_set (1);
}

// Clear screen

ncurses_erase();

// Print out something

ncurses_addstr ('Hello World!');

// Refresh the screen

ncurses_refresh();

// Wait a while

sleep (4);

// Exit

ncurses_end();

?>
