<?

/***
//
//		PDF Distiller
//		By Marco Tabini
//
//		From the December, 2002 Issue of php|architect
//
//		Copyright (c) Marco Tabini and Associates, Inc.
//
/***/

// Turn on error reporting

error_reporting (E_ALL);

// Creates a simple HTML page with a message and aborts the script
// We use this function for reporting errors to the user

function print_message ($s)
{
	die ('<html><body><h1>Warning!</h1>' . $s . '</body></html>');
}

// Check that a file was uploaded

if (!count ($_FILES) || $_FILES['infile']['error'] || strcmp ($_FILES['infile']['type'], 'application/postscript'))
	print_message ('No file uploaded or upload error!');

// Try running it through Ghostscript

exec ('gs -sDEVICE=pdfwrite -r300 -sOutputFile=' . $_FILES['infile']['tmp_name'] . '.pdf -dNOPAUSE -dBATCH ' . $_FILES['infile']['tmp_name'], $a, $n);

if ($n)
	print_message ('Unable to convert file. Please ensure that you have used the proper format and try again.');

// First, output headers that tell the browser the type of the file
// we're outputting, how long it is and how we want it displayed
// The Content-Disposition header also allows us to specify a filename

header ('Content-Type: application/pdf');
header ('Content-Disposition: attachment; filename="' . $_FILES['infile']['name'] . '.pdf"');
header ('Content-Length: ' . filesize ($_FILES['infile']['tmp_name'] . '.pdf'));

// Dump the PDF file and delete it from the PHP folder
readfile ($_FILES['infile']['tmp_name'] . '.pdf');
unlink ($_FILES['infile']['tmp_name'] . '.pdf');
?>
