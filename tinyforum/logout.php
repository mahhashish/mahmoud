<?php
include_once 'session.php';
if($_SESSION['user_info'] == false){
    header("location: login.php");
}else{
    $_SESSION['user_info'] = false;
    header("location: login.php");
}

?>