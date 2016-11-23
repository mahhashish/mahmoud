define('GRAPH_NAME', 'abc_reg_sales');
$graphName = GRAPH_NAME.$region_id.'.png';
$graphTimeout = 60*24;

$graph = new graph(WIDTH, HEIGHT, $graphName, $graphTimeout, true);