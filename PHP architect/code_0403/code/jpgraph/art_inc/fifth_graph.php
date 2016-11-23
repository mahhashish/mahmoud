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

$graph->legend->Pos(0.5, 0.95, 'center', 'center'); 
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->SetFillColor('white');
$graph->legend->SetShadow(false);
$graph->legend->SetLineWeight(0);