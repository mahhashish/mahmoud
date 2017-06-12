<?php
require_once('inc/connect.ini.php');
require_once('inc/functions.ini.php');
  //$user = $_GET['user'];
  //$kt = $_GET['kt'];

$user = isset( $_GET['user'] )? $_GET['user']: false;
$kt = isset( $_GET['kt'] )? $_GET['kt']: false;


if (($user) && ($kt)) {
//if ((isset($_GET['user']) && isset($_GET['kt']))) {




//

 $query  = "SELECT email,txn_id FROM $t_payments WHERE email='$user' AND txn_id='$kt' LIMIT 1";
        $result = $conn->query($query);
        if ($result != false) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
           $kt = $pRow['txn_id'];
$email  = $pRow['email'];
            } 
        } 
else {
echo "Record not found";
}
//
//
}
else {

echo "No User details given";
exit;
}

?>

<link rel="stylesheet" type="text/css" href="<?php echo $website."/".$location;?>/admin/style.css">
<style type="text/css">
/*<![CDATA[*/
table.c2
{
  background-color:#CCC
}
label
{
  display:block;
  float:left;
  margin-right:.4em;
  text-align:right;
  width:2em
}
#container
{
  margin-left:auto;
  margin-right:auto;
  text-align:left;
  width:1004px
}
.right
{
  float:right
}
.error
{
  border:2px solid red;
  margin-left:auto;
  margin-right:auto;
  width:500px
}
body,td.c1,th.c1,.center
{
  text-align:center
}
.c125
{
  height:125px;
  width:125px

}
.c170
{
  width:170px

}
.c100
{
  width:100px
}
/*]]>*/
</style>
<div id="container">
<form id="form1" name="form1" method="post" action="">
<table class="c2" width="1000" border="0" cellpadding="3" cellspacing="1" id="T2">
<tr>
<th class="c1" align="center" colspan="9">Your Banner Statistics</th>
</tr>
<tr>
<th class="c1">Banner</th>
<th class="c1">URL</th>
<th class="c1">Alt</th>
<th class="c1">Impressions</th>
<th class="c1">Clicks</th>
<th class="c1">CTR</th>
<th class="c1">Period</th>
<th class="c1">Created</th>
<th class="c1">Expires</th>


</tr>
<tr>


  <?php

$result1   = "SELECT * FROM $t_client_banners WHERE txn_id='$kt' LIMIT 1";
foreach ($conn->query($result1) as $row) {
    $r0       = $row[0]; // id
    $r1       = $row[2]; // image
    $r2       = $row[4]; // link
    $r3       = $row[3]; // Alt text
    $r4       = $row[5]; // impressions
    $r5       = $row[6]; // clicks
    $r7       = $row[7]; // period

    $r8       = $row[9]; // created
    $r9       = $row[8]; // timer
    $r10       = $row[10]; // expires
}
?>

<td class="c1"><img src="<?php echo $r1; ?>" alt="<?php echo $row[3] ?>"/></td>
<td class="c1"><?php echo $row[4] ?></td>
<td class="c1"><?php echo $row[3] ?></td>
<td class="c1"><?php echo $row[5] ?></td>
<td class="c1"><?php echo $row[6] ?></td>
<td class="c1"><?php print ctr($r5, $r4); ?></td>
<td class="c1"><?php echo $row[7] ?></td>
<td class="c1"><?php echo $row[9] ?></td>
<td class="c1"><?php echo $row[10] ?></td>
</tr>