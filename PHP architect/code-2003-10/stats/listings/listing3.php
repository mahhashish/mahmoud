<?php
include('salesclasses.php');

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
?>
