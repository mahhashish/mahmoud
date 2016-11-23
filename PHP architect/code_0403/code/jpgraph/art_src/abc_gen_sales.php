<?php
/**
 * simulate sales for ABC Co. and log to database
 *
 * Please note this script relies on U.S. Census data 
 * downloaded from http://www.census.gov/geo/www/gazetteer/places2k.html.  
 * This data is used to distribute sales by state in proportion with 
 * the population in each state.  Complete details on loading this 
 * census data and using JpGraph and Smarty to present the data 
 * is included in the PHP Graphics book.
 *
 * @author	Jason E. Sweat
 * @since	2002-12-15
 */
error_reporting(E_ALL);
set_time_limit(0);

require_once 'phpa_db.inc.php';
require_once 'adodb/adodb.inc.php' ;

define('START_TIME',mktime(0,0,0,1,1,2002));
define('END_TIME',mktime(0,0,0,12,15,2002));
define('TIME_STEP', 86400);
define('INS_SQL','INSERT INTO abc_sales (`date`, `channel_id`, `item_id`, `state`, `qty`, `rev`) VALUES (?, ?, ?, ?, ?, ?)');
define('BASE_NUM_SALES',30);
define('SALES_RAND_FACTOR',20);

$sql = <<<EOS
SELECT id, unit_price
FROM abc_catalog
EOS;

$rs = $conn->Execute($sql);
if ($rs && !$rs->EOF) {
	$itemPrice = $rs->GetAssoc();
}

$itemStdQty = array(0,100,200,50,10,5);
$itemStdRnd = array(0,10,20,5,2,5);
$itemStdInc = array(0,5,5,5,5,1);

foreach ($itemPrice as $key => $value) {
	$itemInfo[$key]['price'] = (float)$value;
	$itemInfo[$key]['qty'] = $itemStdQty[$key];
	$itemInfo[$key]['rnd'] = $itemStdRnd[$key];
	$itemInfo[$key]['inc'] = $itemStdInc[$key];
}

$stateData=unserialize('a:49:{i:0;a:2:{s:5:"value";i:159;s:5:"state";s:2:"AL";}i:1;a:2:{s:5:"value";i:255;s:5:"state";s:2:"AR";}i:2;a:2:{s:5:"value";i:439;s:5:"state";s:2:"AZ";}i:3;a:2:{s:5:"value";i:1651;s:5:"state";s:2:"CA";}i:4;a:2:{s:5:"value";i:1805;s:5:"state";s:2:"CO";}i:5;a:2:{s:5:"value";i:1927;s:5:"state";s:2:"CT";}i:6;a:2:{s:5:"value";i:1947;s:5:"state";s:2:"DC";}i:7;a:2:{s:5:"value";i:1975;s:5:"state";s:2:"DE";}i:8;a:2:{s:5:"value";i:2547;s:5:"state";s:2:"FL";}i:9;a:2:{s:5:"value";i:2840;s:5:"state";s:2:"GA";}i:10;a:2:{s:5:"value";i:2945;s:5:"state";s:2:"IA";}i:11;a:2:{s:5:"value";i:2991;s:5:"state";s:2:"ID";}i:12;a:2:{s:5:"value";i:3435;s:5:"state";s:2:"IL";}i:13;a:2:{s:5:"value";i:3652;s:5:"state";s:2:"IN";}i:14;a:2:{s:5:"value";i:3748;s:5:"state";s:2:"KS";}i:15;a:2:{s:5:"value";i:3893;s:5:"state";s:2:"KY";}i:16;a:2:{s:5:"value";i:4053;s:5:"state";s:2:"LA";}i:17;a:2:{s:5:"value";i:4280;s:5:"state";s:2:"MA";}i:18;a:2:{s:5:"value";i:4469;s:5:"state";s:2:"MD";}i:19;a:2:{s:5:"value";i:4515;s:5:"state";s:2:"ME";}i:20;a:2:{s:5:"value";i:4870;s:5:"state";s:2:"MI";}i:21;a:2:{s:5:"value";i:5046;s:5:"state";s:2:"MN";}i:22;a:2:{s:5:"value";i:5246;s:5:"state";s:2:"MO";}i:23;a:2:{s:5:"value";i:5348;s:5:"state";s:2:"MS";}i:24;a:2:{s:5:"value";i:5380;s:5:"state";s:2:"MT";}i:25;a:2:{s:5:"value";i:5668;s:5:"state";s:2:"NC";}i:26;a:2:{s:5:"value";i:5691;s:5:"state";s:2:"ND";}i:27;a:2:{s:5:"value";i:5752;s:5:"state";s:2:"NE";}i:28;a:2:{s:5:"value";i:5796;s:5:"state";s:2:"NH";}i:29;a:2:{s:5:"value";i:6097;s:5:"state";s:2:"NJ";}i:30;a:2:{s:5:"value";i:6162;s:5:"state";s:2:"NM";}i:31;a:2:{s:5:"value";i:6233;s:5:"state";s:2:"NV";}i:32;a:2:{s:5:"value";i:6912;s:5:"state";s:2:"NY";}i:33;a:2:{s:5:"value";i:7318;s:5:"state";s:2:"OH";}i:34;a:2:{s:5:"value";i:7441;s:5:"state";s:2:"OK";}i:35;a:2:{s:5:"value";i:7563;s:5:"state";s:2:"OR";}i:36;a:2:{s:5:"value";i:8002;s:5:"state";s:2:"PA";}i:37;a:2:{s:5:"value";i:8039;s:5:"state";s:2:"RI";}i:38;a:2:{s:5:"value";i:8182;s:5:"state";s:2:"SC";}i:39;a:2:{s:5:"value";i:8209;s:5:"state";s:2:"SD";}i:40;a:2:{s:5:"value";i:8412;s:5:"state";s:2:"TN";}i:41;a:2:{s:5:"value";i:9158;s:5:"state";s:2:"TX";}i:42;a:2:{s:5:"value";i:9238;s:5:"state";s:2:"UT";}i:43;a:2:{s:5:"value";i:9491;s:5:"state";s:2:"VA";}i:44;a:2:{s:5:"value";i:9513;s:5:"state";s:2:"VT";}i:45;a:2:{s:5:"value";i:9724;s:5:"state";s:2:"WA";}i:46;a:2:{s:5:"value";i:9916;s:5:"state";s:2:"WI";}i:47;a:2:{s:5:"value";i:9981;s:5:"state";s:2:"WV";}i:48;a:2:{s:5:"value";i:9999;s:5:"state";s:2:"WY";}}');

