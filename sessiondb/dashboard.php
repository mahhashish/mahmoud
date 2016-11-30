
	<?php
include ('fetchc2bdata.php');
	session_start();

	if(isset($_SESSION['login']))
	{


		$passed = $_SESSION['login'];
		
	}else
	 {
	 	session_unset();

		echo '<script>
		alert("Security Token Missing. Please Login")
		location.assign("admin.php");
		</script>';
	}

	?>
	<html>
<title>
	Admin Dashboard
</title>
<link rel="stylesheet" href="/pckvmwsdl/bootstrap/css/bootstrap.css" type="text/css">

	Logged in As <br><? echo $passed['name']; ?> 
	

		
		    <div class="navbar">
			<div class="tab">

            <form action="logout.php" method="POST" autocomplete="off">
			<input type="submit" value="Logout" name="logout"/>
			</div>

			

	</html>
	<?php
if(isset($_POST['submit']))
{
	$passed = $_SESSION['login'];

	header('Location: logout.php');
}


	?>