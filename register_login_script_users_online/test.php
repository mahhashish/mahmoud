<?php
// Include the file in which are set the object of the classes for Login, Register, Recover-data, and User-page
include('users.php');
?>
<!doctype html>
<html>
<head>
 <meta charset="utf-8" />
 <title>Test Users Script</title>
 <link rel="stylesheet" type="text/css" href="usrtempl/style.css" />
 <script src="usrjs/jquery_1.7.js" type="text/javascript"></script>
</head>
<body>
<?php
// display the value stored in $login, defined in "users/users.php"
// contains the login form or the welcome message
echo $login;
?>

<br/><br/>
Number of registered users: <?php echo $objUsers->users['total']; ?><br/>
Newest user: <?php echo $objUsers->users['last']; ?>
<h5>Online users:</h5> <?php echo $objUsers->users['online']; ?>

<br/><br/>
<?php
// Showing different content for logged user and visitors
if(isset($_SESSION['username'])) {
  echo '<h4>Content for logged users.</h4>';
}
else {
  echo '<h4>Content for visitors / not logged.</h4>';
}


// Display /Proccess data according to user rank
if(isset($_SESSION['rank']) && $_SESSION['rank']<2) {
  echo '<h4>Content for users with rank lower than 2.</h4>';
}
else if(isset($_SESSION['rank']) && $_SESSION['rank']==2) {
  echo '<h4>Content for users with rank 2.</h4>';
}
else if(isset($_SESSION['rank']) && $_SESSION['rank']>2) {
  echo '<h4>Data for users with rank higher than 2.</h4>';
}
?>
</body>
</html>