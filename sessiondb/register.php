<html>
<title>
	Register User
</title>
<body>
	<form action="" method="POST" autocomplete="off">
		<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css">
		<center><div id="body">
			<h4>Registration Form</h4>

			<form action="" method="POST" autcomplete="off">
			Full Names: <br><input type="text" name="kycdetails" required/></br>
			Telephone: <br><input type="number" name="tel" required/></br>
			Email Add:<br><input type="email" name="email" required/></br>
			<input type="submit" name="register" value="Register"/>

		</div></center>
	</form>
</body>
</html>


<?php


include('logindb.php');


if(isset($_POST['register']))
{

	$names     = htmlspecialchars(stripslashes(trim(strtoupper(filter_input(INPUT_POST, 'kycdetails')))));
	$clean     = preg_replace("/[^A-Za-z0-9]/", "",$names);
	$tel       = filter_input(INPUT_POST, 'tel');
	$email     = filter_input(INPUT_POST, 'email');
	$userid    = mt_rand('32124','98799');
   

		try{

			$start = $con->prepare("INSERT INTO tb_details (name,userid,email_add,PhoneNumber,password,date_registration) values ('$clean', '$userid', '$email', '$tel', md5('pass123'), now())");
			$start->execute();
			echo '<script>
			alert("Your Login UserID is '.' '.$userid.' Default Password is pass123");
			location.assign("admin.php");
			</script>';

		}catch(Exception $e)

		{

			echo 'Duplicate Entry Username and Email';
			file_put_contents('registration.xlsx','<?xml version="1.0" ?>');
			file_put_contents('registration.xlsx',$e->getmessage(),FILE_APPEND | LOCK_EX);
		}




}


?>
