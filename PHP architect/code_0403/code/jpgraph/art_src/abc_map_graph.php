<?php
/**
 * pie plots on an image map
 * leading to csim drilldown
 *
 * @author  Jason E. Sweat
 * @since   2002-12-18
 */
require_once 'phpa_db.inc.php';

require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_canvas.php';
require_once 'jpgraph/jpgraph_pie.php';

define('GRAPH_START', strftime(MYSQL_DT_FMT, mktime(0, 0, 0, 1, 1, date('Y'))));
define('WIDTH', 500);
define('HEIGHT', 310);
define('PIE_SIZE', 0.16);
define('DRILL_GRAPH', 'abc_reg_sales_graph.php?region=');
define('IMG_DIR', 'img/');

$sql = <<<EOS
SELECT *
FROM `abc_region`
ORDER BY `id`
EOS;

$rs = $conn->Execute($sql);
if ($rs && !$rs->EOF) {
  $regions = $rs->GetArray();
} else {
  die('DB error');
}

$region = 1;
$region_id = $regions[$region]['id'];

$sql = <<<EOS
SELECT sr.`region_id`, 
  s.`channel_id`,
  c.`short_desc`,
  r.`region`,
  r.`map_x`,
  r.`map_y`,
  ROUND(SUM( s.`rev` )/1000) AS rev
FROM `abc_sales` s, `abc_state_region` sr, `abc_channel` c, `abc_region` r
WHERE s.`state` = sr.`state_abbr`
  AND sr.`region_id` = r.`id`
  AND s.`channel_id` = c.`id`
GROUP BY sr.`region_id`, s.`channel_id`
ORDER BY sr.`region_id`, s.`channel_id`
EOS;

$rs = $conn->Execute($sql);
if ($rs && !$rs->EOF) {
  $regionData = $rs->GetArray();
} else {
  die('DB error');
}

$lastRegion = '';
$rIndex = -1;
$graphData['label'] = array();
for ($i=0,$j=count($regionData); $i<$j; $i++) {
  if ($lastRegion != $regionData[$i]['region_id']) {
    $lastRegion = $regionData[$i]['region_id'];
    $rIndex++;
    $graphData['r'.$rIndex]['label'] = array();
    $graphData['r'.$rIndex]['rev'] = array();
    $graphData['r'.$rIndex]['revFmt'] = array();
  }
  if (!$rIndex) { //first region only
    $graphData['label'][] = $regionData[$i]['short_desc'];
  }
  $graphData['r'.$rIndex]['label'][] = $regionData[$i]['short_desc']."\n$".number_format($regionData[$i]['rev']);
  $graphData['r'.$rIndex]['rev'][] = $regionData[$i]['rev'];
  $graphData['r'.$rIndex]['revFmt'][] = '$'.number_format($regionData[$i]['rev']);
  $graphData['r'.$rIndex]['targets'][] = DRILL_GRAPH.$regionData[$i]['region_id'];
  $graphData['r'.$rIndex]['alts'][] = "Click for more information regarding {$regions[$rIndex]['region']} sales.";
  $graphData['r'.$rIndex]['map_x'] = $regionData[$i]['map_x'];
  $graphData['r'.$rIndex]['map_y'] = $regionData[$i]['map_y'];
}

$sliceColors = array('lightgreen', 'pink', 'lightblue');

$graphName = IMG_DIR.'abc_channel_graph.png';
$graph = new PieGraph(WIDTH, HEIGHT);
$graph->SetBackgroundImage('img/abc-regions.png', BGIMG_FILLFRAME);

for ($i=0; $i<$rIndex+1; $i++) {
  $pickRegion = 'r'.$i;

  $p1 = new PiePlot($graphData[$pickRegion]['rev']);
  $p1->SetCenter($graphData[$pickRegion]['map_x'], $graphData[$pickRegion]['map_y']);
  $p1->SetSize(PIE_SIZE);
  $p1->SetLabels($graphData[$pickRegion]['revFmt']);
  $p1->SetSliceColors($sliceColors);
  $p1->SetCSIMTargets($graphData[$pickRegion]['targets'], $graphData[$pickRegion]['alts']);

  if (!$i) {
    $p1->SetLegends($graphData['label']);
  }

  $graph->Add($p1);
}

$graph->legend->Pos(0.9, 0.85, 'center', 'center'); 
$graph->Stroke($graphName);

$mapName = 'ABC_Region_Drill';
$imgMap = $graph->GetHTMLImageMap($mapName); 

print <<<EOS
$imgMap
<img src="$graphName" alt="ABC Sales by Channel" ismap usemap="#$mapName" border="0">
EOS;

?>
