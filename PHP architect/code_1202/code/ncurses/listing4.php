#!/usr/local/bin/php -q

<?php

// Create a custom error handler

function my_error_handler ($errno, $errstr, $errfile, $errline)
{
	ncurses_end();

	echo "$errstr on line $errline\n\n";

	exit(0);
}

set_error_handler ('my_error_handler');

// Centers a line on the given window

function center_window ($win, $y, $text)
{
	ncurses_getmaxyx ($win, $height, $width);
	ncurses_mvwaddstr ($win, $y, ($width - strlen ($text)) / 2, $text);
}

// Creates a message box

function msgbox ($text)
{
	// Get size of screen
	
	ncurses_getmaxyx (STDSCR, $height, $width);

	// Determine size of message box

	$win_w = ($width - 6);
	$win_h = ($height - 6);

	if ($win_w > (strlen ($text) + 4))
		$win_w = strlen ($text) + 4;
	else
		$text = wordwrap ($text, $win_w - 4);

	$string_data = explode ("\n", $text);

	if ($win_h > (count ($string_data) + 4))
		$win_h = count ($string_data) + 4;

	// Create a new window
	
	$win = ncurses_newwin ($win_h, $win_w, ($height - $win_h) / 2, ($width - $win_w) / 2); 

	// Apply a simple border
	
	ncurses_wborder ($win, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

	// Type all the information
	
	for ($i = 0; $i < min ($win_h - 4, count ($string_data)); $i++)
		ncurses_mvwaddstr ($win, $i + 2, ($win_w - strlen ($string_data[$i])) / 2, $string_data[$i]);

	// Refresh window
	
	ncurses_wrefresh ($win);

	// Wait for input
	
	ncurses_getch();

	// Destroy window
	
	ncurses_delwin ($win);
}

// Creates the main menu

function main_menu()
{
	// Clear screen

	ncurses_erase();

	// Create main menu

	ncurses_color_set (2);
	ncurses_attron (NCURSES_A_BOLD);
	center_window (STDSCR, 2, "Main Menu");
	center_window (STDSCR, 3, "---------");
	ncurses_attroff (NCURSES_A_BOLD);

	center_window (STDSCR, 4, '(1) Stop Apache');
	center_window (STDSCR, 5, '(2) Start Apache');
	center_window (STDSCR, 6, '(3) Restart Apache');
	center_window (STDSCR, 8, '(Q) Quit');

	ncurses_refresh();
}

// Initialize ncurses

if (ncurses_init())
	die ("Unable to initialize ncurses!");

// Initialize colour, if available

if (ncurses_has_colors())
{
	// Initialize a simple colour pair
	// And select it

	ncurses_start_color();

	ncurses_init_pair (1, NCURSES_COLOR_CYAN, NCURSES_COLOR_BLACK);
	ncurses_init_pair (2, NCURSES_COLOR_RED, NCURSES_COLOR_BLACK);
}

// Turn off echo

ncurses_noecho();

// Create main menu

main_menu();

while (true)
{
	$c = ncurses_getch();
	$s = chr ($c);

	switch ($s)
	{
		case '1'	:

			msgbox (shell_exec ("apachectl start") . "\n\nPress a key to continue");
			main_menu();

			break;

		case '2'	:
		
			msgbox (shell_exec ("apachectl stop") . "\n\nPress a key to continue");
			main_menu();

			break;

		case '3'	:

			msgbox (shell_exec ("apachectl restart") . "\n\nPress a key to continue");
			main_menu();

			break;

		case 'Q'	:
		case 'q'	:

			ncurses_end();
			exit;

			break;
	}
}

?>
