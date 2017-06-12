<?php
require_once ('inc/check.php');
require_once('inc/connect.ini.php');
require_once('inc/functions.ini.php');

function renderForm($id, $image, $link, $alt, $timer, $xClick, $error)
{
    include('inc/edit.ini.html');
}
if (isset($_POST['submit'])) {
    if (is_numeric($_POST['id'])) {
        $id    = $_POST['id'];
        $image = $_POST['image'];
        $link  = $_POST['link'];
        $alt   = $_POST['alt'];
        $timer = $_POST['timer'];
$xClick = $_POST['xClick'];

        if ($image == '' || $link == '' || $alt == '') {
            $error = 'ERROR: Please fill in all required fields!';
            renderForm($id, $image, $link, $alt, $timer, $error, $xClick);
        } //$image == '' || $link == '' || $alt == ''
        else {
            $choice = explode(':', $_POST['timer']);
            $left   = $choice[0];
            $right  = $choice[1];
            if (($left == "10") && ($right == "year")) {
 $conn->query("UPDATE $t_banners SET image='$image', link='$link', alt='$alt', timer='Not Set', xClick='$xClick', expires=NULL, expired='no' WHERE id='$id'");
                $page = $_GET['page'];
                header("Location: index.php?page=$page");
            } //($left == "10") && ($right == "year")
            else {
                if (($left == "7") && ($right == "day")) {
                    $left  = "7";
                    $right = "day";
                    $sTime = "1 Week";
                } //($left == "7") && ($right == "day")
                elseif ($left == "12") {
                    $sTime = "12 hours";
                    $left  = "12";
                    $right = "Hour";
                } //$left == "12"
                    elseif (($left == "1") && ($right == "day")) {
                    $left  = $left;
                    $right = "day";
                    $sTime = $left . " Day";
                } //($left == "1") && ($right == "day")
                    elseif (($left >= "2" && $left <= "6") && ($right == "day")) {
                    $left  = $left;
                    $right = "day";
                    $sTime = $left . " Days";
                } //($left >= "2" && $left <= "6") && ($right == "day")
                    elseif (($left == "1") && ($right == "month")) {
                    $left  = $left;
                    $right = "month";
                    $sTime = $left . " Month";
                } //($left == "1") && ($right == "month")
                    elseif ($left >= "2" && $left <= "11" && $right == "month") {
                    $sTime = $left . " Months";
                } //$left >= "2" && $left <= "11" && $right == "month"
                    elseif ($left == "1" && $right == "year") {
                    $sTime = $left . " Year";
                } //$left == "1" && $right == "year"
                    elseif ($left == "Not" && $right == "Set") {
                    $sTime = "unlimited";
                    $left  = "10";
                    $right = "year";
                } //$left == "Not" && $right == "Set"
                if (($timer != $sTime) && ($timer != "Not Set")) {
                    $conn->query("UPDATE $t_banners SET clicks='0', impressions='0' WHERE id='$id'");
                } //($timer != $sTime) && ($timer != "Not Set")
                $conn->query("UPDATE $t_banners SET image='$image', link='$link', alt='$alt', timer='$sTime', xClick='$xClick', expired='no', expires=DATE_ADD(NOW(), INTERVAL $left $right) WHERE id='$id'");
                $page = $_GET['page'];
                header("Location: index.php?page=$page");
            }
        }
    } //is_numeric($_POST['id'])
    else {
        echo 'Error invalid ID!';
    }
} //isset($_POST['submit'])
else {
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
        $id     = $_GET['id'];
        $query  = "SELECT * FROM $t_banners WHERE id=$id";
        $result = $conn->query($query);
        if ($result != false) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $image = $row['image'];
                $link  = $row['link'];
                $alt   = $row['alt'];
                $timer = $row['timer'];
 $xClick = $row['xClick'];
                renderForm($id, $image, $link, $alt, $timer, $xClick,'');
            } //$row = $result->fetch(PDO::FETCH_ASSOC)
        } //$result != false
        else {
            echo "No results found!";
        }
    } //isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0
    else {
        echo 'Error invalid ID or No ID Found!';
    }
}
?>