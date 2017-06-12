<?php
include 'header.php'; 

  $conn=mysql_connect("localhost","root","123456");
$database="school";
mysql_select_db($database, $conn);
$msg='';
session_start();
if(isset($_POST['submit']) && !empty($_POST['submit'] ))
{


  $name = $_POST['name'];
  $address = $_POST['address'];
  $email = $_POST['email'];
  $city = $_POST['city'];
  $password = md5($_POST['password']);
  $repassword = $_POST['repassword'];
  $birthmonth = $_POST['birthmonth'];
  $birthday = $_POST['birthday'];
  $birthyear = $_POST['birthyear'];
  $gender = $_POST['gender'];
  $identity = $_POST['identity'];
  $phone = $_POST['phone'];
  $dateOfBirth = $birthday."-". $birthmonth."-".$birthyear;
  
$select = mysql_query("select email from user_registration where email='$email'");
   
   if(mysql_num_rows($select)==0)
   {
$sql_insert = mysql_query("insert into user_registration (name,address,email,city,password,dob,gender,identity,phone) values ('$name','$address','$email','$city','$password','$dateOfBirth','$gender','$identity','$phone')");
      
       if($sql_insert)
      {
        $msg = "Registration Successfull";

        if($identity=="student")
        {
          header("location: login.php");
        }
        
      }
      else
      {
        $msg = "Error: Registration UnSuccessfull";
      }

   }

   else
   {
      $msg = "Email is already in database";
   }
 } 

 

?>



<html>
<head>
<script type="text/javascript">

 function validform() 
 {

var email= document.getElementById("email").value;
var password = document.getElementById("password").value;
var repassword = document.getElementById("repassword").value;
var phone = document.getElementById("phone").value;
var atpos = email.indexOf("@");
var dotpos = email.lastIndexOf(".");
     if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
      {

         alert("Not a valid e-mail address");
         return false;
      }

      if(password.length<8 )
      {
      alert("Password must have atleast 8 characters");
         return false;
      }
      else if (password!=repassword) 
      {
    alert("Error: Passwords do not match with Confirm Password.");
            return false;

      }
       if(/^\d{10}$/.test(phone))
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

    <link rel="stylesheet" type="text/css" href="style.css" media="all" />
    <link rel="stylesheet" type="text/css" href="demo.css" media="all" />



</head>

<header style="padding-bottom: 50px">
   <h1 style="text-align: center; font-size: 35px;">Registration Form</h1>
</header>
      
      <div  class="form" style="padding-right: 20px">
        <form id="contactform" style="padding-top: 20px;"  onsubmit="return validform()" method="post"> 
        <!-- <div class="contact" style="color: red; padding-left: 400px; padding-bottom: 10px; padding-top: 10px"><?php //echo $msg;?></div> -->
        <p class="contact" style="color: red; font-size: 20px"><?php echo $msg;?></p> 
          <p class="contact"><label for="name">Name</label></p> 
          <input id="name" name="name" placeholder="First and last name" required="" tabindex="1" type="text"> 
           
           <p class="contact"><label for="name">Address</label></p> 
           <textarea class="contact" id="address" name="address" placeholder="Address" required=""   ></textarea>

          <p class="contact"><label for="email">Email</label></p> 
          <input id="email" name="email" placeholder="example@domain.com" required="" type="text"> 
                
                <p class="contact"><label for="city">City</label></p> 
          <input id="city" name="city" placeholder="city" required="" tabindex="2" type="text"> 
           
                <p class="contact"><label for="password">Create a password</label></p> 
          <input type="password" id="password" name="password" required=""> 
                <p class="contact"><label for="repassword">Confirm your password</label></p> 
          <input type="password" id="repassword" name="repassword" required=""> 
        
               <fieldset>
                 <label>Birthday</label>
                  <label class="month"> 
                  <select class="select-style" name="birthmonth" id="birthmonth" required="">
                  <option value="">Month</option>
                  <option  value="01">January</option>
                  <option value="02">February</option>
                  <option value="03" >March</option>
                  <option value="04">April</option>
                  <option value="05">May</option>
                  <option value="06">June</option>
                  <option value="07">July</option>
                  <option value="08">August</option>
                  <option value="09">September</option>
                  <option value="10">October</option>
                  <option value="11">November</option>
                  <option value="12" >December</option>
                  </label>
                 </select>    
                <label>Day<input class="birthday" maxlength="2" name="birthday"  id="birthday" placeholder="Day" required=""></label>
                <label>Year <input class="birthyear" maxlength="4" name="birthyear" id="birthyear" placeholder="Year" required=""></label>
              </fieldset>
            <p class="contact"><label for="password">Gender</label></p> 
            <select class="select-style gender" name="gender" id="gender">
            <option value="select">Select</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            </select><br><br>
            
            <select class="select-style gender" name="identity" id="identity">
            <option value="select">I am a...</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            </select><br><br>


            <p class="contact"><label for="phone">Mobile phone</label></p> 
            <input id="phone" name="phone" placeholder="phone number" required="" type="text"> <br>
            <input class="button" type="submit" name="submit" id="submit" tabindex="5" value="Sign up" >  
                 
   </form> 

   
</div>      
</div>
           <p style="font-family: sans-serif; font-size: 25px; text-align: center; padding-top: 30px" class="message">Already a Member  
              <a href="login.php">Log In</a></p>

<?php 
include 'footer.php';
?>






