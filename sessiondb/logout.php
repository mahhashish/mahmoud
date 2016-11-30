<?php
try{
include ('logindb.php');
session_start();
if(isset($_SESSION['login']))
{
 

 $userid = $_SESSION['login']['userid'];
 
 $logout = $con->prepare("UPDATE tb_details set session_active ='1',timeout= now() where userid = '$userid'");
 $logout->execute();

echo '<script>
alert("Dear '.$_SESSION['login']['name'].' You Have Been Logged Out");
location.assign("index.php");
</script>';
session_unset();

}else{
	echo 'Please Login to view content'.'<br>';
	echo '<a href="index.php">Log In</a href>';
}
}catch(Exception $e)
{
	echo 'An Error Has Occured.'.$e->getmessage();
}

?>