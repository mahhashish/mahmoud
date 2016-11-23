$graph = new graph(WIDTH, HEIGHT);
$graph->SetScale('textlin');

$b1 = new BarPlot($graphData['qty']);
$l1 = new LinePlot($graphData['f_qty']);

$graph->Add($b1);
$graph->Add($l1);
$graph->Stroke();