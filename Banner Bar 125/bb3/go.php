<?php
require_once('admin/inc/connect.ini.php');
require_once('admin/inc/functions.ini.php');
$table         = $_GET['ad'];
$allowedTables = array(
    $t_banners,
    $t_client_banners
);
if (!in_array($table, $allowedTables)) {
    $table = $t_banners;
} //!in_array($table, $allowedTables)
$banID = (int) $_GET['id'];
$stmt  = $conn->prepare("SELECT clicks, link FROM $table WHERE id=:id");
$stmt->bindValue(':id', $banID, PDO::PARAM_INT);
$stmt->execute();
$row   = $stmt->fetch(PDO::FETCH_ASSOC);
$count = $row['clicks'];
$link  = $row['link'];
$count = $count + 1;
//$d     = date('d-m-Y H:i:s');
if ($table == $t_banners) {
$stmt  = $conn->prepare("UPDATE $table SET clicks='$count', lastclick=NOW() WHERE id=:id");
}
else {
$stmt  = $conn->prepare("UPDATE $table SET clicks='$count' WHERE id=:id");
}

$stmt->bindValue(':id', $banID, PDO::PARAM_INT);
$stmt->execute();
if ($table == $t_banners) {
    $stmt = $conn->prepare("UPDATE $table SET pause='yes' WHERE xClick>=clicks AND id=:id");
    $stmt->bindValue(':id', $banID, PDO::PARAM_INT);
    $stmt->execute();
} //$table == 'banners'
header('Location: ' . $link);
exit;
?>