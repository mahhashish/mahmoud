<?php
session_start();
include ('logindbphp');

if(isset($_POST['reset']))
   
   {


$email    	= filter_input(INPUT_POST, 'email');
$t_number 	= filter_input(INPUT_POST, 'userid');

$test = $con->prepare("SELECT email_add,PhoneNumber FROM tb_details where userid ='$t_number' and email_add = '$email'");
$test->execute();

$results  = $test->fetchAll(PDO::FETCH_ASSOC);


foreach ($results as $data) {

	
	$update   = $con->prepare("UPDATE tb_details set password = md5('pass123') where userid='$t_number' and email_add='$email'");
	$update->execute();

	if($update)
	{

     echo '<script>
	alert("Password Reset Successfull. Default Password pass123");
	location.assign("admin.php");
	</script>';

	}else{
		echo 'An Error Occured. Please Contact Admin';
	}
}
if(!$results)
{
	echo '<script>
	alert("Details Not Found. Please Enter Correct Details");
	location.assign("admin.php");
	</script>';
}

  }

 

?>

<html>
<title>
	PASSWORD RESET
</title>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css">
<body>



<form action="" method="POST" autcomplete="off">


	<center><div id="body">
		<p>Password Reset Center</p>
		<input type="number" name="userid" placeholder="Enter  Your UserID" required/></br>
		<input type="email" name="email" placeholder="Enter Your Email Address" required/></br>
		<input type="submit" name="reset" value=" Reset Your Password"></br>
     
	</div>

      </div>
      </center>
</form>
</body>
 <div class="frame">
    
      </div>
</html>





