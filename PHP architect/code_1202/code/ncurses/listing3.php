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
	ncurses_init_pair (2, NCURSES_COLOR_RED, NCURSES_COLOR_BLACK);
}

// Clear screen

ncurses_erase();

// Print out something using colour pair 1

ncurses_color_set (1);
ncurses_addstr ("Hello World!\n");

// Print out using colour pair 2 -- and make it bold

ncurses_color_set (2);
ncurses_addstr ("This is red\n");
ncurses_attron (NCURSES_A_BOLD);
ncurses_addstr ("This is red and bold\n");

// Try out some other effects

ncurses_attroff (NCURSES_A_BOLD);
ncurses_attron (NCURSES_A_UNDERLINE);
ncurses_addstr ("Here's some underlined text\n");

ncurses_attron (NCURSES_A_BLINK);
ncurses_addstr ("Underlined and blinking\n");

// Refresh the screen

ncurses_refresh();

// Wait a while

sleep (4);

// Exit

ncurses_end();

?>
