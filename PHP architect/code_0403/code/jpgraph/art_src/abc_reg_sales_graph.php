<?php
/**
 * ABC Company Sale Region Graph
 *
 * @author	Jason E. Sweat
 * @since	2002-12-19
 */
require_once 'phpa_db.inc.php';

require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_canvas.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'jpgraph/jpgraph_scatter.php';
require_once 'jpgraph/jpgraph_line.php';

define('GRAPH_START', strftime(MYSQL_DT_FMT, 
                      mktime(0, 0, 0, 1, 1, date('Y')-1)));
define('WIDTH', 500);
define('HEIGHT', 310);
define('GRAPH_NAME', 'abc_reg_sales');
define('GRAPH_TIMEOUT', 60*24);
define('USING_TRUECOLOR', true);

$colors = array('pink', 'orange', 'yellow', 'lightgreen', 'lightblue');

$sql = <<<EOS
SELECT `id`, `region`
FROM `abc_region`
ORDER BY `id`
EOS;

$rs = $conn->Execute($sql);
if ($rs && !$rs->EOF) {
  $regions = $rs->GetAssoc();
} else {
  die('DB error - Regions Query');
}
// the following line of code is useful in the 
// development process to verify your result set
//print '<pre>'; var_dump($regions); exit;

$region_id = check_passed_region('region');
if (!$region_id) {
  graph_error('region parameter incorrect');
}

$graphName = GRAPH_NAME.$region_id.'.png';
$graph = new graph(WIDTH, HEIGHT, $graphName, GRAPH_TIMEOUT, true);

$sql = <<<EOS
SELECT `short_desc`, `item_desc`
FROM `abc_catalog`
ORDER BY `short_desc`
EOS;

$rs = $conn->Execute($sql);
if ($rs && !$rs->EOF) {
  $items = $rs->GetArray();
} else {
  die('DB error - Items Query');
}

$sql = <<<EOS
SELECT 
  YEAR( s.`date` ) AS y,
  MONTH( s.`date` ) AS m,
  SUM( s.`qty` ) AS qty,
  SUM( s.`rev` ) AS rev,
  c.`short_desc`,
  c.`item_desc`,
  CONCAT( YEAR( s.`date` ), MONTH( s.`date` ), c.`short_desc`) AS f_key
FROM `abc_sales` s, 
  `abc_state_region` sr, 
  `abc_catalog` c
WHERE s.`date` >= ?
  AND s.`state` = sr.`state_abbr`
  AND sr.`region_id` = ?
  AND s.`item_id` = c.`id`
GROUP BY
  YEAR( s.`date` ),
  MONTH( s.`date` ),
  c.`short_desc`,
  c.`item_desc`
ORDER BY 1, 2, 5
EOS;

$rs = $conn->Execute($sql, array(GRAPH_START, $region_id));
if ($rs && !$rs->EOF) {
  $salesData = $rs->GetArray();
} else {
  die('DB error - Sales Query');
}

$sql = <<<EOS
SELECT 
  CONCAT( YEAR( f.`date` ), MONTH( f.`date` ), c.`short_desc`) AS f_key,
  YEAR( f.`date` ) AS y,
  MONTH( f.`date` ) AS m,
  SUM( f.`qty` ) AS qty,
  SUM( f.`rev` ) AS rev,
  c.`short_desc`,
  c.`item_desc`
FROM 
  `abc_forecast` f,
  `abc_catalog` c
WHERE f.`date` >= ?
  AND f.`region_id` = ?
  AND f.`item_id` = c.`id`
GROUP BY
  YEAR( f.`date` ),
  MONTH( f.`date` ),
  c.`short_desc`,
  c.`item_desc`
ORDER BY 1, 2, 5
EOS;

$rs = $conn->Execute($sql, array(GRAPH_START, $region_id));
if ($rs && !$rs->EOF) {
  $fcstData = $rs->GetAssoc();
} else {
  die('DB error - Forecast Query');
}

