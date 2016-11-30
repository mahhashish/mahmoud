<?php
session_start();
session_unset(['login']);



include ('logindb.php');


if(isset($_POST['check']))
{


try{

$userid   = filter_input(INPUT_POST, 'userid');
$password = md5(filter_input(INPUT_POST, 'password'));

//Start the connection

$query = $con->prepare("SELECT * FROM tb_details WHERE userid = '$userid' and password = '$password' ");
$query->execute();

//Check results
$results = $query->fetchALL(PDO::FETCH_ASSOC);

foreach($results as $fetched)
     {
  if($fetched['session_active']=='2')
  {
    echo '<script>
    alert("Simultaneous Loggins are Not Allowed");
    location.assign("admin.php");
    </script>';

  }else{

   switch($fetched['password'])
   {

   	case('32250170a0dca92d53ec9624f336ca24'):
    sleep(2);

    $_SESSION['pass_change'] = array('0'=>$fetched["userid"]);

   	header('location:passchange.php');
   	exit;

   	default;

   	/*---capture the Time the User Logins save the data in the DB----*/


   	/*----------------------------------------*/

   	$_SESSION['login'] = array('name'=>$fetched['name'],'timein'=>$fetched['timein'],'userid'=>$fetched['userid']);
    
    
   echo '<script>
    alert("Welcome '.$fetched['name'].'. Last Successful Login Attempt was '.$fetched['timein'].'");
    location.assign("dashboard.php");
    </script>';

     $timein = $con->prepare("UPDATE tb_details set timein = now(),session_active='2' where userid=".$fetched['userid']."");
     $timein->execute();

   }
 }
}



  }catch(Exception $e)
  {
  	echo '
  	<div class="fade">
  	Sorry an Error Occured
  	</div>';
    file_put_contents('errors/registration.log', date('y-m-d h:i:sa').$e->getmessage(), FILE_APPEND|LOCK_EX);
  }
if(!$results)
{
	echo '<center>User Details Not Found.</center>';
}

}



?>

<html>
<title>
	Setup
</title>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css">
<body>


<form action="" method="POST" autcomplete="off">
	<center><div id="body">
		<input type="number" name="userid" placeholder="Enter UserID" required/></br>
		<input type="password" name="password" placeholder="Enter Your Password" required/></br>
		<input type="submit" name="check" value="Check UserID"></br>
		<a href="register.php">Sign Up</a href> &nbsp &nbsp <a href="passreset.php">Reset Password</a href></br>
     
	</div></center>

      </div>
</form>
</body>
 <div class="frame">
    
      </div>
</html>





