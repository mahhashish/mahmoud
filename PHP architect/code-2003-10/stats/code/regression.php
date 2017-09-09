<?php
include('statsinc.php');

function regression($datax, $datay, $xval) {
	$stddevx=stddev($datax);
	$stddevy=stddev($datay);
	$meanx=mean($datax);
	$meany=mean($datay);
	$r=correlation($datax, $datay);
	return $r*($stddevy/$stddevx)*$xval-$r*($stddevy/$stddevx)*$meanx+$meany;
}

foreach ($data as $sale) {
	$datax[]=$sale->salesman->years_exp;
        $datay[]=$sale->stockitem->price*$sale->qty;
}

echo "A salesman with 1 year exp will most likely sell: \$" 
. round(regression($datax, $datay, 1), 2) . " of product";
?>
