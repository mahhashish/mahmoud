#!/usr/local/bin/php -q 

<?php

// Global variables

$file_data = '';	// Main file buffer
$cursor_pos = 0;	// Current location of cursor within current row
$cursor_row = 0;	// Current location of cursor within file
$top_pos = 0;		// Current location of screen(0,0) within file
$top_pos_row = 0;	// Current row location of screen(0,0) within file
$screen_width = 0;	// Screen width
$screen_height = 0;	// Screen height

$cur_x = 0;		// Cursor x position
$cur_y = 0;		// Cursor y position

// Global error handler
// Allows us a graceful out for untrapped errors

function global_error_handler ($errno, $errstr, $errfile, $errline)
{
	ncurses_end();
	echo "ERROR: $errstr on line $errline\n";
}

// Dies safely--ending the nCurses library
// so that the terminal will still work

function try_or_die ($value)
{
	if ($value)
	{
		ncurses_end();
		die ("Internal nCurses error\n");
	}
}

// Prints status information

function print_status()
{
	global $screen_height;
	global $cursor_pos;
	global $cursor_row;
	global $argv;
	
	ncurses_color_set (1);
	ncurses_move ($screen_height, 0);
	ncurses_addstr ("Line: " . str_pad (number_format ($cursor_row), 6, ' ', STR_PAD_LEFT) . " - File name: " . $argv[1]);
	ncurses_color_set (2);
}

// Performs initialization procedure

function init_me()
{
	global $screen_width;
	global $screen_height;

	// Set the error handler

	set_error_handler ("global_error_handler");

	// Init the nCurses library

	try_or_die (ncurses_init());

	// Set noecho mode

	try_or_die (ncurses_noecho());

	// Clear the screen

	try_or_die (ncurses_erase());

	// Get screen extent

	try_or_die (ncurses_getmaxyx (STDSCR, $screen_height, $screen_width));

	// Save the last row for status information

	$screen_height--;

	if (ncurses_has_colors())
	{
		// Init color pair for the status line
		// and for the normal background

		ncurses_start_color();
		ncurses_init_pair (1, NCURSES_COLOR_BLUE, NCURSES_COLOR_CYAN);
		ncurses_init_pair (2, NCURSES_COLOR_CYAN, NCURSES_COLOR_BLACK);
		
		// Create status line

		ncurses_color_set(1);
		ncurses_move ($screen_height, 0);
		ncurses_addstr (str_repeat (' ', $screen_width));
		print_status();

		// Use the default colors now

		ncurses_color_set (2);
	}
}

// Verifies that the x and y coordinates of the cursor are not out of bounds
// If they are, scroll as appropriate

function check_coords()
{
	global $file_data;
	global $top_pos;
	global $top_pos_row;
	global $screen_width;
	global $screen_height;
	global $cur_x;
	global $cur_y;
	global $cursor_pos;
	global $cursor_row;

	// Has the cursor exceeded the current screen?

	if ($cur_y >= $screen_height)
	{
		$diff = $cur_y - $screen_height + 1;

		// Move down as many lines as needed

		while ($diff)
		{
			$top_pos += $screen_width;
			if ($top_pos > strlen ($file_data[$top_pos_row]))
			{
				$top_pos = 0;
				$top_pos_row++;
			}

			// If moved beyond end of file, stop scrolling and beep

			if ($top_pos_row < count ($file_data))
				$diff--;
			else
			{
				$top_pos_row = count ($file_data) - $screen_height;
				$diff = 0;
				ncurses_beep();
			}
		}
		
		// Update y-coord and redraw screen

		$cur_y = $screen_height - 1;
		update_screen();
	}

	// Scrolling up?

	if ($cur_y < 0)
	{
		$diff = -$cur_y;

		// Scroll as many lines as needed

		while ($diff)
		{
			$top_pos -= $screen_width;
			if ($top_pos < 0)
			{
				$top_pos_row--;
				$top_pos = ($top_pos_row >= 0 ? strlen ($file_data[$top_pos_row]) : 0);
			}

			// If we've reached the beginning of the file, stop scrolling

			if ($top_pos_row >= 0)
				$diff--;
			else
			{
				$top_pos_row = 0;
				$top_pos = 0;
				$diff = 0;
				ncurses_beep();
			}

			$cursor_pos -= $screen_width;
		}

		// Update variables & redraw screen

		$cursor_row = $top_pos_row;
		$cursor_pos = $top_pos;

		$cur_y = 0;
		update_screen();
	}

	// Print status at bottom of screen
	// and move cursor to appropriate spot

	print_status();
	ncurses_move ($cur_y, $cur_x);
}

