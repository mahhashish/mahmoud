$region_id = check_passed_region('region');
if (!$region_id) {
  graph_error('region parameter incorrect');
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