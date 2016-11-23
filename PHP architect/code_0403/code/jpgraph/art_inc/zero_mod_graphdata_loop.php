for ($i=0,$j=count($graphData['labelX']); $i<$j; $i++) {
  $graphData['zero'][$i] = 0;
}
//extend the forecast revenue line by repeating the last value
$graphData['f_rev'][$j] = $graphData['f_rev'][$j-1];