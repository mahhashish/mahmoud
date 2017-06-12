<?php

require_once ('config.ini.php'); 

function showError($message)
{
    echo "<h2>Error</h2>";
    echo nl2br(htmlspecialchars($message));
    exit();
}
// DB connection string and username/password
$connStr = 'mysql:host=localhost;dbname='.$db;

// Create the connection object
try {
    $conn = new PDO($connStr, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    //showHeader('Error');
    showError("Sorry, an error has occurred. Please try your request
later\n" . $e->getMessage());
}
$email        = $conn->query("SELECT email FROM $t_settings")->fetchColumn();

$return_url   = $website."/".$location."/admin/pipn/success.php";
$cancel_url   = $website."/".$location."/admin/pipn/cancel.php";
$notify_url   = $website."/".$location."/admin/pipn/payments.php";
?>