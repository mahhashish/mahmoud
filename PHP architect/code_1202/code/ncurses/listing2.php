#!/usr/local/bin/php

<?php

error_reporting(E_ALL);

// Initialize ncurses

if (ncurses_init())
	die ("Unable to initialize ncurses!");


// Get dimensions of the screen

ncurses_getmaxyx (STDSCR, $screen_height, $screen_width);

// Create a window and center it

$win1 = ncurses_newwin (20, 50, ($screen_height - 20) / 2, ($screen_width - 50) / 2);

// Clear screen

ncurses_erase();

// Clear both windows

ncurses_wclear ($win1);

// Create a border for our window

ncurses_wborder ($win1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

// Type something in the window

$output_message = "This screen is full of garbage!";

ncurses_mvwaddstr ($win1, 9, (50 - strlen ($output_message)) / 2, "This screen is full of garbage!");

// Fill the screen with lots of text

for ($i = 0; $i < 3000; $i++)
	ncurses_addstr ($i);

// Refresh the screen

ncurses_refresh();

// Wait a while

sleep (2);

// Refresh the window, thus making it visible

ncurses_wrefresh ($win1);

// Wait a while

sleep (2);

// Destroy the window

ncurses_delwin ($win1);

// Exit

ncurses_end();

?>
