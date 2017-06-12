<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['loggedin'])) {
	header("Location: login/L-login.php");
	exit;
} else {
	// the session variable exists
}
?>