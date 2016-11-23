$graph->SetY2Scale('lin');
$graph->SetY2OrderBack(false);

//generate the individual plots
$b1 = new BarPlot($graphData['qty']);
$b2 = new BarPlot($graphData['rev']);
$b2->SetFillColor('lightgreen');
$b1z = new BarPlot($graphData['zero']);
$b2z = new BarPlot($graphData['zero']);
$l1 = new LinePlot($graphData['f_rev']);
$l1->SetStepStyle();
$l1->SetColor('darkgreen');
$l1->SetWeight(3);

//create the grouped plots
$gb1 = new GroupBarPlot(array($b1, $b1z));
$gb2 = new GroupBarPlot(array($b2z, $b2));

//add the plots to the graph object
$graph->Add($gb1);
$graph->AddY2($gb2);
$graph->AddY2($l1);
