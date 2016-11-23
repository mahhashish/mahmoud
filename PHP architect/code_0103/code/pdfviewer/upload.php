<?php
if (!isset ($_FILES['userfile']) || $_FILES['userfile']['error'] || !is_uploaded_file ($_FILES['userfile']['tmp_name']))
	die ("File upload error. Please try again.");

session_start();

if (!exec ("pdfinfo {$_REQUEST['userfile']['tmp_name']}", $args))
	die ('Error converting PDF file [possibly encrypted?]');

foreach ($args as $v)
{
	$data = explode (":", $v);
	$args2[trim ($data[0])] = trim ($data[1]);
}

if ($args2['Pages'] > 10)
	die ('This converted only supports a maximum of 2 pages');

$_SESSION['args'] = $args2;
$_SESSION['max_zoom_level'] = 4;	// Maximum zoom level

$tempfilename = tempnam ($_SERVER['DOCUMENT_ROOT'] . '/tmp', 'pdf');
$tempfilename2 = tempnam ($_SERVER['DOCUMENT_ROOT'] . '/tmp', 'pdf');

copy ($_FILES['userfile']['tmp_name'], $tempfilename2);

// Prepare high-res files

shell_exec ("gs -dNOPAUSE -dSAFER -sDEVICE=png16m -r200 -sOutputFile=$tempfilename%ld.png -dBATCH {$_FILES['userfile']['tmp_name']}");

$_SESSION['imgid'] = $tempfilename;
$_SESSION['docid'] = $tempfilename2;

?>
<html>
<frameset cols="100,1*">
<frameset rows="1*, 100">
	<frame src="header.php">
	<frame src="footer.php">
</frameset>
<frame name=downtarget src="view.php?p=1&z1=1">
</frameset>
</html>
