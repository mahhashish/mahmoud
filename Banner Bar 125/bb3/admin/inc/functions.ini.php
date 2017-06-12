<?php
/**
 * Splash page
 *
 */
function print_banners($table)
{

    global $conn, $t_settings, $t_client_banners, $total, $website, $location;

    $result               = $conn->query("SELECT * FROM $t_settings");
    $row                  = $result->fetch(PDO::FETCH_ASSOC);
    $display_count_limit  = $row['display_count']; // Total banners to display
    $client_banners_limit = $row['client_banners']; // Total client banners to display

    if ($table == $t_client_banners) {
        $query   = "SELECT * FROM $table WHERE status='active' LIMIT $client_banners_limit";
        $result1 = $conn->query($query);
        $count   = $result1->rowCount();
        $total   = $display_count_limit - $count;
    } else {
        $query   = "SELECT * FROM $table WHERE expired='no' ORDER BY rand() LIMIT $total";
        $result1 = $conn->query($query);
    }
    if ($result1 != false) {
        while ($row = $result1->fetch(PDO::FETCH_ASSOC)) {
            print "<a href='" . $website."/".$location . "/go.php?id=" . $row["id"] . "&amp;ad=" . $table ."'><img src='" . $row["image"] . "' alt='" . $row["alt"] . "' /></a>";
            $BanID    = $row['id'];
            $BanViews = $row['impressions'];
            $newViews = $BanViews + 1;
            $conn->query("UPDATE $table SET impressions = $newViews WHERE id = $BanID");
        }
    }
    $result  = null;
    $result1 = null;
}
/**
 * Click Through Rate
 *
 */
function ctr($r5, $r4)
{
    $var1 = $r5;
    $var2 = $r4;
    //if impressions are not equal to 0 divide clicks by impressions and times by 100 then show only the first 5 characters including the .
    if ($var2 != 0) {
        $string = $var1 / $var2 * 100;
        $res    = substr($string, 0, 4);
        return $res . "%";
    } else {
        //if impressions are equal to 0 echo 0
        return '0';
    }
}
/**
 * Expiry or Expired
 *
 */
function expiry($r11, $r12)
{
    $never   = $r11;
    $expired = $r12;
    if (is_null($never)) {
        echo "Never";
    } //is_null($never)
    elseif ($expired == "yes") {
        echo '<img src="img/expired.png" width="100px" height="100px" alt="expired" /><br>Expired';
    } //$expired == "yes"
    else {
//
$timestamp = strtotime($r11);

$dOrder = date("d-m-Y H:i:s", $timestamp); 
 echo $dOrder;

//
       
    }
}
/**
 * Paused
 *
 */
function pause($r8, $r0)
{
    $status = $r8;
    if ($status == "yes")
        echo '<a href="javascript:pausep(\'' . $r0 . '\',\'yes\');"><img src="img/pause.png" width="125px" height="125px" alt="pause" /></a></div>';
    else
        echo '<a href="javascript:pausep(\'' . $r0 . '\',\'no\');"><img src="img/blank.png" width="125px" height="125px" alt="blank" /></a></div>';
}
/**
 * Version Checker
 *
 */
function check_version()
{
    global $version, $email, $conn, $t_settings, $headers;
    $REMOTE_VERSION_URL = "http://ianjgough.com/wp-content/demos/bannerbar/version.txt";
    if (function_exists('curl_init')) { // if cURL is available, use it...
        $ch = curl_init($REMOTE_VERSION_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $REMOTE_VERSION = curl_exec($ch);
        curl_close($ch);
    } //function_exists('curl_init')
    else {
        $REMOTE_VERSION = file_get_contents($REMOTE_VERSION_URL); // ...if not, use the common file_get_contents()
    }
    if (version_compare($version, $REMOTE_VERSION, '<')) {
        $update_alert = $conn->query("SELECT update_alert FROM $t_settings")->fetchColumn();
        if ($update_alert == 0) {
            $to      = $email;
            $subject = "Banner Bar update available";
            $message = "
<html>
<head>
<title>Banner Bar Update</title>
</head>
<body>
<p>Just to inform you there is an update to Banner Bar available for download<br />Please visit <a href='http://ianjgough.com/php/banner-bar-125/'>Banner Bar homepage</a> for more details.
<br />Thanks,<br />
Ian<br />
<a href='http://ianjgough.com/'>ianjgough.com</a>
</p>
</body>
</html>
";
           
            mail($to, $subject, $message, $headers);
            $update_alert = $conn->query("SELECT update_alert FROM $t_settings")->fetchColumn();
            if ($update_alert == 0) {
                $query  = "UPDATE $t_settings SET update_alert='1'";
                $result = $conn->query($query);
            } //$update_alert == 0
        } //$update_alert == 0
        return "Version " . $version . "&nbsp;&#124;&nbsp;<a href=\"http://ianjgough.com/wp-content/demos/bannerbar/loop.php\" title=\"Opens in New Window\" onclick=\"window.open(this.href);return false;\"><span class=\"external\">New Version Available</span></a>";
    } //version_compare($version, $REMOTE_VERSION, '<')
    else {
        return "Version " . $version;
    }
}
/**
 * Date Order
 *
 */
function dateOrder($row)
{

$timestamp = strtotime($row);

$dOrder = date("d-m-Y H:i:s", $timestamp); 
 echo $dOrder;

}
?>