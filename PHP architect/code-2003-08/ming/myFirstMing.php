 <?php
  
	$m = new SWFMovie();
	$m->setDimension(320, 240);
	$m->setBackground(255, 255, 255);


	// All the rest goes in here!
	$s = new SWFShape();

	//Starting X and Y values
	$x = 100;
	$y = 100;

	//Height of trangle
	$height = 20;

	$s->setLine(2, 0, 255, 0);
	$s->setRightFill($s->addFill(0,200,0));
	$s->movePenTo($x, $y);
	$s->drawLineTo($x-$height,$y-(0.5*$height));
	$s->drawLineTo($x-$height, $y+(0.5*$height));
	$s->drawLineTo($x, $y);

	$m->add($s);


	header('Content-type: application/x-shockwave-flash');
	$m->output();


  ?>
