<?php
include('config.php');
$re_ct = '';            // will be used to return the result

           /* Define /$sqlc array with SQL queries to create tables */

// The users table
$sqlc['users'] = "CREATE TABLE `users` (`name` VARCHAR(32), `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, `passenc` VARCHAR(32), `email` VARCHAR(55), `rank` DECIMAL(1,0) DEFAULT 0, `ip_reg` VARCHAR(15), `ip_visit` VARCHAR(15), `dtreg` INT(11) NOT NULL, `dtvisit` INT(11) NOT NULL, `visits` SMALLINT UNSIGNED DEFAULT 0, `pass` VARCHAR(25)) CHARACTER SET utf8 COLLATE utf8_general_ci";

// Table for users data loged with Facebook
$sqlc['fbusers'] = "CREATE TABLE `fbusers` (`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, `fbuserid` VARCHAR(18), `name` VARCHAR(32), `email` VARCHAR(55), `rank` DECIMAL(1,0) DEFAULT 1, `perms` VARCHAR(600), `ip_visit` VARCHAR(15), `dtreg` INT(11) NOT NULL, `dtvisit` INT(11) NOT NULL, `visits` SMALLINT UNSIGNED DEFAULT 1) CHARACTER SET utf8 COLLATE utf8_general_ci";

// Table for users data loged with Yahoo
$sqlc['yhusers'] = "CREATE TABLE `yhusers` (`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(32), `email` VARCHAR(55), `rank` DECIMAL(1,0) DEFAULT 1, `ip_visit` VARCHAR(15), `dtreg` INT(11) NOT NULL, `dtvisit` INT(11) NOT NULL, `visits` SMALLINT UNSIGNED DEFAULT 1) CHARACTER SET utf8 COLLATE utf8_general_ci";

// Table for users data loged with Google
$sqlc['gousers'] = "CREATE TABLE `gousers` (`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(32), `email` VARCHAR(55), `rank` DECIMAL(1,0) DEFAULT 1, `ip_visit` VARCHAR(15), `dtreg` INT(11) NOT NULL, `dtvisit` INT(11) NOT NULL, `visits` SMALLINT UNSIGNED DEFAULT 1) CHARACTER SET utf8 COLLATE utf8_general_ci";

// The logattempt table (to control the number of login attempts)
$sqlc['logattempt'] = "CREATE TABLE `logattempt` (`email` VARCHAR(55) PRIMARY KEY, `nri` TINYINT UNSIGNED DEFAULT 0, `ip` VARCHAR(15), `dt` INT(11)) CHARACTER SET utf8 COLLATE utf8_general_ci";

// The 'userdat' table (stores the optional users data)
$sqlc['usersdat'] = "CREATE TABLE `usersdat` (`id` INT UNSIGNED PRIMARY KEY, `name` VARCHAR(32), `pronoun` VARCHAR(32), `country` VARCHAR(18), `city` VARCHAR(30), `adres` VARCHAR(150), `bday` DATE, `ym` VARCHAR(30), `msn` VARCHAR(35), `site` VARCHAR(35), `img` VARCHAR(135), `ocupation` VARCHAR(600), `interes` VARCHAR(600), `transmit` VARCHAR(1200), `fav` VARCHAR(2800) DEFAULT '') CHARACTER SET utf8 COLLATE utf8_general_ci";

// The useron table (stores the online users)
$sqlc['useron'] = "CREATE TABLE `useron` (`email` VARCHAR(55) PRIMARY KEY, `name` VARCHAR(32), `sid` CHAR(50), `dt` INT(11)) CHARACTER SET utf8 COLLATE utf8_general_ci";

// table for messages
$sqlc['msgs'] = "CREATE TABLE `msgs` (`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, `user` VARCHAR(32) NOT NULL, `idusr` INT UNSIGNED, `name` VARCHAR(32) DEFAULT '', `email` VARCHAR(55) DEFAULT '', `fbuserid` VARCHAR(18) DEFAULT '0', `social` VARCHAR(6) DEFAULT '0', `msg` VARCHAR(750) DEFAULT '', `dt` int(11) NOT NULL DEFAULT 0, `ip` VARCHAR(15) DEFAULT '', `amail` int(1) NOT NULL DEFAULT 0) CHARACTER SET utf8 COLLATE utf8_general_ci";

// create tables
$obj = new Base($mysql);      // creates object instance to Base class

// traverse the $sqlc array, and calls the method to create the tables
foreach($sqlc as $tab=>$sql) {
  $re_ct .= $obj->sqlExecute($sql) ? sprintf($lsite['create_tables'], $tab) : $obj->eror;
}
// create admin account, with rank 9
$sql = "INSERT INTO `users` (`name`, `passenc`, `email`, `rank`, `ip_reg`, `ip_visit`, `dtreg`, `pass`) VALUES ('".ADMINNAME."', '".md5(ADMINPASS)."', '".ADMINMAIL."', 9, '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['REMOTE_ADDR']."', ".time().", '".ADMINPASS."')";
$re_ct .= $obj->sqlExecute($sql) ? $lsite['create_admin'] : $obj->eror;

echo $re_ct;