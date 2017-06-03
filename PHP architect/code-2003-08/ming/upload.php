<?php

// File upload
// Process and verify file
 if ($_FILES['userfile']=="none") {
	echo ("No File Uploaded");
	exit;
}

if ($_FILES['userfile']['size']==0) {
	echo ("No File Uploaded... File size = 0");
	exit;
}

if ($_FILES['userfile']['type'] != "audio/mpeg") {
	echo ("File is not an MP3");
	exit;
} 


$uploaddir = "D:\webstuff\ming\\";	//<-- change this depending on your system


if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir . $_FILES['userfile']['name'])) {
    print "File is valid, and was successfully uploaded.  Processing:\n";
    
	$mp3Filename = $uploaddir . $_FILES['userfile']['name'];

	/* -=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-
		Grab Header Information and MP3 Info
	/-=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-*/

	// include the class to read MP3 header and ID3v1 tags
	include("mp3.inc.php");

	$info = mp3info($mp3Filename);
	$mp3Length=round($info['time']);
	$mp3Title=trim($info['tagtitle']);
	$mp3Artist=trim($info['tagartist']);
	$mp3Album=trim($info['tagalbum']);
	$mp3Year=trim($info['tagyear']);
	$mp3Comment=trim($info['tagcomment']);

	//echo out the ID3v1 info
	echo "<br />Name - ". trim($_FILES['userfile']['name']);
	echo "<br />Length (s) - ". $mp3Length;
	echo "<br />Title - ".$mp3Title;
	echo "<br />Artist - ".$mp3Artist;
	echo "<br />Album - ".$mp3Album;
	echo "<br />Year - ".$mp3Year;
	echo "<br />Comment - ".$mp3Comment."<br /><br />";




	/* -=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-
		MySQL stuff here to save ID3v1 info
	/-=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-*/

	$hostname = 'localhost';
	$dbname = 'mp3stream';
	$user = 'root';				//<-- Change depending on your system
	$pwd = '';					//<-- Change depending on your system


	# Connect to database service and get link ID
	$dblink = mysql_connect($hostname, $user, $pwd)
	   or die ("Error: No connection to MySQL server\n");

	# Connect to database
	mysql_select_db($dbname,$dblink)
	   or die ("Error: MySQL database not selected\n");

	# Set the SQL statement
	$safe_mp3Filename = addslashes($mp3Filename);
	$sql = "INSERT INTO mp3info (mp3ID, mp3Filename, mp3Length,mp3Title,mp3Artist,mp3Album,mp3Year,mp3Comment,mp3Genre) VALUES ('','$safe_mp3Filename', '$mp3Length', '$mp3Title', '$mp3Artist', '$mp3Album', '$mp3Year', '$mp3Comment', '')";


	# Send SQL statement
	$result = mysql_query($sql, $dblink)
	   or die ("SQL query failed: $sql");


	   # Close the database link
	   mysql_close($dblink);

	/* -=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-
		MySQL stuff ends here
	-=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-*/



	/* -=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-
		Save as a SWF file
	-=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-*/

	// Creates new movie
	$m = new SWFMovie();

	// SWF Movie rate
	$rate = 12;

	$m->setRate($rate);
	$m->setFrames(($mp3Length*$rate)+3);

	// Stops the movie so that sound won't play at startup
	$m->add(new SWFAction("stop();"));


	// Stream the MP3 here
	$m->streamMp3(fopen("$mp3Filename", "rb"));
	
	$mp3Filename=$mp3Filename.".swf";
	 
	// Saves it
	$m->save("$mp3Filename");

	 // add lets the end user know everything was copasetic
	echo ("<br />MP3 File Processed into SWF successfully");



	
} else {
    print "Possible file upload attack!  Here's some debugging info:\n";
    print_r($_FILES);
}





?>