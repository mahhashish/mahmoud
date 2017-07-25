<?php
// sets the user name and the title
$usr = isset($_GET['usr']) ? $_GET['usr'] : '';
$titlusr = isset($_GET['usr']) ? 'User page: '. $usr : 'Script Users';

// 6the html code for the <head> area
$htmlhead = '<!doctype html>
<html>
<head>
 <meta charset="utf-8" />
<title>'.$titlusr.'</title>
<base href="http://'.$_SERVER['SERVER_NAME'].'/" />
<meta name="language" content="ro" />
<meta name="description" content="'.$titlusr.', register, login PHP - AJAX" />
<meta name="keywords" content="'.$usr.', users, register, login" />
<meta name="abstract" content="'.$titlusr.'" />
<meta name="audience" content="all" />
<meta name="robots" content="ALL" />
<meta name="author" content="MarPlo" />
<link href="templ/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<header id="header">';

$htmlhead .= $logare;          // adds login form / message

$htmlhead .= '<h2 id="titlusr">'. $titlusr. '</h2></header>';

echo $htmlhead;          // aoutput the html code
?>