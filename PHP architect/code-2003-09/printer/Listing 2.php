<?

         /* --- SECOND PART  --- */

    /* First we connect to the DB */
$conn = mysql_connect('server', 'username', 'password')
						or die("Could not connect: " . mysql_error());
mysql_select_db('mydbname');
$res = mysql_query("SELECT * FROM products ORDER BY id ASC", $conn);
mysql_close($conn);

    /* Then we set-up printer functions & tools */
$handle = printer_open();
printer_start_doc($handle,'Dumping data from products');
printer_start_page($handle);

$x = printer_get_option($handle, PRINTER_RESOLUTION_X) / 300; // Gets X res
$y = printer_get_option($handle, PRINTER_RESOLUTION_Y) / 300; // And Y res

    /* $bigtitle is an ultrabold big font */
$bigtitle = printer_create_font('Comic Sans MS', 140*$y, 60*$x, PRINTER_FW_ULTRABOLD, false, false, false, 0);
    /* $smalltitle is a bold medium font */
$smalltitle = printer_create_font('Times New Roman', 70*$y, 30*$x, PRINTER_FW_BOLD, false, false, false, 0);
    /* $font is a small font we can use to write into the table */
$font = printer_create_font('Times New Roman', 45*$y, 15*$x, PRINTER_FW_MEDIUM, false, false, false, 0);
    /* $pen in the pen we'll use to draw table borders */
$pen = printer_create_pen(PRINTER_PEN_SOLID, 1, "000000");

    /* Now we print the titles */
printer_set_option($handle, PRINTER_TEXT_COLOR, "FF0000"); // Sets text color
printer_select_font($handle, $bigtitle); // Selects the font

printer_draw_text($handle, "Our avialable products", 50*$x, 50*$y); // Writes the title
printer_delete_font($bigtitle); // Deletes the font

    /* Now we'll repeat the operation */
printer_set_option($handle, PRINTER_TEXT_COLOR, "000000"); // Sets color to black
printer_select_font($handle, $smalltitle);

printer_draw_text($handle, "Backup of table 'products'.", 50*$x, 220*$y);
printer_draw_text($handle, "Date: ".date("r"), 50*$x, 300*$y);
printer_delete_font($smalltitle);

printer_select_pen($handle, $pen); // Selects $pen
printer_select_font($handle, $font); // And $font

$x_pos = array (200*$x,   // We store in this array the X pos
		300*$x,   // Of each column border.
		1540*$x,  // We'll use them to draw table borders. 
		1780*$x,
		1960*$x,
		2060*$x);

$x_txt = array (220*$x,   // We store in this array the X pos
	 	320*$x,   // Of the text in each column.
		1560*$x,  // We'll use them to write text inside table cells
		1800*$x,
		1980*$x);

$y_pos = 400*$y; // Sets Y position
$y_txt = $y_pos + 15*$y; // Sets Y text position

$page = 1; // Sets page number

    /* Writes the header of the table */
printer_draw_line($handle, $x_pos[0], $y_pos, $x_pos[5], $y_pos);

printer_draw_text($handle, "ID",			 $x_txt[0], $y_txt);
printer_draw_text($handle, "PRODUCT NAME / DESCRIPTION", $x_txt[1], $y_txt);
printer_draw_text($handle, "PROD. ID", 			 $x_txt[2], $y_txt);
printer_draw_text($handle, "PRICE", 			 $x_txt[3], $y_txt);
printer_draw_text($handle, "QTY", 			 $x_txt[4], $y_txt);

$y_pos += 80*$y; // Increments $y_pos
$y_txt = $y_pos + 15*$y; // Resets Y text position

