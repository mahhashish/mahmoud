<?php
if (!isset($_SESSION)) session_start();

require_once ('inc/check.php');
require_once ('./inc/paginator.class.php');
require_once('inc/connect.ini.php');
require_once('inc/functions.ini.php');
define('Allow', '1');
require_once('inc/header.ini.php');
$per_page = $conn->query("SELECT per_page FROM $t_settings")->fetchColumn();
?>

<title>Banner Bar Control Panel</title>

<script type="text/javascript" src="inc/modal.js"></script>
<script type= "text/javascript">
//<![CDATA[
$(document).ready(function() {
    // Toggle click the boxes on each row
    $('#T2 tr').filter(':has(:checkbox:checked)').addClass('selected').end().click(function(event) {
        $(this).toggleClass('selected');
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).attr('checked', function() {
                return !this.checked;
            });
        }
    });
    $('#selectall').click(function() {
        $('.checkAll').prop('checked', isChecked('selectall'));
    });
});

function isChecked(checkboxId) {
    var id = '#' + checkboxId;
    return $(id).is(":checked");
}

function resetSelectAll() {
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
    if ($(".checkAll").length == $(".checkAll:checked").length) {
        $("#selectall").attr("checked", "checked");
    } else {
        $("#selectall").removeAttr("checked");
    }
}
//]]>
</script>
</head>
<body>
<div id="container">
<div id="top">
<span class="left">
<a href="new.php" class="button style1"><b class="gradient"></b><span>Add New</span></a>
<a href="#setForm" class="button style1 modalInput" rel="leanModal"><b class="gradient"></b><span>Settings</span></a>
<a href="javascript:backup();" class="button style1"><b class="gradient"></b><span>Backup Database</span></a>
</span>
<span class="right">
<a href="client.php" class="button style1"><b class="gradient"></b><span>Client Banners</span></a>
<a href="login/L-logout.php" class="button style1"><b class="gradient"></b><span>Logout</span></a></span></div>
<?php
if (isset($_SESSION['alert'])) {
    $msg = $_SESSION['alert'];
    echo "<script>alert('$msg');</script>";
    unset($_SESSION['alert']);
}
$cron = $conn->query("SELECT cron FROM $t_settings")->fetchColumn();
if ($cron == 0) {
    $errorM       = true;
    $errorLink    = "http://ianjgough.com/wp-content/demos/bannerbar/instructions/#section9";
    $errorMessage = "Setup Cron Job for the timer(s) to work.";
}
if ($sandbox == 1) {
    $errorM       = true;
    $errorLink    = $website."/".$location."/admin/inc/config.ini.php";
    $errorMessage = "Paypal Sandbox is active (Edit config.ini.php to disable)";
}
if ($errorM) {
    echo "<div class=\"center\"><p><a href=\"" . $errorLink . "\" class=\"redText\">" . $errorMessage . "</a></p></div>";
}
?>


<div class="center greenText" id="message"></div>

