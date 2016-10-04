<?php
// sets the user name and the title
$usr = '';  $titlusr = 'User';
if(isset($_GET['usr'])) {
  // if value from 'usr' is key in $allusrpg (defined in 'config.php'), sets data for All-Users page, else, for an User Page
  if(array_key_exists($_GET['usr'], $allusrpg)) {
    $usr = $allusrpg[$_GET['usr']];
    $titlusr = $lsite['allusr']. (($_GET['usr'] == '0') ? ' ( '.$objUsers->users['total'].' )' : $usr);
  }
  else {
    $usr = $_GET['usr'];
    $titlusr = $lsite['userpage']['title']. $usr;
  }
}

// the html code for the <head> area
$htmlhead = '<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>'.$titlusr.' - coursesweb.net</title>
<meta name="description" content="'.$titlusr.' - coursesweb.net, Free courses, register, login PHP - AJAX" />
<meta name="keywords" content="'.$usr.', users, register, login" />
<meta name="robots" content="ALL" />
<meta name="author" content="MarPlo" />
<link href="'.USRTEMPL.'style.css" rel="stylesheet" type="text/css" />
<link href="'.USRTEMPL.'usrpg.css" rel="stylesheet" type="text/css" />
<!--[if IE]><script src="'.USRJS.'html5.js"></script><![endif]-->
<script src="'.USRJS.'jquery_1.7.js" type="text/javascript"></script>
<script src="'.USRJS.'functions.js" type="text/javascript"></script>'.jsTexts($lsite);

// If its User page, adds JS functions for messages
if(isset($_GET['usr']) && $_GET['usr']!='0' && $_GET['usr']!='fb') $htmlhead .= '<script src="'. USRJS. 'msgs.js" type="text/javascript"></script>';

// If its the page of the logged user, or Admin, adds JS with admin functions
if(isset($_SESSION['usritspage']) && $_SESSION['usritspage']===1) $htmlhead .= '<script src="'.USRJS.'usrloged.js" type="text/javascript"></script>';

$htmlhead .= '</head>';

// sends head zone to browser
echo $htmlhead;
flush();

$htmlhead = '<body>
<header id="header">';

$htmlhead .= $login;          // adds login form / message

$htmlhead .= '<h2 id="titlusr">'. $titlusr. '</h2></header>';

echo $htmlhead;          // aoutput the html code