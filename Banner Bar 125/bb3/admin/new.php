<?php
session_start();
require_once ('inc/check.php');
require_once('inc/connect.ini.php');
require_once('inc/functions.ini.php');
/*
new.php
Allows user to create a new entry in the database
*/
//show the form
function renderForm($imagepop, $link, $alt, $timer, $error, $xClick)
{
    //getting the form from our new.html file
    include('inc/new.ini.html');
}
// check if the form has been submitted. If it has, start to process the form and save it to the database
if (isset($_POST['submit'])) {
    // get form data, making sure it is safe before we think about putting it into the database
     $imagepop = $_POST['imagepop'];
        $link  = $_POST['link'];
        $alt   = $_POST['alt'];
        $timer = $_POST['timer'];
$xClick = $_POST['xClick'];
    // check to make sure all fields are entered
    if ($imagepop == '' || $link == '' || $alt == '') {
        if ($imagepop == '' || $alt == '' || $link == '')
            $error = "ERROR: 1 or more required fields missing";
        if ($imagepop == '' && $alt == '' && $link == '')
            $error = "ERROR: Please fill in all required fields!";
        // if any field is blank, display the form again
        renderForm($imagepop, $link, $alt, $timer, $error, $xClick);
    } else {
        /*
        save the data to the database where image,link and alt are taken from the form fields.  CURDATE gives us the current date like 2011-05-29
        */
        $choice = explode(':', $_POST['timer']);
        $left = $choice[0];
        $right = $choice[1];
//$d = date('d-m-Y H:i:s');

        if (($left == "10") && ($right == "year")) {
            $conn->query("INSERT $t_banners SET image='$imagepop', link='$link', alt='$alt', timer='Not Set', impressions='0', clicks='0', xClick='$xClick' ,expired='no', expires=NULL, creationdate=NOW()");
            if (isset($_SESSION['upload']))
                unset($_SESSION['upload']);
            header("Location: index.php");
        } else {
           if(($left == "7") && ($right == "day")){
$left = "7";
$right = "day";
$sTime = "1 week";
}

elseif($left == "12"){
$sTime = "12 hours";
$left = "12";
$right = "hour";
}

elseif(($left =="1")&&($right =="day")){
$left = $left;

$right = "day";
$sTime = $left." day";

}

elseif(($left >="2" && $left <="6")&&($right =="day")){
$left = $left;

$right = "day";
$sTime = $left." days";

}
elseif(($left =="1")&&($right =="month")){
$left = $left;

$right = "month";
$sTime = $left." month";

}
elseif($left >="2" && $left <="11" && $right =="month"){
$sTime = $left." months";
}

elseif($left =="1" && $right =="year"){
$sTime = $left." year";
}


elseif($left =="Not" && $right =="Set"){
$sTime = "unlimited";
$left = "10";
$right = "year";}
          $conn->query("INSERT $t_banners SET image='$imagepop', link='$link', alt='$alt', timer='$sTime', impressions='0', clicks='0', xClick='$xClick', expired='no', expires=DATE_ADD(NOW(), INTERVAL $left $right), creationdate='$d'");
            // once saved, redirect back to the index page
            if (isset($_SESSION['upload']))
                unset($_SESSION['upload']);
            header("Location: index.php");
        }
    }
} else {
    // if the form hasn't been submitted, display the form
    renderForm('', '', '', '', '', '');
}
// if the alert session has been set show it then unset the session to stop it showing again until we need it too
if (isset($_SESSION['alert'])) {
    $msg2 = $_SESSION['alert'];
    echo "<script>alert('$msg2');</script>";
    unset($_SESSION['alert']);
}
?>