// Graph Data construction loop
$graphData['f_qty'] = array();
$graphData['labelX'] = array();
for ($i=0,$j=count($salesData); $i<$j; $i++) {
  $row = $salesData[$i];
  if ('A'==$row['short_desc']) {
    $graphData['labelX'][] = strftime('%b', mktime(0, 0, 0, $row['m'], 1, $row['y']));
  }
  if (!array_key_exists($row['m']-1, $graphData['f_qty'])) {
    $graphData['f_qty'][$row['m']-1] = $fcstData[$row['f_key']]['qty'];
    $graphData['f_rev'][$row['m']-1] = $fcstData[$row['f_key']]['rev'];
    $graphData['qty'][$row['m']-1] = $row['qty'];
    $graphData['rev'][$row['m']-1] = $row['rev'];
  } else {
    $graphData['f_qty'][$row['m']-1] += $fcstData[$row['f_key']]['qty'];
    $graphData['f_rev'][$row['m']-1] += $fcstData[$row['f_key']]['rev'];
    $graphData['qty'][$row['m']-1] += $row['qty'];
    $graphData['rev'][$row['m']-1] += $row['rev'];
  }
  if(!array_key_exists($row['short_desc'], $graphData)) {
    $graphData[$row['short_desc']]['qty'] = array();
    $graphData[$row['short_desc']]['rev'] = array();
  }
  $graphData[$row['short_desc']]['qty'][] = $row['qty'];
  $graphData[$row['short_desc']]['rev'][] = $row['rev'];
}

for ($i=0,$j=count($graphData['labelX']); $i<$j; $i++) {
  $graphData['zero'][$i] = 0;
}
//extend the forecast revenue line by repeating the last value
$graphData['f_rev'][$j] = $graphData['f_rev'][$j-1];

if (USING_TRUECOLOR) {
  $graph->SetBackgroundImage('img/abc-background_prefade.png', BGIMG_FILLFRAME);
} else {
  //AdjBackgroundImage only works with GD, not GD2 true color
  $graph->SetBackgroundImage('img/abc-background.png', BGIMG_FILLFRAME);
  $graph->AdjBackgroundImage(0.9, 0.3);
}
$graph->img->SetMargin(65, 65, 30, 50);
$graph->SetScale('textlin');
$graph->SetY2Scale('lin');
$graph->SetY2OrderBack(false);
$graph->title->Set(date('Y')." Sales for {$regions[$region_id]} Region");
$graph->title->SetFont(FF_ARIAL, FS_BOLD, 12);
$graph->SetMarginColor('white'); 
$graph->yaxis->title->Set('Left Bar Units Sold');
$graph->yaxis->title->SetFont(FF_ARIAL, FS_BOLD, 10);
$graph->yaxis->SetLabelFormatCallback('y_fmt');
$graph->yaxis->SetTitleMargin(48);
$graph->y2axis->title->Set('Right Bar Revenue ( $ 000 )');
$graph->y2axis->title->SetFont(FF_ARIAL, FS_BOLD, 10);
$graph->y2axis->SetTitleMargin(45);
$graph->y2axis->SetLabelFormatCallback('y_fmt_dol_thou');
$graph->xaxis->SetTickLabels($graphData['labelX']);

$abqAdd = array();
$abrAdd = array();
for($i=0,$j=count($items); $i<$j; $i++) {
  $key = $items[$i]['short_desc'];
  $b1 = new BarPlot($graphData[$key]['qty']);
  $b1->SetFillColor($colors[$i]);
  $b1->SetLegend($key);
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

$l1 = new LinePlot($graphData['f_rev']);
$l1->SetStepStyle();
$l1->SetColor('darkgreen');
$l1->SetWeight(3);
$l1->SetLegend('Rev Fcst');

$graph->Add($gb1);
$graph->AddY2($gb2);
$graph->AddY2($l1);
$graph->legend->Pos(0.5, 0.95, 'center', 'center'); 
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->SetFillColor('white');
$graph->legend->SetShadow(false);
$graph->legend->SetLineWeight(0);
$graph->Stroke();

exit;

function y_fmt($val)
{
  return number_format($val);
}

function y_fmt_dol_thou($val)
{
  return '$'.number_format($val/1000);
}

function check_passed_region( $parm )
{
	global $regions;
	
	if (array_key_exists($parm,$_GET)) {
		$val = $_GET[$parm];
		if (array_key_exists($val, $regions)) {
			return $val;
		}
	}
	return false;
}

function graph_error($msg) 
{
  $graph = new CanvasGraph(WIDTH, HEIGHT);    

  $t1 = new Text($msg);
  $t1->Pos(0.05, 0.5);
  $t1->SetOrientation('h');
  $t1->SetFont(FF_ARIAL, FS_BOLD);
  $t1->SetColor('red');
  $graph->AddText($t1);

  $graph->Stroke();
  exit;
}

?>