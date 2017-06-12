<?php

// session 1
$url = 'ftp://www.shaman.ca/ftp/';
$user = 'petej';
$pass = 'password';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
curl_setopt($ch, CURLOPT_FTPLISTONLY, 1);
$output = curl_exec($ch);
if (curl_errno($ch) != 0) {
    $output = 'Error: ' . curl_error($ch);
}
curl_close($ch);

print "CURL OUTPUT:\n{$output}\n\n";

// session 2
$infile = 'listing10.php';
$destination = $infile;
$ch = curl_init("{$url}{$destination}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_UPLOAD, 1);
if (!$fh = fopen($infile, 'r')) {
    curl_close($ch);
    die("Error: unable to open {$infile} for reading\n");
}
curl_setopt($ch, CURLOPT_INFILE, $fh);
$output = curl_exec($ch);
$output = "File {$infile} was successfully uploaded";
if (curl_errno($ch) != 0) {
    $output = 'Error: ' . curl_error($ch);
}
fclose($fh);
curl_close($ch);

print "CURL OUTPUT:\n{$output}\n\n";

// session 3
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
curl_setopt($ch, CURLOPT_FTPLISTONLY, 1);
curl_setopt($ch, CURLOPT_POSTQUOTE, array("DELE {$destination}"));
$output = curl_exec($ch);
if (curl_errno($ch) != 0) {
    $output = 'Error: ' . curl_error($ch);
}
curl_close($ch);

print "CURL OUTPUT:\n{$output}\n\n";

// session 4
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
curl_setopt($ch, CURLOPT_FTPLISTONLY, 1);
$output = curl_exec($ch);
if (curl_errno($ch) != 0) {
    $output = 'Error: ' . curl_error($ch);
}
curl_close($ch);

print "CURL OUTPUT:\n{$output}\n\n";
?>