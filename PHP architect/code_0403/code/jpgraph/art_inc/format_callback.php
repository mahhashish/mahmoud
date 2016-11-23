$graph->y2axis->SetLabelFormatCallback('y_fmt_dol_thou');

function y_fmt_dol_thou($val)
{
  return '$'.number_format($val/1000);
}
