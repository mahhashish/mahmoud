<?php
require_once ('check.php');
require_once('connect.ini.php');

$d = date("dmY");
define( 'DUMPFILE', '../backup/'.$db.$d .'.sql' );
header('Content-Type: text/plain');
try {
    $f      = fopen(DUMPFILE, 'wt');
    $tables = $conn->query('SHOW TABLES');
    foreach ($tables as $table) {
        $sql    = '-- TABLE: ' . $table[0] . PHP_EOL;
        $create = $conn->query('SHOW CREATE TABLE `' . $table[0] . '`')->fetch();
        $sql .= $create['Create Table'] . ';' . PHP_EOL;
        fwrite($f, $sql);
        $rows = $conn->query('SELECT * FROM `' . $table[0] . '`');
        $rows->setFetchMode(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $row = array_map(array(
                $conn,
                'quote'
            ), $row);
            $sql = 'INSERT INTO `' . $table[0] . '` (`' . implode('`, `', array_keys($row)) . '`) VALUES (' . implode(', ', $row) . ');' . PHP_EOL;
            fwrite($f, $sql);
        }
        $sql    = PHP_EOL;
        $result = fwrite($f, $sql);
        if ($result == FALSE) {
            $msg = "There seems to have been an error in backing up one or more tables";
        }
    }
    if (!empty($msg)) {
        echo $msg;
    } else {
        echo "All tables have been backed up successfully";
    }
    fclose($f);
}
catch (Exception $e) {
    echo 'Damn it! ' . $e->getMessage() . PHP_EOL;
}
?>