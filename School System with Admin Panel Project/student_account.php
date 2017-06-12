
 <html>
 <head>
 	
 </head>
 <body bgcolor=" #7F789C ">
 <h1 style="text-align: center; color: #2A0E9E;">Welcome Student</h1>

<?php

session_start();
if($_SESSION['identity']=='teacher')
{
 header("location: teacher_account.php");
 return false;
}
if(!isset($_SESSION['id']))
{
 header("location: login.php");
 return false;
}



  else
{ 

?>
<p style="font-size: 20px; color: #2A0E9E; padding-top: 50px; ">Set Your Profile Image</p>

<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file" id="file" /><br />
    <button type="submit" name="submit">save</button>
</form>
<?php
    if(isset($_GET["img"]))
    {
        $n=$_GET["img"];
        echo "<img src='uploads/".$n."'>";
    }
?>
<p style="font-size: 20px; color: #2A0E9E; padding-top: 50px; text-align: right; "><a href="logout.php">Logout</a></p>
<h3 style="color: #0E27C2; font-style:italic;"><?php echo "Name:- ",$_SESSION['name']; } ?></h3>
<h3 style="color: #0E27C2; font-style:italic;"><?php echo "Gender:- ",$_SESSION['gender'];  ?></h3>
<h3 style="color: #0E27C2; font-style:italic;"><?php echo "DOB:- ",$_SESSION['dob'];  ?></h3>
<h3 style="color: #0E27C2; font-style:italic;"><?php echo "Address:- ",$_SESSION['address']."&nbsp".$_SESSION['city'];  ?></h3>

 </body>
 </html>		