while(($row = mysql_fetch_array($res, MYSQL_ASSOC)) !== FALSE)
    {
    	       /* Writes each row */
       printer_draw_line($handle, $x_pos[0], $y_pos, $x_pos[5], $y_pos);
       
       printer_draw_text($handle, $row["id"], 	    $x_txt[0], $y_txt);
       printer_draw_text($handle, $row["prodname"], $x_txt[1], $y_txt);
       printer_draw_text($handle, $row["prodid"],   $x_txt[2], $y_txt);
       printer_draw_text($handle, $row["price"],    $x_txt[3], $y_txt);
       printer_draw_text($handle, $row["qty"],      $x_txt[4], $y_txt);
       
       $y_pos += 80*$y; // Increments $y_pos
       $y_txt = $y_pos + 15*$y; // Resets Y text position
       
       if ($y_pos >= 3200*$y) // If reaches the end of the page
            {
               printer_draw_line($handle, $x_pos[0], $y_pos, $x_pos[5], $y_pos); // Closes the table
               printer_draw_text($handle, "Page $page", 2080*$x, 40*$y); // Writes page number
               
               ($page == 1)? $y_start=400*$y : $y_start=50*$y; // If this is the 1st page vertical lines
               						       // starts from 400, else from 50

                     /* Draws verical lines */
               printer_draw_line($handle, $x_pos[0], $y_start, $x_pos[0], $y_pos);
               printer_draw_line($handle, $x_pos[1], $y_start, $x_pos[1], $y_pos);
               printer_draw_line($handle, $x_pos[2], $y_start, $x_pos[2], $y_pos);
               printer_draw_line($handle, $x_pos[3], $y_start, $x_pos[3], $y_pos);
               printer_draw_line($handle, $x_pos[4], $y_start, $x_pos[4], $y_pos);
               printer_draw_line($handle, $x_pos[5], $y_start, $x_pos[5], $y_pos);

               printer_end_page($handle); // Closes this page
               printer_start_page($handle); // And starts a new one
               printer_select_font($handle, $font); // Re-selects $font

               $y_pos = 50*$y; // Resets $y_pos
               $y_txt = $y_pos + 15*$y; // And Y text position
               $page++; // Increments page number
               
               
                  /* Writes the header of the table in the new page */
               printer_draw_line($handle, $x_pos[0], $y_pos, $x_pos[5], $y_pos);
               
               printer_draw_text($handle, "ID",				 $x_txt[0], $y_txt);
	       printer_draw_text($handle, "PRODUCT NAME / DESCRIPTION",  $x_txt[1], $y_txt);
	       printer_draw_text($handle, "PROD. ID", 			 $x_txt[2], $y_txt);
	       printer_draw_text($handle, "PRICE", 			 $x_txt[3], $y_txt);
	       printer_draw_text($handle, "QTY", 			 $x_txt[4], $y_txt);
	       
	       $y_pos += 80*$y; // Increments $y_pos
	       $y_txt = $y_pos + 15*$y; // Resets Y text position
            }       
    }

     /* When exits the while loop */
printer_draw_line($handle, $x_pos[0], $y_pos, $x_pos[5], $y_pos); // Closes the table
printer_draw_text($handle, "Page $page", 2080*$x, 40*$y); // Writes page number
               
($page == 1)? $y_start=400*$y : $y_start=50*$y; // If the document has only one page
						// starts from 400,else from 50

      /* Draws verical lines */
printer_draw_line($handle, $x_pos[0], $y_start, $x_pos[0], $y_pos);
printer_draw_line($handle, $x_pos[1], $y_start, $x_pos[1], $y_pos);
printer_draw_line($handle, $x_pos[2], $y_start, $x_pos[2], $y_pos);
printer_draw_line($handle, $x_pos[3], $y_start, $x_pos[3], $y_pos);
printer_draw_line($handle, $x_pos[4], $y_start, $x_pos[4], $y_pos);
printer_draw_line($handle, $x_pos[5], $y_start, $x_pos[5], $y_pos);

printer_delete_pen($pen);  // Deletes $pen
printer_delete_font($font); // And $font
printer_end_page($handle); // Closes the last page
printer_end_doc($handle); // Closes the document
printer_close($handle); // Closes the connection
?>
