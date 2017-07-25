<?php
header('Content-type: text/html; charset=utf-8');

include('admin.php');		// Include the file with data for connecting to mysql
$objLogare = new Logare($mysql);
$conn = $objLogare->setConn();       // get the connection

// Create the users table
$sql = "CREATE TABLE `users` (`nume` VARCHAR(32), `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, `parola` VARCHAR(32), `email` VARCHAR(45), `rank` DECIMAL(1,0) DEFAULT 0, `ip_reg` VARCHAR(15), `ip_visit` VARCHAR(15), `datereg` DATETIME NULL, `datevisit` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `visits` SMALLINT UNSIGNED DEFAULT 0, `pass` VARCHAR(18)) CHARACTER SET utf8 COLLATE utf8_general_ci"; 
if($conn->query($sql) === TRUE)
    echo '<br /><br /><br /><center><h4>The <u>users</u> table was created.</h4></center><br />'; 
else
  echo '<br /><br /><br /><center><h4>The <u>users</u> table could not be created - '. $conn->error. '</h4></center>';

// Create the user_temp table
$sql2 = "CREATE TABLE `user_temp` (`nume` VARCHAR(32) PRIMARY KEY, `nri` TINYINT UNSIGNED DEFAULT 0, `ip` VARCHAR(15), `dt` INT(10)) CHARACTER SET utf8 COLLATE utf8_general_ci"; 
if($conn->query($sql2) === TRUE)
    echo '<br /><br /><br /><center><h4>The <u>user_temp</u> table was created.</h4></center><br />'; 
else
  echo '<br /><br /><br /><center><h4>The <u>user_temp</u> table could not be created - '. $conn->error. '</h4></center>';

// Create the 'userdat' table, which contains the users optional data
$sql3 = "CREATE TABLE `usersdat` (`id` INT UNSIGNED PRIMARY KEY, `nume` VARCHAR(32), `pronoun` VARCHAR(32), `country` VARCHAR(15), `city` VARCHAR(25), `adres` VARCHAR(125), `bday` DATE, `ym` VARCHAR(25), `msn` VARCHAR(32), `site` VARCHAR(32), `img` VARCHAR(125), `ocupation` VARCHAR(500), `interes` VARCHAR(500), `transmit` VARCHAR(1000)) CHARACTER SET utf8 COLLATE utf8_general_ci"; 
if($conn->query($sql3) === TRUE)
    echo '<br /><br /><br /><center><h4>The <u>usersdat</u> table was created.</h4></center><br />'; 
else
  echo '<br /><br /><br /><center><h4>The <u>usersdat</u> table could not be created - '. $conn->error. '</h4></center>';

// Create useron table (which store the online users)
$sql4 = "CREATE TABLE `useron` (`nume` VARCHAR(32) PRIMARY KEY, `sid` CHAR(50), `dt` INT(10)) CHARACTER SET utf8 COLLATE utf8_general_ci"; 
if ($conn->query($sql4))
    echo '<br /><br /><br /><center><h4>The <u>useron</u> table was created.</h4></center><br />'; 
else
  echo '<br /><br /><br /><center><h4>The <u>useron</u> table could not be created - '. $conn->error. '</h4></center>';

$conn->close();
?>