<?php
  // Start the session
  session_start();
  // if session "loggedin" has not been set redirect back to the login page
  if (!isset($_SESSION['loggedin'])) {
      header("Location: L-login.php");
      exit;
  } else {
      // else if session "loggedin" has been set unset it and redirect back to the login page
      unset($_SESSION['loggedin']);
      header("Location: L-login.php");
      exit;
  }
?>
