<? define('IMG_DIR', 'img/');
$graphName = IMG_DIR.'abc_channel_graph.png';

$graph = new PieGraph(WIDTH, HEIGHT);
//the rest of the graph code...
$graph->Stroke($graphName);

$mapName = 'ABC_Region_Drill';
$imgMap = $graph->GetHTMLImageMap($mapName); 

print <<<EOS
$imgMap
<img src="$graphName" alt="ABC Sales by Channel" 
  ismap usemap="#$mapName" border="0">
EOS;
?>