for ($i=START_TIME; $i<END_TIME; $i+=TIME_STEP) {
	$orders = BASE_NUM_SALES + rand(1,SALES_RAND_FACTOR) - rand(1,SALES_RAND_FACTOR);
	print "working on ". strftime('%D',$i) . " $orders orders<br>";
	flush();
	for ($k=0; $k<$orders; $k++) {
		$sale = new sale($i);
	}
}

print "Done!";

class sale {
	var $_state;
	var $_channel;
	var $_item;
	var $_qty;
	var $_rev;
	
	function sale($dt) {
		global $itemInfo;
		
		//set channel
		$r = rand(1,100);
		if ($r < 20) {
			$this->_channel = 2; //phone
		} elseif ($r < 40 + strftime('%m',$dt)*2 ) {
			$this->_channel = 1; //web eating into retail
		} else {
			$this->_channel = 3; //retail
		}
		
		$this->_item = rand(1,5);
		$this->_qty = $itemInfo[$this->_item]['qty'] 
					+ $itemInfo[$this->_item]['inc'] * rand(1, $itemInfo[$this->_item]['rnd'])
					- $itemInfo[$this->_item]['inc'] * rand(1, $itemInfo[$this->_item]['rnd']);
		if ($this->_qty < 1) {
			$this->_qty = 1;
		}
		
		$this->_rev = $this->_qty * $itemInfo[$this->_item]['price'];
		
		$this->_pick_state();
		
		$this->_log_sale($dt);
	}
	
	function _pick_state() {
		global $stateData;
		
		$pick = rand (1,10000);
		$last = $stateData[0]['state'];
		for ($i=0,$j=count($stateData); $i<$j; $i++) {
			$last = $stateData[$i]['state'];
			if ($pick <= $stateData[$i]['value']) {
				break;
				//$i=$j;
			}
		}
		$this->_state = $last;
	}
	
	function _log_sale($dt) {
		global $conn;

		$conn->Execute(INS_SQL, array(strftime('%Y-%m-%d',$dt), $this->_channel, $this->_item, $this->_state, $this->_qty, $this->_rev));
	}
}

?>