// Scroll up or down $lines lines

function scroll ($lines)
{
	global $file_data;
	global $screen_width;
	global $cur_y;
	global $cursor_pos;
	global $cursor_row;

	$diff = $lines;

	if ($diff > 0)
	{
		// Scrolling down. Increase the cursor position
		// as needed.

		while ($diff)
		{
			$cursor_pos += $screen_width;
			if ($cursor_pos > strlen ($file_data[$cursor_row]))
			{
				$cursor_pos = 0;
				$cursor_row++;
			}

			// If we're beyond the end of the file,
			// stop scrolling and beep.

			if ($cursor_row >= count ($file_data))
			{
				$cursor_row = count ($file_data) - 1;
				$lines -= $diff;
				$diff = 0;
				ncurses_beep();
			}
			else
				$diff--;
		}
	}
	else
	{
		// Scrolling up. Decrease the cursor position
		// as needed.

		while ($diff)
		{
			$cursor_pos -= $screen_width;
			if ($cursor_pos < 0)
			{
				$cursor_row--;
				$cursor_pos = ($cursor_row >= 0 ? strlen ($file_data[$cursor_row]) - 1 : 0);
			}

			// If we're at the beginning of the file,
			// stop scrolling and beep.

			if ($cursor_row < 0)
			{
				$cursor_pos = $cursor_row = 0;
				$diff = 0;
			}
			else
				$diff++;
		}
	}

	// Update y and force coordinate
	// check (which includes redrawing
	// the screen).

	$cur_y += $lines;
	check_coords();	
}

// Updates the screen based on the current y location

function update_screen()
{
	global $file_data;
	global $top_pos;
	global $top_pos_row;
	global $screen_width;
	global $screen_height;
	global $cur_x;
	global $cur_y;

	$i = 0;

	$current_row = $top_pos_row;
	$pos = $top_pos;

	// Start refreshing from the beginning of the
	// screen

	ncurses_move (0,0);

	while ($current_row < count ($file_data) && $i < $screen_height)
	{
		// Print out each row.

		while ($i < $screen_height && $pos < strlen ($file_data[$current_row]))
		{
			ncurses_addstr (substr ($file_data[$current_row], $pos, $screen_width));
			$pos += $screen_width;
			$i++;
		}
		$current_row++;
		$pos = 0;
	}

	// Move the cursor to the current position

	ncurses_move ($cur_y, $cur_x);
}

// Load file in buffer

if ($argc != 2)
	die ("Usage: editor.php filename\n");

if (!file_exists ($argv[1]))
	die ("File $argv[1] doesn't exist\n");

$file_data = file ($argv[1]);

// Init program

init_me();

// Show file

update_screen();

// Keep looping
// F10 exits from the program

while (true)
{
	$input = ncurses_getch();

	switch ($input)
	{
		case	NCURSES_KEY_F10	:

			ncurses_end();
			exit;

			break;

		case	NCURSES_KEY_DOWN :
	
			scroll (1);
			check_coords();

			break;

		case	NCURSES_KEY_NPAGE :

			scroll ($screen_height);
			check_coords();

			break;

		case	NCURSES_KEY_UP:

			scroll (-1);
			check_coords();

			break;

		case	NCURSES_KEY_PPAGE :

			scroll (-$screen_height);
			check_coords();

			break;

		default:

			echo $input;

			break;
	}
}

?>
