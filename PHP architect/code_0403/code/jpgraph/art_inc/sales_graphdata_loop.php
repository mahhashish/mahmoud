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
