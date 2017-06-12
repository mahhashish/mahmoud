<?php
if (!isset($_SESSION)) session_start();
require_once ('./inc/paginator.class.php');
require_once('./inc/connect.ini.php');
require_once('./inc/functions.ini.php');
define('Allow', '1');
require_once('./inc/header.ini.php');
$per_page = $conn->query("SELECT per_page FROM $t_settings")->fetchColumn();
?>
<title>Client Banners</title>
</head>
<body>
<div id="container">
<div id="top">
<span class="left">
<a href="index.php" class="button style1"><b class="gradient"></b><span>Home</span></a>
<a href="../advertise.php" class="button style1"><b class="gradient"></b><span>Client Form</span></a>
</span>
<span class="right">
<a href="login/L-logout.php" class="button style1"><b class="gradient"></b><span>Logout</span></a></span></div>
<?php
if (isset($_SESSION['alert'])) {
    $msg = $_SESSION['alert'];
    echo "<script>alert('$msg');</script>";
    unset($_SESSION['alert']);
}
$dir = 0;
echo '<p class="center">Total clicks:&nbsp;<span class="bold">', $conn->query("SELECT SUM(clicks) 
    FROM $t_client_banners")->fetchColumn(), '</span>';
echo '&nbsp;-&nbsp;Total banners:&nbsp;<span class="bold">', $conn->query("SELECT count(*) FROM $t_client_banners")->fetchColumn(), '</span>';
echo '&nbsp;-&nbsp;Total impressions:&nbsp;<span class="bold">', $conn->query("SELECT SUM(impressions) FROM $t_client_banners")->fetchColumn(), '</span>';
$a = $conn->query("SELECT clicks,image FROM $t_client_banners ORDER BY clicks DESC 
    LIMIT 1");
while ($row = $a->fetch(PDO::FETCH_NUM)) {
    echo '<br />Most Popular with&nbsp;<span class="bold">', $row[0], '</span>
    &nbsp;click(s)&nbsp;<a href="', $row[1], '" title="Opens in New Window" 
    onclick="window.open(this.href);return false;"><span class="external">
    View Banner</span></a></p>';
}
$num_rows           = $conn->query("SELECT count(*) FROM $t_client_banners")->fetchColumn();
echo "<span class=\"right\">";
$pages              = new Paginator;
$pages->items_total = $num_rows;
$pages->items_per_page = $per_page;
$pages->mid_range   = 9; // Number of pages to display. Must be odd and > 3
$pages->paginate();
echo $pages->display_pages();
echo "</span>";
if (isset($_GET["order"])) {
    $order           = $_GET["order"];
    $allowed_columns = array(
        'id',
        'impressions',
        'clicks',
        'lastClick',
        'timer',
        'expires',
        'creationDate'
    );
    if (in_array($order, $allowed_columns)) {
        $_SESSION['order'] = $order;
    } else {
        $_SESSION['order'] = "id";
        die("<p>That is not allowed!</p>");
    }
}
if (isset($_SESSION['order'])) {
    $order = $_SESSION['order'];
} elseif (!isset($_SESSION['order'])) {
    $order = "id";
}
$getDir = isset( $_GET['dir'] )? $_GET['dir']: false;

if ($getDir == 'DESC') {
    $dir             = 'DESC';
    $_SESSION['dir'] = "DESC";
    $dir_link        = 'ASC';
} else {
    $dir             = 'ASC';
    $_SESSION['dir'] = "ASC";
    $dir_link        = 'DESC';
}
$page_link = $_SESSION['dir'];
?>
<table width="1000" border="0" cellspacing="1" cellpadding="0">
<tr>
<td>
<form id="form1" method="post" action="">
<table class="c2" width="1000" border="0" cellpadding="3" cellspacing="1" id="T2">
<tr>
<th class="c1" align="center" colspan="9">Banner Bar 125 Control Panel</th>
</tr>
<tr>
<th class="c1">Banner</th>
<th class="c1">Info</th>
<th class="c1"><?php echo '<a href="client.php?order=impressions&amp;dir='.$dir_link.'">Impressions</a>'; ?></th>
<th class="c1"><?php echo '<a href="client.php?order=clicks&amp;dir='.$dir_link.'">Clicks</a>'; ?></th>

<th class="c1">CTR</th>
<th class="c1"><?php echo '<a href="client.php?order=timer&amp;dir='.$dir_link.'">Period</a>'; ?></th>
<th class="c1"><?php echo '<a href="client.php?order=creationDate&amp;dir='.$dir_link.'">Created</a>'; ?></th>
<th class="c1"><?php echo '<a href="client.php?order=expires&amp;dir='.$dir_link.'">Expires</a>'; ?></th>
<th class="c1">Status</th>

</tr>

<?php
$orders  = array("id","impressions","clicks","lastClick", "timer","expires","created"); //field names
$getOrder = isset( $_GET['order'] )? $_GET['order']: false;

$key     = array_search($getOrder,$orders); // see if we have such a name
$orderby = $orders[$key]; //if not, first one will be set automatically. smart enuf :)
$dir  = array("ASC","DESC");
$key     = array_search($getDir,$dir);
$direction = $dir[$key];
if($num_rows != 0){
$sql   = "SELECT * FROM $t_client_banners ORDER BY $orderby $direction $pages->limit";



foreach ($conn->query($sql) as $row) {
    $r0       = $row[0]; // id
    $r1       = $row[1]; // txn_id
    $r2       = $row[2]; // image
    $r3       = $row[3]; // Alt text
    $r4       = $row[4]; // url
    $r5       = $row[5]; // impressions
    $r6       = $row[6]; // clicks

    $r7       = $row[7]; // period
    $r8       = $row[8]; // status
    $r9       = $row[9]; // created
    $r10       = $row[10]; // expires

    $mystring = <<<EOT
<tr>
<td class="c1 c125">
<div id="banImgId_$r0" style="background-image:url('$r2');width:125px;height:125px"></div>
EOT;
    print $mystring;
    //pause
    $mystring = <<<EOT
</td>
<td class="c1 c170">
<p><label for="link_$r0">Link</label> <input type="text" name="link" id="link_$r0" value="$r4" size="18" /></p>
<p><label for="alt_$r0">Alt</label><input type="text" name="alt" id="alt_$r0" value="$r3" size="18" /></p><p class="center"><a href="$r4" title="Opens in New Window" onclick="window.open(this.href);return false;"><span class="external">Visit</span></a></p>
</td>
<td class="c1">$r5
</td>
<td class="c1">$r6
</td>

<td class="c1">
EOT;
    print $mystring;
    //clicks, impressions
    print ctr($r6, $r5);
    $mystring1 = <<<EOT

</td>
<td class="c1">$r7
</td>
<td class="c1 c100">$r9
EOT;
    print $mystring1;
    //expires, expired
   // print expiry($r10, $r11);
    $mystring2 = <<<EOT

</td>
<td class="c1 c100">$r10
</td>


<td class="c1">

EOT;
    print $mystring2;


      if ($r8 == 'p') {
$pageN = 0;
if ($pageN == "") {
				$pageN = "1";
}
echo '<p class="center"><a href="status.php?status=approve&id=' . $r0 . '&page='.$pageN.'" title="Edit Banner">Approve</a><br /><br /><a href="status.php?status=deny&id=' .$r0 . '&page='.$pageN.'" title="Edit Banner">Deny</a></p>';

}
elseif ($r8 == "deny") {
echo "Denied";
}
else {
echo "Active";
}


     $mystring3 = <<<EOT
</td>

</tr>
EOT;
    print $mystring3;
       }

}
//$conn = null;

?>

</table>
</form>
</td>
</tr>
</table>
<div class="right">
<?php
echo $pages->display_pages();
//echo "<span class=\"paginate\">Page: $pages->current_page of $pages->num_pages</span>\n";
?>
</div>
</div>
<?php
require_once('./inc/footer.ini.php');
?>