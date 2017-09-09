<?php
include('statsinc.php');

// Include JPGraph files
include('/usr/phpinc/jpgraph.php');
include('/usr/phpinc/jpgraph_bar.php');

// Prepare one dimensional arrays for Y axis data and data fed into stats functions

foreach ($data as $sale) {
	$yaxisdata[$sale->qty]++;
	$statsdata[]=$sale->qty;
}
// Sort array
sort($statsdata);

// Build our graph object and set properties
$graph = New Graph(450, 300, "auto");

// Use an integer X-scale
$graph->SetScale("textlin");

// Set graph and axis titles
$graph->title->Set("Frequency Distribution of Sales Quantity");
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->Set("Sales Quantity");
$graph->xaxis->title->SetFont(FF_FONT2,FS_BOLD);
$graph->yaxis->title->Set("Frequency");
$graph->yaxis->title->SetFont(FF_FONT2, FS_BOLD);

// Add some grace to the top so that the scale doesn't end exactly at the max value.
$graph->yaxis->scale->SetGrace(10);

// Set sub-titles of mean, median and std dev
$graph->subtitle->Set("Mean: " . round(mean($statsdata), 2) . " Median: " . median($statsdata) . " Std Dev: " . round(stddev($statsdata), 2));

// Make the margin around the plot a little bit bigger than default
$graph->img->SetMargin(40,140,40,80);

// Display every 5th datalabel
$graph->xaxis->SetTextTickInterval(5);
$graph->xaxis->SetLabelAngle(90);

// Create the bar plot
$b1 = new BarPlot($yaxisdata);
$b1->SetLegend("Sales Quantity");
$b1->SetAbsWidth(6);
$b1->SetShadow();

// The order the plots are added determines who's ontop
$graph->Add($b1);

// Finally output the  image
$graph->Stroke();
?>
