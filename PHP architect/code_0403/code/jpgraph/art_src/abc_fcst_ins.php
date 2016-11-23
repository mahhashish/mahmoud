<?php
/**
 * load forecast data for abc examples
 *
 * @author	Jason E. Sweat
 * @since	2002-12-17
 */
error_reporting(E_ALL);

require_once 'phpa_db.inc.php';

define('FCST_VAR_NEG', 0.20);
define('FCST_VAR_POS', 0.16);
define('MYSQL_DT_FMT', '%Y-%m-%d');

$sqlCnt = <<<EOS
SELECT COUNT( 1 ) AS cnt
FROM `abc_forecast`
EOS;

$rs = $conn->Execute($sqlCnt);
if ($rs && !$rs->EOF) {
	$row = $rs->fetchRow();
	if (!'0' == $row['cnt']) {
		die('`abc_forecast` not empty, try truncating first.');
	}
} else {
	die('DB Error');
}

$sql = <<<EOS
SELECT id, unit_price
FROM `abc_catalog`
EOS;

$rs = $conn->Execute($sql);
if ($rs && !$rs->EOF) {
	$priceData = $rs->getAssoc();
} else {
	die('DB Error');
}

foreach($priceData as $key => $value) {
	$price[$key] = (float)$value;
}

$sql = <<<EOS
SELECT YEAR( s.date ) AS y, 
	MONTH ( s.date) AS m, 
	s.channel_id, 
	s.item_id, 
	r.region_id, 
	sum( s.qty ) AS qty
FROM `abc_sales` s, `abc_state_region` r
WHERE s.state = r.state_abbr
GROUP BY YEAR( s.date ) , 
	MONTH ( s.date ), 
	s.channel_id, 
	s.item_id, 
	r.region_id
EOS;

$rs = $conn->Execute($sql);
if ($rs && !$rs->EOF) {
	$baseData = $rs->getArray();
} else {
	die('DB Error');
}

$sql = <<<EOS
INSERT INTO `abc_forecast` (
	`date`, `channel_id`, `item_id`, `region_id`, `qty`, `rev` )
VALUES (?, ?, ?, ?, ?, ?)
EOS;

for ($i=0,$j=count($baseData); $i<$j; $i++) {
	$row = $baseData[$i];
	$rndMaxP = $row['qty'] * FCST_VAR_POS;
	$rndMaxN = $row['qty'] * FCST_VAR_NEG;
	$decBias = ('12'==$row['m']) ? 2.2 : 1;
	$fQty = $row['qty'] + rand(1, $rndMaxP) - rand(1, $rndMaxN);
	$fQty = round($fQty/10,0)*10*$decBias;
	$fRev = $fQty * $price[$row['item_id']];
	$fDate = strftime(MYSQL_DT_FMT, mktime(0,0,0,$row['m'],1,$row['y']));
	$conn->Execute($sql, array($fDate, $row['channel_id'], $row['item_id'], $row['region_id'], $fQty, $fRev));
}

$rs = $conn->Execute($sqlCnt);
if ($rs && !$rs->EOF) {
	$row = $rs->fetchRow();
	print $row['cnt'] . ' records inserted into `abc_forecast`.';
}

?>