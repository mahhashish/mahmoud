<?php
include('statsinc.php');

// Include JPGraph files

include('/usr/phpinc/jpgraph.php');
include('/usr/phpinc/jpgraph_scatter.php');

foreach ($data as $sale) {
        $datax[]=$sale->salesman->years_exp;
        $datay[]=$sale->stockitem->price*$sale->qty;
}

// Create a scatter plot
$s1 = new ScatterPlot($datay, $datax);

// Build our graph object and set properties
$graph = New Graph(450, 300, "auto");

// Use an linear X-scale
$graph->SetScale("linlin");

// Set graph and axis titles
$graph->title->Set("Sales Value and Years Experience Scatter Plot");
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->Set("Years Experience");
$graph->xaxis->title->SetFont(FF_FONT2,FS_BOLD);
$graph->yaxis->title->Set("Sales Value");
$graph->yaxis->title->SetFont(FF_FONT2, FS_BOLD);

// Add some grace to the top so that the scale doesn't end exactly at the max value.
$graph->yaxis->scale->SetGrace(10);

// Make the margin around the plot a little bit bigger than default
$graph->img->SetMargin(40,140,40,80);

// Count from 0-5 not 1-6 as JPGraph would by default
$graph->xaxis->SetTickLabels(array(0, 1, 2, 3, 4, 5));
$graph->xaxis->SetLabelAngle(90);

// Add our scatter plot objects to the graph
$graph->add($s1);
$graph->stroke();
?>