<?php
echo '<p class="center">Total clicks:&nbsp;<span class="bold">', $conn->query("SELECT SUM(clicks) 
    FROM $t_banners")->fetchColumn(), '</span>';
echo '&nbsp;-&nbsp;Total banners:&nbsp;<span class="bold">', $conn->query("SELECT count(*) FROM $t_banners")->fetchColumn(), '</span>';
echo '&nbsp;-&nbsp;Total impressions:&nbsp;<span class="bold">', $conn->query("SELECT SUM(impressions) FROM $t_banners")->fetchColumn(), '</span>';
$a = $conn->query("SELECT clicks,image FROM $t_banners ORDER BY clicks DESC 
    LIMIT 1");
while ($row = $a->fetch(PDO::FETCH_NUM)) {
    echo '<br />Most Popular with&nbsp;<span class="bold">', $row[0], '</span>
    &nbsp;click(s)&nbsp;<a href="', $row[1], '" title="Opens in New Window" 
    onclick="window.open(this.href);return false;"><span class="external">
    View Banner</span></a></p>';
}
$num_rows           = $conn->query("SELECT count(*) FROM $t_banners")->fetchColumn();
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
'xClick',
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
<form id="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="c2" width="1000" border="0" cellpadding="3" cellspacing="1" id="T2">
<tr>
<th class="c1" align="center" colspan="12">Banner Bar 125 Control Panel</th>
</tr>
<tr>
<th class="c1"><input type="checkbox" id="selectall" name="checkbox" /></th>
<th class="c1">Banner</th>
<th class="c1">Info</th>
<th class="c1"><?php echo '<a href="index.php?order=impressions&amp;dir='.$dir_link.'">Impressions</a>'; ?></th>
<th class="c1"><?php echo '<a href="index.php?order=clicks&amp;dir='.$dir_link.'">Clicks</a>'; ?></th>
<th class="c1"><?php echo '<a href="index.php?order=lastClick&amp;dir='.$dir_link.'">Last Click</a>'; ?></th>
<th class="c1">CTR</th>
<th class="c1"><?php echo '<a href="index.php?order=xClick&amp;dir='.$dir_link.'">xClick</a>'; ?></th>
<th class="c1"><?php echo '<a href="index.php?order=timer&amp;dir='.$dir_link.'">Timer</a>'; ?></th>
<th class="c1"><?php echo '<a href="index.php?order=expires&amp;dir='.$dir_link.'">Expires</a>'; ?></th>
<th class="c1"><?php echo '<a href="index.php?order=creationDate&amp;dir='.$dir_link.'">Created</a>'; ?></th>
<th class="c1">E</th>
</tr>

<?php
$orders  = array("id","impressions","clicks","lastClick","xClick","timer","expires","created"); //field names
$getOrder = isset( $_GET['order'] )? $_GET['order']: false;

$key     = array_search($getOrder,$orders); // see if we have such a name
$orderby = $orders[$key]; //if not, first one will be set automatically. smart enuf :)
$dir  = array("ASC","DESC");
$key     = array_search($getDir,$dir);
$direction = $dir[$key];
if($num_rows != 0){

$sql   = "SELECT * FROM $t_banners ORDER BY $orderby $direction $pages->limit";
foreach ($conn->query($sql) as $row) {
    $r0       = $row[0]; // id
    $r1       = $row[1]; // image
    $r2       = $row[2]; // link
    $r3       = $row[3]; // Alt text
    $r4       = $row[4]; // impressions
    $r5       = $row[5]; // clicks
    $r6       = $row[6]; // xClick
    $r7       = $row[7]; // Last click
    $r8       = $row[8]; // pause
    $r9       = $row[9]; // created
    $r10       = $row[10]; // timer
    $r11       = $row[11]; // expires
    $r12      = $row[12]; // expired


    $mystring = <<<EOT
<tr>

<td class="c1"><input type='checkbox' name='checkbox[]' id='checkbox$r0'  class='checkAll' value="$r0" /></td>
<td class="c1 c125">
<div id="banImgId_$r0" style="background-image:url(' $r1');width:125px;height:125px;cursor:pointer">
EOT;
    print $mystring;
    //pause
    print pause($r8, $r0);
    $mystring = <<<EOT
</td>
<td class="c1 c170">
<p><label for="link_$r0">Link</label> <input type="text" name="link" id="link_$r0" value="$r2" size="18" /></p>
<p><label for="alt_$r0">Alt</label><input type="text" name="alt" id="alt_$r0" value="$r3" size="18" /></p><p class="center"><a href="$r2" title="Opens in New Window" onclick="window.open(this.href);return false;"><span class="external">Visit</span></a></p>
</td>
<td class="c1">$r4
</td>
<td class="c1">$r5
</td>
<td class="c1">
EOT;
    print $mystring;

if (is_null($r7)) {
    echo "None yet";
} //is_null($r7)
else {
dateOrder($row["7"]);
}

 $mystringa = <<<EOT
</td>
<td class="c1">
EOT;
    print $mystringa;
    //clicks, impressions
    print ctr($r5, $r4);
    
$mystringb = <<<EOT
</td>
<td class="c1">
EOT;
    print $mystringb;

if (is_null($r6)) {
    echo "Not set";
} //is_null($r6)
else {
    echo $r6;
}

$mystring1 = <<<EOT
</td>
<td class="c1">$r10
</td>
<td class="c1 c100">
EOT;
    print $mystring1;
    //expires, expired
    print expiry($r11, $r12);
    $mystring2 = <<<EOT
</td>
<td class="c1 c100">
EOT;
    print $mystring2;
dateOrder($row["9"]);
 $mystring3 = <<<EOT
</td>
<td class="c1">
<a href="edit.php?id=$r0&amp;page=$pages->current_page" title="Edit Banner">E</a>
</td>
</tr>

EOT;
    print $mystring3;
       }

if (isset($_POST['reset'])) {
    while (list($key, $val) = each($_POST['checkbox'])) {
        $count = $conn->prepare("UPDATE $t_banners SET impressions = '0', clicks='0', xClick=NULL, lastClick=NULL WHERE id='$val'");
        $count->execute();
        $no = $count->rowCount();
        echo " No of records = " . $no;
    }
    if ($no > 0) {
        echo '<meta http-equiv="refresh" content="0;URL=index.php">';
    }
}
if (isset($_POST['delete'])) {
        while (list($key, $val) = each($_POST['checkbox'])) {
            $imageDelete   = $conn->query("SELECT image FROM $t_banners WHERE id='$val'")->fetchColumn();
            $imagePath     = parse_url($imageDelete, PHP_URL_PATH);
            $realImagePath = $_SERVER{'DOCUMENT_ROOT'} . $imagePath;
            if (file_exists($realImagePath)) {
                unlink($realImagePath);
            }
            $count = $conn->prepare("DELETE FROM $t_banners WHERE id='$val'");
            $count->execute();
            $no = $count->rowCount();
            echo " No of records = " . $no;
        }
        if ($no > 0) {
            echo '<meta http-equiv="refresh" content="0;URL=index.php">';
        }
    }
}
?>

<tr>
<th class="c1" colspan="12" align="center">
<div class="center">
<input type="submit" value="Delete" class="button style1" name="delete" id="delete" onclick="return confirm('Are you sure you want to Delete these banners?')" />
<input type="submit" value="Reset" class="button style1" name="reset" id="reset" onclick="return confirm('Are you sure you want to Reset the Stats for these banners?')" />
</div>
</th>
</tr>
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

<script type="text/javascript">
//<![CDATA[
function pausep(id, state) {
    a = "banImgId_"; // part 1 of image id
    b = a + id; // join part 1 with the id we sent to the function
    $.ajax({
        type: "GET",
        url: "inc/pause.ini.php",
        data: "c=" + id + "&d=" + state ,
        // include variables as if in url e.g. index.php?c=17&state=no
        success: function (resp) {
            document.getElementById(b).innerHTML = resp; //update innerHTML with response
        },
        error: function (e) {
            alert('Error: ' + e); // if we get an error
        }
    });
}
function backup() {
    $.ajax({
        type: "GET",
        url: "inc/backup.php",
        success: function (resp) {
            document.getElementById('message').innerHTML = resp;
        },
        error: function (e) {
            alert('Error: ' + e);
        }
    });
}
//]]>
</script>
<?php 
require_once('inc/settings.ini.php'); 
require_once('inc/footer.ini.php');
?>