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

class drawShapes {

	var $lineThickness = 1;
	var $lineRed = 255;
	var $lineBlue = 255;
	var $lineGreen = 255;
	
	var $fillRed = 255;
	var $fillBlue = 255;
	var $fillGreen = 255;
	var $fillAlpha = 255;


		/**
        * Constructor is NULL
        * Param     None
        */

       
	function drawShapes() {

	}

		/**
        * Draws an rectangle
        * Arguments     x, y, width and height

				(x,y)   	      ___________
					+---------+         |
                    |         |         |
					|	      |      $height
					|	      |         |
					+---------+   ______|_____
					
					|		  |	  
					|--width--|
					|         |
					


		* Returns	 a Ming Shape Object
        */

	function getRectangleShape($x, $y, $height, $width)  {

		$s = new SWFShape();
		
		$s->setLine($this->lineThickness, $this->lineRed, $this->lineGreen, $this->lineBlue);
		$s->setRightFill($s->addFill($this->fillRed, $this->fillGreen, $this->fillBlue,$this->fillAlpha));

		$s->movePenTo($x,$y);
		$s->drawLineTo($x,$y+$height);
		$s->drawLineTo($x+$width,$y+$height);
		$s->drawLineTo($x+$width,$y);
		$s->drawLineTo($x,$y);
  
  		return $s;
  
  }


		/**
        * Draws an equilateral triangle in the direction pointing as specified by the user
        * Arguments     x, y, direction and height

					     (x,y)    ___________
							/\          |
                           /  \         |
						  /    \     $height
						 /      \       |
						 -------- ______|_____
								  

				** Direction is where the point is pointing **


		* Returns	 a Ming Shape Object
        */


	function getTriangleShape($x,$y,$direction,$height) {

		$s = new SWFShape();

		$s->setLine($this->lineThickness, $this->lineRed, $this->lineGreen, $this->lineBlue);
		$s->setRightFill($s->addFill($this->fillRed, $this->fillGreen, $this->fillBlue,$this->fillAlpha));

		$s->movePenTo($x, $y);

		if ($direction == "right") {
			$s->drawLineTo($x-$height,$y-(0.5*$height));
			$s->drawLineTo($x-$height, $y+(0.5*$height));
			$s->drawLineTo($x, $y);
		}
		if ($direction == "left") {
			$s->drawLineTo($x+$height,$y-(0.5*$height));
			$s->drawLineTo($x+$height, $y+(0.5*$height));
			$s->drawLineTo($x, $y);
		}
		if ($direction == "up") {
			$s->drawLineTo($x-(0.5*$height),$y+$height);
			$s->drawLineTo($x+(0.5*$height), $y+$height);
			$s->drawLineTo($x, $y);
		}
		if ($direction == "down") {
			$s->drawLineTo($x-(0.5*$height),$y-$height);
			$s->drawLineTo($x+(0.5*$height), $y-$height);
			$s->drawLineTo($x, $y);
		}

		return $s;

	}


	
		/**
        * Draws an circle with radius as specified by the user
        * Arguments     x, y, radius

												
				** You are crazy if you think I am going
				to even attempt an ASCII circle ;-)

				BUT the center of the circle is x,y

		* Returns	 a Ming Shape Object
        */

	function getCircleShape($x,$y, $r) {
    
		$a = $r * 0.414213562; // = tan(22.5 deg)
		$b = $r * 0.707106781; // = sqrt(2)/2 = sin(45 deg)
		
		$s = new SWFShape();
		
		$s->setLine($this->lineThickness, $this->lineRed, $this->lineGreen, $this->lineBlue);
		$s->setRightFill($s->addFill($this->fillRed, $this->fillGreen, $this->fillBlue,$this->fillAlpha));

		$s->movePenTo($x+$r, $y);

		$s->drawCurveTo($x+$r, $y-$a, $x+$b, $y-$b);
		$s->drawCurveTo($x+$a, $y-$r, $x, $y-$r);
		$s->drawCurveTo($x-$a, $y-$r, $x-$b, $y-$b);
		$s->drawCurveTo($x-$r, $y-$a, $x-$r, $y);
		$s->drawCurveTo($x-$r, $y+$a, $x-$b, $y+$b);
		$s->drawCurveTo($x-$a, $y+$r, $x, $y+$r);
		$s->drawCurveTo($x+$a, $y+$r, $x+$b, $y+$b);
		$s->drawCurveTo($x+$r, $y+$a, $x+$r, $y);
	
	return $s;
	//return "Circle created with radius ".$r." X = ".$x." Y = ".$y;

	}



		/**
        * Draws an oval
        * Arguments     x, y, radius of corners and width

			   (x,y) _________	 
					(_________)  
					 
					 
					|		  |	  
					|--width--|
					|         |
					


		* Returns	 a Ming Shape Object
        */

   function getOvalShape($x,$y,$r,$width)  {

	$s = new SWFShape();
	
	$s->setLine($this->lineThickness, $this->lineRed, $this->lineGreen, $this->lineBlue);
	$s->setRightFill($s->addFill($this->fillRed, $this->fillGreen, $this->fillBlue,$this->fillAlpha));
	
	$s->movePenTo($x, $y);
	$s->drawCurveTo($x, $y-$r, $x+$r, $y-$r);
	$s->drawLineTo($x+($width-($r)), $y-$r);
	$s->drawCurveTo($x+$width, $y-$r, $x+$width, $y);
	$s->drawCurveTo($x+$width, $y+$r, $x+($width-($r)), $y+$r);
	$s->drawLineTo($x+$r, $y+$r);
	$s->drawCurveTo($x, $y+$r, $x, $y);

	return $s;
  }


		/**
        * Accessor set method for changing the line thickness
        * Arguments     new URL
		* Returns	 nothing
        */

	function setShapeLineStyle($thickness,$red,$green,$blue) {
		$this->lineThickness = $thickness;
		$this->lineRed = $red;
		$this->lineGreen = $green;
		$this->lineBlue = $blue;

	
	} // setLineStyle function



		/**
        * Accessor set method for changing the fill style colours only at this point
        * Arguments     new date search string
		* Returns	 nothing
        */

	function setShapeFillStyle($red,$green,$blue,$alpha) {

		$this->fillRed = $red;
		$this->fillGreen = $green;
		$this->fillBlue = $blue;
		$this->fillAlpha = $alpha;

	
	} // setFillStyle function

		
		/**
        * Accessor set method for changing the alpha.
		* Important for buttons when you only wish to change the alpha but keep
		* the other variables the same
        * Arguments     new date search string
		* Returns	 nothing
        */

	function setAlpha($alpha) {

		$this->fillAlpha = $alpha;

	
	} // setFillStyle function



} //class


?>
