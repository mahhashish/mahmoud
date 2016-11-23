<? $colors = array('pink', 'orange', 'yellow', 'lightgreen', 'lightblue');

$abqAdd = array();
$abrAdd = array();
for($i=0,$j=count($items); $i<$j; $i++) {
  $key = $items[$i]['short_desc'];
  $b1 = new BarPlot($graphData[$key]['qty']);
  $b1->SetFillColor($colors[$i]);
  $b1->SetLegend($items[$i]['item_desc']);
  $abqAdd[] = $b1;
  
  $b2 = new BarPlot($graphData[$key]['rev']);
  $b2->SetFillColor($colors[$i]);
  $abrAdd[] = $b2;
}
$ab1 = new AccBarPlot($abqAdd);
$ab2 = new AccBarPlot($abrAdd);
$b1z = new BarPlot($graphData['zero']);
$b2z = new BarPlot($graphData['zero']);

$gb1 = new GroupBarPlot(array($ab1, $b1z));
$gb2 = new GroupBarPlot(array($b2z, $ab2));

$graph->Add($gb1);
$graph->AddY2($gb2);
?>