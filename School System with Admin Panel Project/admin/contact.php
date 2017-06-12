<?php
$conn=mysql_connect("localhost","root","123456");
$database="school";
mysql_select_db($database, $conn);
$msg="";

include 'header.php';
include 'sidebar.php';

 if(isset($_POST['submit']) && !empty($_POST['submit'] )) 
 {
 	  $email   = $_POST['email'];
    $call    = $_POST['call'];
    $fax     = $_POST['fax'];
    $mailadd = $_POST['mailadd']; 
    $sql_insert = mysql_query("insert into contact (email,cellnum,fax,mailadd) values ('$email','$call','$fax','$mailadd')");


    if($sql_insert)
    {
    	$msg= "Data Successfully Added";
    }
    else
    {
    	$msg = "Error to add data";
    }

  
 }
if(isset($_POST['update']) && !empty($_POST['update'] ))
{ 

	  $email   = $_POST['email'];
    $call    = $_POST['call'];
    $fax     = $_POST['fax'];
    $mailadd = $_POST['mailadd'];

	$sql_update = mysql_query("update contact set email = '$email', cellnum ='$call', fax ='$fax', mailadd='$mailadd' where id=13");


      if($sql_update)
      {
          $msg = "Data Successfully Updated";
      }
      else
      {
      	 $msg = "Problem to update the data";
      }

}



?>
<html>
<head>
<style type="text/css">
	
    .contact {
    width: 360px;
    padding: 8% 0 0;
    margin: auto; 
    padding-top: 10px;
}
.form {
  position: absolute;
  z-index: 1;
  background: #FFFFFF;
  max-width: 450px;
  margin: 0 auto 100px;
  padding: 45px;
  text-align: left;
 background-color:  #fdfefe  ;
}
.form input {
  font-family: "Roboto", sans-serif;
  outline: 0;
  background: #f2f2f2;
  width: 100%;
  border: 0;
  margin: 0 0 15px;
  padding: 15px;

  box-sizing: border-box;
  font-size: 14px;
}
</style>
 
 <script type="text/javascript">	
 function validform()
 {
var call= document.getElementById("call").value;
var email= document.getElementById("email").value;
var fax = document.getElementById("fax").value;
var mailadd = document.getElementById("mailadd").value;
   var atpos = email.indexOf("@");
   var dotpos = email.lastIndexOf(".");
     if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
      {

         alert("Not a valid e-mail address");
         return false;
      }
    
    
   if(/^\d{10}$/.test(call))
   	{
      return true;
   	}
else
{
	alert("invalid number");
	return false;
}
          
 } 


</script>

</head>
<body style="background-color: #edf7f1  ">
   
<form name="contact" onsubmit="return validform()" method="post" >
 <div id="mydiv" style="color: red; padding-left: 400px; padding-bottom: 20px; padding-top: 10px; font-size: 20px;text-align:center; "><?php echo $msg;?></div> 
<div class="contact"> 
 <div class="form">
  <label style="font-family: sans-serif; font-size:20px;  padding-top: 20px">Email</label>
    <input type="text" name="email" id="email" />
  <label style="font-family: sans-serif; font-size:20px; padding-top: 20px">Call toll-free</label>
    <input type="text" name="call" id="call" />
  
  <label style="font-family: sans-serif;font-size:20px; ">Toll-free fax</label>
    <input type="text"  name="fax" id="fax" />
  <label style="font-family: sans-serif;font-size:20px;  padding-top: 100px">Mailing Address</label>
   <textarea style="padding-right: 200px; padding-bottom: 100px" name="mailadd" id="mailadd"> </textarea>
      
  <input type="submit" name="submit"  value="Add"  style="background-color:#011614; color: white; display: inline-block; font-size: 20px; height: 50px; margin-top: 10px; width: 100px; " >   <input type="submit" name="update" value="Update"	style="background-color:#011614; color: white; display: inline-block; font-size: 20px; height: 50px; margin-top: 10px; width: 100px; "></td>  
</div>
</div>
  </form>	
</body>
</html>    
            