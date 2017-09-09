<?php
include('statsinc.php');

foreach ($data as $sale) {
	$datax[]=$sale->stockitem->price*$sale->qty;
	$datay_yearsexp[]=$sale->salesman->years_exp;
	// Assign a numeric value to each level of education
	switch ($sale->salesman->education) {
		case 'none':
			$datay_education[]=0;
			break;
		case 'diploma':
			$datay_education[]=1;
			break;
		case 'degree':
			$datay_education[]=2;
			break;
		case 'doctorate':
			$datay_education[]=3;
			break;
	}
}

echo "Years Experience correlates to Sale Value: " . correlation($datax, $datay_yearsexp);
echo "<br>Education correlates to Sale Value: " . correlation($datax, $datay_education);
?>
