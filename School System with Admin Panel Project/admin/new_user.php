<?php
include 'header.php'; 
include'sidebar.php';
 ?>


<html>
<head>

 <script type="text/javascript">

 function validform() 
 {

var email= document.getElementById("email").value;
var phone = document.getElementById("phone").value;

var atpos = email.indexOf("@");
var dotpos = email.lastIndexOf(".");
     if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
      {
         alert("Not a valid e-mail address");
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
</head>
<body>
<form style="position: absolute; padding-left: 400px; width: 50%" onsubmit="return validform()" name="article" method="post">
<div class="clear"></div>
		
	<article class="module width_full">
			<header><h3>Add New Member</h3></header>
				<div class="module_content">
						<fieldset>
							<label>Name</label>
							<input type="text" id="name" name="name" required="" >
						</fieldset>
						<fieldset>
							<label>Email</label>
							<input type="text" id="email" name="email" required="" >
						</fieldset>
						<fieldset > 
							<label>Address</label>
							<textarea id="address" name="address" required=""></textarea>
						</fieldset>
            <fieldset > 
              <label>City</label>
              <input type="text" name="city" id="city" required="">
            </fieldset>
            <fieldset > 
              <label>Mobile No</label>
              <input type="text" name="phone" id="phone" required="">
            </fieldset>
           <fieldset > 
              <label>Birthday</label>
               <select name="birthmonth" id="birthmonth" placeholder="Month" required="">
                  <option value="">Month</option>
                  <option value="01">January</option>
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
                 <label>Day</label>
                 <input type="text" name="day" id="day" maxlength="2" placeholder="Day" required="">
                <label>Year</label>
               <input type="text" name="year" id="year" maxlength="4" minlength="4" placeholder="Year" required=""> </fieldset>
               <fieldset > 
              <label>Gender</label>
              <select required=""  name="gender" id="gender" >
            <option value="select">Select</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            </select>
            </fieldset > 
            <fieldset > 
              <label>Identity As...</label>
              <select required="" name="identity" id="identity" >
            <option value="select">Select</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            </select>
            </fieldset > 



   		   			<input type="submit" value="Add" id="submit" name="submit" class="alt_btn">
   		   					
							<input type="submit" value="Reset" id="reset" name="reset" class="alt_btn">
			    </div>
	</article>
</form>
</body>
</html>