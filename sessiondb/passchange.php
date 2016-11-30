<?php

include('logindb.php');

session_start();

if(isset($_SESSION['pass_change']))
{

try{
$userid = $_SESSION['pass_change'][0];

$change = $con->prepare("SELECT * FROM tb_details where userid = '$userid'");
$change->execute();
$results = $change->fetchALL(PDO::FETCH_ASSOC);

foreach($results as $fetched)
  {

$userid = $fetched['userid'];
$names  = $fetched['name'];

}

   }catch(Exception $e)
{
	echo $e->getmessage();
}

}else
{
	echo '<script>
		alert("Please Login with Default Settings to Change Password")
		location.assign("admin.php");
		</script>';
}

?>

<html>
<title>
	Setup
</title>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css">
<body>
<center><div id="body">
	<h5>Welcome! Please Change Your Password</h5>
	

		<form action="" method="POST" autcomplete="off">
		<input type="text"  name="userid" value="<?php echo $userid; ?>" disabled="false" required/></br>
		<input type="text"  name="names" value="<?php echo $names; ?>" disabled="false" required/></br>
		<input type="password" name="password" placeholder="Enter Your Password" required/></br>
		<input type="password" name="password1" placeholder="Re-Enter Your Password" required/></br>
		<input type="submit" name="reset" value="Reset Password">
		
	</div>
</form>
</body></center>
</html>
<?php

if(isset($_POST['reset']))
{
	$pass1 = filter_input(INPUT_POST, 'password');
	$pass2 = filter_input(INPUT_POST, 'password1');

	if($pass2 != $pass1 )
	{
		echo '<script>
		alert("Password not matching");
		</script>';
	}else

	{
		$passnew = md5($pass2);
if($passnew=='32250170a0dca92d53ec9624f336ca24')
{
	echo '<script>
   	alert("You Cannot Use your Default Password as Your New Password");
   	location.assign("admin.php");
   	</script>';

}else{
		$change = $con->prepare("UPDATE tb_details set password='$passnew' where userid='$userid'");

		$change->execute();

		echo '<script>
   	alert("Reset Done. Now Login to Your Account");
   	location.assign("admin.php");
   	</script>';
	}
}
	session_destroy();
}
?>