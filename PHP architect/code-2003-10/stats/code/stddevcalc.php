<?php
include('dataprep.php');

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

// Implement the Standard Deviation algorithm

function stddev($data) {
        $mean=mean($data);
        $totalval=0;
        $totalsq=0;
        foreach ($data as $element) {
                $total+=($element-$mean)*($element-$mean);
        }
        return sqrt($total/sizeof($data));
}

// Construct an array of sale values

foreach ($data as $sale) {
        $salevaluedata[]=$sale->stockitem->price*$sale->qty;
}

echo "Standard deviation of sale values is: " . stddev($salevaluedata);
?>
