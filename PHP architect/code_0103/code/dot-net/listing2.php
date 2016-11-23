<?php
// Create COM object for Microsoft Word
$myword = new COM("word.application") or die("Cannnot Open Microsoft Word"); 
// Display Microsoft Word Version 
echo "We have loaded Version $myword->Version of Microsoft Word<BR>"; 
// Open Microsoft Word 
$myword->Visible = 1; 
?>
