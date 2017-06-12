<?php
$msg='';

session_start();
   if(isset($_SESSION['email']))
   { 
      header('location: dashboard.php');

  }
   


  if(isset($_POST["login"]))
      {
        

     $email = $_POST['email'];
     $password = $_POST['password'];
      $user = 'poonambansal963@gmail.com';
      $pass = '12345678';


    
        

       if(($email==$user) && ($password==$pass))
       {
        $_SESSION['email'] = $email;
      header("location: dashboard.php");
       }

       else
       {
        $msg= "Error: Username or Password are not correct";
       }
      }
  

?>
<html>
<head>
<style type="text/css">
  .login {
  width: 360px;
  padding: 8% 0 0;
  margin: auto;
}
.form {
  position: relative;
  z-index: 1;
  background: #FFFFFF;
  max-width: 360px;
  margin: 0 auto 100px;
  padding: 45px;
  text-align: center;
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

.heading{
  text-align: center;
 color: #F6FBFB;

}
</style>
<script type="text/javascript">
 function validlogin()
 {
var email= document.getElementById("email").value
var password= document.getElementById("password").value

if(email=="")
    {
  alert("Error: Username should not be blank! ");
  return false;
    } 
var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
     {

        alert("Not a valid e-mail address");
        return false;
    }
    if(password=="")
    {
  alert("Enter Your Password ");
  return false;
    } 
    if(password.length<8)
    {
  alert("Password should not be less than 8 characters ");
  return false;
      
    }
 }	
</script>
<script src="http://code.jquery.com/jquery-1.11.2.min.js"></script> 
  
<script type="text/javascript">
  
  setTimeout(function() {
    $('#mydiv').fadeOut('fast');
}, 5000); // <-- time in milliseconds
</script>
</head>
<body style="background-color:  #1DE3D4 ">
<form name="login" onsubmit="return validlogin()" method="post">
 <div id="mydiv" style="color: red; padding-left: 400px; padding-bottom: 20px; padding-top: 10px"><?php echo $msg;?></div>
 

 <div class="heading"><H1>Admin Login</H1></div>
<div class="login"  > 
 
 <div class="form">
  <label style="font-family: sans-serif; padding-top: 20px">Email</label>
    <input type="text" placeholder="abc@gmail.com" name="email" id="email" />
    <label style="font-family: sans-serif;">Password</label>
   
  <input type="password" placeholder="Enter Your Password" name="password" id="password" />
  <input type="submit" name="login"  value="login"  style="background-color:#29847D; color: white; display: inline-block; font-size: 20px" >
  
    </form>
  </div>
  </div>
</body>
</html> 