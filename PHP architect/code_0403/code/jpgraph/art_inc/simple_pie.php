$sliceColors = array('lightgreen', 'pink', 'lightblue');
$graph = new PieGraph(WIDTH, HEIGHT);
$graph->title->Set($regions[$region]['region'].' Region');
$graph->subtitle->Set('Sales by Channel since '.GRAPH_START);

$p1 = new PiePlot($graphData[$pickRegion]['rev']);
$p1->SetLegends($graphData[$pickRegion]['label']);
$p1->SetSliceColors($sliceColors);

$graph->Add($p1);
$graph->Stroke();