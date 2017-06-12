<?php
if (isset($_SERVER["REMOTE_ADDR"]) && ($_SERVER["REMOTE_ADDR"] != $_SERVER["SERVER_ADDR"])) {
    exit('Permission Denied!<br />This Script Can Only Be Run Locally!');
} //isset($_SERVER["REMOTE_ADDR"]) && ($_SERVER["REMOTE_ADDR"] != $_SERVER["SERVER_ADDR"])
require_once('inc/connect.ini.php');
function check_banners($table)
{
    global $conn, $t_settings, $t_client_banners, $t_banners;
    $cron = $conn->query("SELECT cron FROM $t_settings")->fetchColumn();
    if ($cron == 0) {
        $query  = "UPDATE $t_settings SET cron='1'";
        $result = $conn->query($query);
    } //$cron == 0
    if ($table == $t_client_banners) {
        $query     = "UPDATE $t_client_banners SET status='e' WHERE expires <= NOW()";
        $whoBanner = "client";
    } //$table == $t_client_banners
    else {
        $query     = "UPDATE $t_banners SET expired='yes' WHERE expires <= NOW()";
        $whoBanner = "of your";
    }
    $result       = $conn->query($query);
    $affectedRows = $result->rowCount();
    echo "Total " . $whoBanner . " banner(s) expired = " . $affectedRows . "\r\n";
}
check_banners(banners);
check_banners(client_banners);
?>