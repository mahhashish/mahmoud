$b1 = new BarPlot($graphData['rev']);

$l1 = new LinePlot($graphData['f_rev']);
$l1->SetStepStyle();
$l1->SetColor('darkgreen');
$l1->SetWeight(3);