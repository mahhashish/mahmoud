<?php

// Class to represent the data of each sale

class sale {
	var $salesman;
	var $stockitem;
	var $client;
	var $qty;

	function sale() {
		$this->salesman = New salesman();
		$this->stockitem = New stockitem();
		$this->client = New client();
	}
};

// Class to represent each salesman

class salesman {
	var $years_exp;
	var $education;
	var $name;
};

// Class to represent each stock item

class stockitem {
	var $price;
	var $name;
};

// Class to represent each client

class client {
	var $sector;
	var $name;
};

// SQL Statement to extract the full data for each sale using SQL JOINs

$ssql="SELECT * FROM sales s "
. "JOIN salesmen sm, stockitems si, clients c "
. "WHERE s.salesmanid=sm.id "
. "AND s.stockitemid=si.id "
. "AND s.clientid=c.id "
. "ORDER BY price*qty ASC";

// Connect to the database

mysql_connect('127.0.0.1', 'root', '');
mysql_select_db('sales');

// Execute the SQL Statement and read its results into an array of objects

$res=mysql_query($ssql);

while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
        $sale = New sale();
        $sale->salesman->years_exp=$row['years_exp'];
        $sale->salesman->education=$row['education'];
        $sale->stockitem->price=$row['price'];
        $sale->client->sector=$row['sector'];
        $sale->qty=$row['qty'];
        $data[] = $sale;
}

// Close DB

mysql_free_result($res);
mysql_close();

// Calculate median by returning the element at the midpoint of the array of data
// The array MUST be sorted for this to work

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

// Implement the Pearson coefficient of correlation algorithm

function correlation($datax, $datay) {
	$meanx=mean($datax);
	$meany=mean($datay);
	$sigmaxy=0;
	$sigmaxsq=0;
	$sigmaysq=0;
	$xtot=0;
	$ytot=0;
	for ($i=0; $i<sizeof($datax); $i++) {
		$sigmaxsq+=($datax[$i]-$meanx)*($datax[$i]-$meanx);
		$sigmaysq+=($datay[$i]-$meany)*($datay[$i]-$meany);
		$sigmaxy+=$datax[$i]*$datay[$i];
		$xtot+=$datax[$i];
		$ytot+=$datay[$i];
	}
	$sigmaxy=$sigmaxy-($xtot*$ytot)/sizeof($datax);
	return $sigmaxy/(sqrt($sigmaxsq*$sigmaysq));
}

// Implement the simple regression algorithm

function regression($datax, $datay, $xval) {
        $stddevx=stddev($datax);
        $stddevy=stddev($datay);
        $meanx=mean($datax);
        $meany=mean($datay);
        $r=correlation($datax, $datay);
        return $r*($stddevy/$stddevx)*$xval-$r*($stddevy/$stddevx)*$meanx+$meany;
}

?>
