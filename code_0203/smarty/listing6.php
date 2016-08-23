<?php

    session_start();
    if (isset($_GET['debug'])) {
        $_SESSION['debug']=$_GET['debug'];
    }
    $smarty_debugging=false;
    if (isset($_SESSION['debug'])) {
        $smarty_debugging=$_SESSION['debug'];
    }

    error_reporting(E_ALL);
    set_error_handler('error_handler');

function error_handler($type, $text, $file, $line, $vars) {
    global $error_log;
    $error_log[]=$text;
    $error_log[]="[$line] $file";
}

function debug_log($key, $data) {
    global $debug_log;    
    debug_log[$key][]=$data;
}

?>