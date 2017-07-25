<?
         /* --- FIRST PART  --- */

    /* We set some vars that an user could have sent us */
    /* If this was a real application they would be cointaned in $_POST */
$name = "John Smith";
$address = "1, This Street";
$city = "Toronto";
$country = "Canada";
$creditcardno = "123456789-ABC";
$title = "How to use PHP printer functions";
$price = "10.00 USD";
$isbn = "12-345-6789-X";

    /* Now we write the text to print */
$text  = "\t\t\tBOOK ORDER";  // We can use tabulation special char
$text .= "\n\r\n\r\n\r\n\r\n\r";  // And new line ones
$text .= "We received this order ".date("l, M dS Y")." at ".date("H:i:s")."\n\r\n\r";
$text .= "From $name\n\r\n\r";
$text .= "Address: $address  -  $city, $country\n\r\n\r";
$text .= "Credit card number: $creditcardno\n\r\n\r\n\r\n\r";
$text .= "The book ordered is the following:\n\r\n\r";
$text .= "Title: $title\n\r";
$text .= "ISBN code: $isbn\n\r\n\r";
$text .= "Price: $price";

    /* And we print it */
$handle = printer_open();  // Opens the connection
printer_set_option($handle, PRINTER_MODE, "RAW");  // Sets the printer mode

printer_start_doc($handle, "Book order");  // Creates the document and sets its title
printer_start_page($handle);  // Starts the page. This function is only needed with modern printers.

printer_write($handle, $text); // Prints the text

printer_end_page($handle);  // Ends the page. This function is only needed with modern printers.
printer_end_doc($handle);  // Closes the document
printer_close($handle);  // Closes the connection

?>
