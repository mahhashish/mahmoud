<?php

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

 Project Name : mySecondRemoting
 Filename :		drawShapes.php
 Written by :	Seth Wilson, P.Eng
 Date :			June 2003

 Description:	

 Revisions:
   1.
   2.
   3.

 Future Upgrades
   1.
   2.
   .


 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */


	$m = new SWFMovie();
	$m->setDimension(320, 240);
	$m->setBackground(255, 255, 255);
	$m->setRate(12);


function getMySQLInfo($sqlIn) {
/* -=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-

	MySQL stuff here to grab and return a  recordset

*/

	$hostname = 'localhost';
	$dbname = 'mp3stream';
	$user = 'root';  //<-- change this based on your system
	$pwd = '';   //<-- change this based on your system



			
	// Connect to database service and get link ID
	$dblink = mysql_connect($hostname, $user, $pwd)
		or die ("Error: No connection to MySQL server\n");

	// Connect to database
	mysql_select_db($dbname,$dblink)
		or die ("Error: MySQL database not selected\n");

	// Set the SQL statement
	$sql = $sqlIn;

	// Send SQL statement
	$result = mysql_query($sql, $dblink)
	or die ("SQL query failed: $sql");

	// return the recordset .... hope you know what you're getting
	// because you are going to have to handle it
	return $result;

	// Close the database link
	mysql_close($dblink);

/* 

	MySQL stuff ends here
-=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-==-=-=-=-
*/
}


// include the drawShape class to make life easier for drawing shapes
	include("drawShapes.php");

// instantiate the drawshapes object
	$shapes = new drawShapes();


//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// Gradients

	$gradX = 325;
	$gradY = 200;


// upper left corner
	$s = new SWFShape();
	$g = new SWFGradient();
	$g->addEntry(0.0, 0, 0, 0, 255);
	$g->addEntry(1.0, 0, 0, 0, 0);

	$f = $s->addFill($g, SWFFILL_RADIAL_GRADIENT);
	$f->scaleTo(0.25);
	$f->moveTo(0, 0);
	$s->setRightFill($f);
	$s->drawLine($gradX, 0);
	$s->drawLine(0, $gradY);
	$s->drawLine((-1*$gradX), 0);
	$s->drawLine(0, (-1*$gradY));

	$m->add($s);

// upper right corner
	$s = new SWFShape();
	$g = new SWFGradient();
	$g->addEntry(0.0, 0, 0, 0, 255);
	$g->addEntry(1.0, 0, 0, 0, 0);

	$f = $s->addFill($g, SWFFILL_RADIAL_GRADIENT);
	$f->scaleTo(0.25);
	$f->moveTo($gradX, 0);
	$s->setRightFill($f);
	$s->drawLine($gradX, 0);
	$s->drawLine(0, $gradY);
	$s->drawLine(-1*$gradX, 0);
	$s->drawLine(0, -1*$gradY);
	
	$m->add($s);

// lower left
	$s = new SWFShape();

	$g = new SWFGradient();
	$g->addEntry(0.0, 0, 0, 0, 255);
	$g->addEntry(1.0, 0, 0, 0, 0);

	$f2 = $s->addFill($g, SWFFILL_RADIAL_GRADIENT);
	$f2->scaleTo(0.25);
	$f2->moveTo(0, $gradY);
	$s->setRightFill($f2);
	$s->drawLine($gradX, 0);
	$s->drawLine(0, $gradY);
	$s->drawLine(-1*$gradX, 0);
	$s->drawLine(0, -1*$gradY);
	
	$m->add($s);

// lower right
	$s = new SWFShape();
	$g = new SWFGradient();
	$g->addEntry(0.0, 0, 0, 0, 255);
	$g->addEntry(1.0, 0, 0, 0, 0);

	$f2 = $s->addFill($g, SWFFILL_RADIAL_GRADIENT);
	$f2->scaleTo(0.25);
	$f2->moveTo($gradX, $gradY);
	$s->setRightFill($f2);
	$s->drawLine($gradX, 0);
	$s->drawLine(0, $gradY);
	$s->drawLine(-1*$gradX, 0);
	$s->drawLine(0, -1*$gradY);

	$m->add($s);







//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Lines and such

	$shapes->setShapeLineStyle(2, 0, 145, 72);
	$shapes->setShapeFillStyle(255,255,255,0);

// draw ovals
	$m->add($shapes->getOvalShape(40,158,12,150));
	$m->add($shapes->getOvalShape(40,40,20,150));


// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Buttons

	$shapes->setShapeLineStyle(3, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);

