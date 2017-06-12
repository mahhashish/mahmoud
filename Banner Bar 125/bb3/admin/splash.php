<!-- Start Banner Bar -->
<div id="switch">l</div>
<div id="panel">
<?php
require_once('inc/connect.ini.php');
require_once('inc/functions.ini.php');
print_banners($t_client_banners);
print_banners($t_banners);
?>
<div id="credit">Powered by <a href="http://ianjgough.com/php/banner-bar-125/">Banner Bar 125</a></div>
</div>
<?php
$display_count_limit  = $conn->query("SELECT display_count FROM $t_settings")->fetchColumn();
$new_window  = $conn->query("SELECT new_window FROM $t_settings")->fetchColumn();
$bar_width = $display_count_limit * "130" + "40";    // Banner bar width
echo "<script type=\"text/javascript\">$(document).ready(function() {";
if ($new_window) {
echo "$(\"#panel a[href^='http://']\").attr(\"target\",\"_blank\");";
}
echo "$(\"#panel\").width($bar_width);});</script>\n";
?>
<!-- End Banner Bar -->