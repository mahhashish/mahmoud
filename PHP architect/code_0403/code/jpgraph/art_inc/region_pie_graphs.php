$graph = new PieGraph(WIDTH, HEIGHT);
$graph->SetBackgroundImage('img/abc-regions.png', BGIMG_FILLFRAME);

for ($i=0; $i<$rIndex+1; $i++) {
  $pickRegion = 'r'.$i;

  $p1 = new PiePlot($graphData[$pickRegion]['rev']);
  $p1->SetCenter($graphData[$pickRegion]['map_x'],
         $graphData[$pickRegion]['map_y']);
  $p1->SetSize(PIE_SIZE);
  $p1->SetLabels($graphData[$pickRegion]['revFmt']);
  $p1->SetSliceColors($sliceColors);
  if (!$i) { 
    $p1->SetLegends($graphData['label']);
  }

  $graph->Add($p1);
}

$graph->legend->Pos(0.9, 0.85, 'center', 'center'); 
$graph->Stroke();