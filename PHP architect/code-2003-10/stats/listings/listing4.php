<?php
include('dataprep.php');

// Calculate median by returning the element at the midpoint of the array of data. The array MUST be sorted and contain an odd number of elements for this to work

function median($data) {
	return $data[floor(sizeof($data)/2)];
}

// Calculate mean by diving the sum of all elements by the number of elements

function mean($data) {
	$total=0;
	$count=0;

	foreach ($data as $element) {
		$total+=$element;
		$count++;
	}
	return $total/$count;
}

// Construct an array of sale values. Does not need to sorted because of the ORDER BY clause used to extract the data

foreach ($data as $sale) {
	$salevaluedata[]=$sale->stockitem->price*$sale->qty;
}

echo "Mean Sale Value: " . mean($salevaluedata);
echo "<br><br>";
echo "Median Sale Value: " . median($salevaluedata);
?>