// Play button	
	
	$x = 40;
	$y = 100;
	$radius = 15;

	$b = new SWFButton(); 
	
	$b->setUp($shapes->getCircleShape($x,$y,$radius));

	$shapes->setShapeFillStyle(0, 145, 72,150);
	$b->setOver($shapes->getCircleShape($x,$y,$radius));
	
	$shapes->setAlpha(50);
	$b->setDown($shapes->getCircleShape($x,$y,$radius));
	$b->setHit($shapes->getCircleShape($x,$y,$radius));



	// Add the actionscript
	$b->addAction(new SWFAction("_root.mp3.play();"), SWFBUTTON_MOUSEUP);

	$b->addAction(new SWFAction("_root.status = 'Play ';"),
				SWFBUTTON_MOUSEDOWN); //BUTTON_MOUSEDOWN

	$b->addAction(new SWFAction("_root.status = 'Play ';"),
				SWFBUTTON_MOUSEOVER); //BUTTON_MOUSEOVER

	$b->addAction(new SWFAction("_root.status = '';"),
				SWFBUTTON_MOUSEOUT);//BUTTON_MOUSEOUT


	$i = $m->add($b);

    //draw a triangle... the universal "Play" symbol

	$height = 12;

	$shapes->setShapeLineStyle(2, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$m->add($shapes->getTriangleShape(($x+8),$y,"right",$height));


 //end of play button


// draw Pause button

	
	$b = new SWFButton(); // Pause button

	$x = 80;

	$shapes->setShapeLineStyle(3, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$b->setUp($shapes->getCircleShape($x,$y,$radius));

	$shapes->setShapeFillStyle(0, 145, 72,150);
	$b->setOver($shapes->getCircleShape($x,$y,$radius));
	
	$shapes->setAlpha(50);
	$b->setDown($shapes->getCircleShape($x,$y,$radius));
	$b->setHit($shapes->getCircleShape($x,$y,$radius));

	$b->addAction(new SWFAction("_root.mp3.stop();"), SWFBUTTON_MOUSEUP);

	$b->addAction(new SWFAction("_root.status = 'Pause';"),
			SWFBUTTON_MOUSEDOWN);

	$b->addAction(new SWFAction("_root.status = 'Pause';"),
			SWFBUTTON_MOUSEOVER);

	$b->addAction(new SWFAction("_root.status = '';"),
			SWFBUTTON_MOUSEOUT);

	$i = $m->add($b);
  
  //draw rectangles

	$shapes->setShapeLineStyle(2, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$m->add($shapes->getRectangleShape(74,94,12,5));
	$m->add($shapes->getRectangleShape(82,94,12,5));

// end of Pause button


  // STOP BUTTON 

	$b = new SWFButton(); // Stop button
	$x = 120;

	$shapes->setShapeLineStyle(3, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$b->setUp($shapes->getCircleShape($x,$y,$radius));

	$shapes->setShapeFillStyle(0, 145, 72,150);
	$b->setOver($shapes->getCircleShape($x,$y,$radius));
	
	$shapes->setAlpha(50);
	$b->setDown($shapes->getCircleShape($x,$y,$radius));
	$b->setHit($shapes->getCircleShape($x,$y,$radius));

	$b->addAction(new SWFAction("_root.mp3.gotoAndStop(1);"), SWFBUTTON_MOUSEUP);

	$b->addAction(new SWFAction("_root.status = 'Stop';"),
		SWFBUTTON_MOUSEDOWN);

	$b->addAction(new SWFAction("_root.status = 'Stop';"),
		SWFBUTTON_MOUSEOVER);

	$b->addAction(new SWFAction("_root.status = '';"),
		SWFBUTTON_MOUSEOUT);

	$i = $m->add($b);
  
  // draw stop rectangle
  
	$shapes->setShapeLineStyle(2, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$m->add($shapes->getRectangleShape(114,94,12,12));

// end of Stop button

// draw Volume Up

	$b = new SWFButton(); 
	
	$x = 170;
	$y = 75;

	$shapes->setShapeLineStyle(2, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$b->setUp($shapes->getTriangleShape($x,75,"up",15));

	$shapes->setShapeFillStyle(0, 145, 72,150);
	$b->setOver($shapes->getTriangleShape($x,$y,"up",15));
	
	$shapes->setAlpha(50);
	$b->setDown($shapes->getTriangleShape($x,$y,"up",15));
	$b->setHit($shapes->getTriangleShape($x,$y,"up",15));



	$b->addAction(new SWFAction("Vol = Vol + 20;	shhh = new Sound();	shhh.setVolume(Vol); _root.status ='Volume = '+ Vol;"),SWFBUTTON_MOUSEUP);

	$b->addAction(new SWFAction("_root.status = 'Volume = '+Vol;"),
		SWFBUTTON_MOUSEDOWN);

	$b->addAction(new SWFAction("_root.status = 'Volume Up';"),
		SWFBUTTON_MOUSEOVER);

	$b->addAction(new SWFAction("_root.status = '';"),
		SWFBUTTON_MOUSEOUT);

	$i = $m->add($b);
// end of Volume up

  
  //Volume Down

	$shapes->setShapeLineStyle(2, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$b = new SWFButton(); 

	$b->setUp($shapes->getTriangleShape(170,120,"down",15));

	$shapes->setShapeFillStyle(0, 145, 72,150);
	$b->setOver($shapes->getTriangleShape(170,120,"down",15));
	
	$shapes->setAlpha(50);
	$b->setDown($shapes->getTriangleShape(170,120,"down",15));
	$b->setHit($shapes->getTriangleShape(170,120,"down",15));

	$b->addAction(new SWFAction("if (Vol > 20) {Vol = Vol - 20;} else {Vol = 0;}	shhh = new Sound();	shhh.setVolume(Vol); _root.status = 'Volume = ' + Vol;"), SWFBUTTON_MOUSEUP);

	$b->addAction(new SWFAction("_root.status = 'Volume = '+Vol;"),
		SWFBUTTON_MOUSEDOWN);

	$b->addAction(new SWFAction("_root.status = 'Volume Down';"),
		SWFBUTTON_MOUSEOVER);

	$b->addAction(new SWFAction("_root.status = '';"),
		SWFBUTTON_MOUSEOUT);

	$i = $m->add($b);

// end of Volume down



// playlist scoll up button
	$b = new SWFButton(); 

	$shapes->setShapeLineStyle(2, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$b->setUp($shapes->getTriangleShape(310,35,"up",10));

	$shapes->setShapeFillStyle(0, 145, 72,150);
	$b->setOver($shapes->getTriangleShape(310,35,"up",10));
	
	$shapes->setAlpha(50);
	$b->setDown($shapes->getTriangleShape(310,35,"up",10));
	$b->setHit($shapes->getTriangleShape(310,35,"up",10));


	$b->addAction(new SWFAction("scrollText.scroll = scrollText.scroll-1;"),SWFBUTTON_MOUSEUP);
	$b->addAction(new SWFAction("_root.status = 'Scroll Up';"),
				SWFBUTTON_MOUSEOVER); //BUTTON_MOUSEOVER

	$b->addAction(new SWFAction("_root.status = '';"),
				SWFBUTTON_MOUSEOUT);//BUTTON_MOUSEOUT

	$i = $m->add($b);
//end of playlist scoll up button


//playlist scroll down button

	$b = new SWFButton(); 

	$shapes->setShapeLineStyle(2, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$b->setUp($shapes->getTriangleShape(310,135,"down",10));

	$shapes->setShapeFillStyle(0, 145, 72,150);
	$b->setOver($shapes->getTriangleShape(310,135,"down",10));
	
	$shapes->setAlpha(50);
	$b->setDown($shapes->getTriangleShape(310,135,"down",10));
	$b->setHit($shapes->getTriangleShape(310,135,"down",10));

	$b->addAction(new SWFAction("scrollText.scroll = scrollText.scroll+1;"),SWFBUTTON_MOUSEUP);
	$b->addAction(new SWFAction("_root.status = 'Scroll Down';"),
				SWFBUTTON_MOUSEOVER); //BUTTON_MOUSEOVER

	$b->addAction(new SWFAction("_root.status = '';"),
				SWFBUTTON_MOUSEOUT);//BUTTON_MOUSEOUT

	$i = $m->add($b);
// end of playlist scroll down button


// Add to playlist button

	$b = new SWFButton(); 

	$shapes->setShapeLineStyle(2, 0, 145, 72);
	$shapes->setShapeFillStyle(12, 255, 133,255);
	$b->setUp($shapes->getOvalShape(225,150,5,55));

	$shapes->setShapeFillStyle(0, 145, 72,150);
	$b->setOver($shapes->getOvalShape(225,150,5,55));
	
	$shapes->setAlpha(50);
	$b->setDown($shapes->getOvalShape(225,150,5,55));
	$b->setHit($shapes->getOvalShape(225,150,5,55));

	$b->addAction(new SWFAction("_root.getURL(\"addplaylist.html\");"),SWFBUTTON_MOUSEUP);
	$b->addAction(new SWFAction("_root.status = 'Add a Song to the Playlist';"),
				SWFBUTTON_MOUSEOVER); //BUTTON_MOUSEOVER

	$b->addAction(new SWFAction("_root.status = '';"),
				SWFBUTTON_MOUSEOUT);//BUTTON_MOUSEOUT

	$i = $m->add($b);

$t = new SWFTextField();
$t->setFont(new SWFFont("_sans"));

$t->addString('Add to Playlist');
$t->setHeight(7);
$t->setBounds(100,10);
$t->setName('add');

$i = $m->add($t);
$i->moveTo(232, 146);


// end of add to playlist button





//  End of Buttons
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-


// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//  TextFields

//status textbox
$t = new SWFTextField();
$t->setFont(new SWFFont("_sans"));

$t->addString('Status');
$t->setHeight(12);
$t->setBounds(230,200);
$t->setName('status');

$i = $m->add($t);
$i->moveTo(50, 150);

//end of status textbox


// Info textbox
$t = new SWFTextField(); //SWFTEXTFIELD_DRAWBOX 
$t->setFont(new SWFFont("_sans"));

$t->addString('Info');
$t->setHeight(8);
$t->setBounds(135,50);
$t->setName('trackinfo');

$i = $m->add($t);
$i->moveTo(50, 29);

// End of Info textbox


// Playlist textbox
	$t = new SWFTextField(SWFTEXTFIELD_NOEDIT | SWFTEXTFIELD_MULTILINE | SWFTEXTFIELD_HTML | SWFTEXTFIELD_NOSELECT);
	$t->setFont(new SWFFont("_sans"));

	$t->addString('Debugging.....');
	$t->setHeight(7);
	$t->setBounds(100,100);
	$t->setName('scrollText');

	$i = $m->add($t);
	$i->moveTo(200, 35);
// end of Playlist textbox


//Build playlist here

	$sql = "SELECT * FROM mp3info";
	$result = getMySQLInfo($sql);

	// Get number of rows (records) returned by SQL statement
	$rows = mysql_num_rows($result);

	// Start array string
	$AStxt = "myArray=new Array(";

	// loop through and build the array statement string to be 
	// added as Actionscript later
	   for ($i = 0 ; $i < $rows ; $i++)  {
			$myrow = mysql_fetch_array($result);
			$AStxt .= "'<a href=\"player.php?song=". $myrow["mp3ID"] ."\">" . $myrow["mp3Artist"] .' - '. $myrow["mp3Title"] . "</a>'";

			if ($i<($rows-1)) $AStxt .= ",";
	   }

	  $AStxt .= ");"; // end of array string


/* $AStxt should equal something like this:

	myArray=new Array('<a href=\"player.php?song=1"\>Artist1 - Title1</a>',
					  '<a href=\"player.php?song=2"\>Artist2 - Title2</a>', and so on);

	this array will be added later using Actionscript and is supposed to be a hyperlink

*/



// add the array string created above to the Actionscript
	$m->add(new SWFAction($AStxt));

// add each element of that array to the scrolling playlist textfield
	$m->add(new SWFAction("scrollText =''; for(i=0;i<myArray.length; i++) { scrollText+=myArray[i]+'\n'; }"));


// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// MP3 Stuff here


//GET the song id and grab that info from the DB pronto.....vite vite!
	$songID = $_GET['song'];


// -=-=-=-=-=-=-=-=-=-=-
// Grab the info about this track from the database.			
	
// Set the SQL statement
$sql = "SELECT * FROM mp3info WHERE mp3ID=".$songID;
// grab the recordset
$result = getMySQLInfo($sql);

// from this ONE recordset grab Artist and Title
$myrow = mysql_fetch_array($result);
$filename = $myrow["mp3Filename"].".swf";
// change windows path backslashes to forward slashes
$filename = str_replace('\\', '/', $filename);
$trackinfo = $myrow["mp3Artist"]." - ".$myrow["mp3Title"];
				 


	
	// Set initial volume for the player
	$volume = 80;


	// This is the movie clip that acts as our container for the MP3
	$mp3MoveClip = new SWFSprite();

	// Adds empty movie clip, won't be empty for long
	// Setname is important -> the name will be used by ActionScript for external movie loading
	$i = $m->add($mp3MoveClip);
	$i->setName("mp3");

	// This is the Actionscript that gets added to the movie to handle the addition of the
	// SWF embedded MP3
	$m->add(new SWFAction("_root.status=''; _root.trackinfo='Now Playing\n$trackinfo'; var Vol = $volume; loadMovie('$filename', _root.mp3);"));
	$m->add(new SWFAction("shhh = new sound();	shhh.setVolume(Vol);"));




// end 


//output
  header('Content-type: application/x-shockwave-flash');
  $m->output();
?>