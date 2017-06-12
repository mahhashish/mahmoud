  <?php
$conn=mysql_connect("localhost","root","123456");
$database="school";
mysql_select_db($database, $conn);
$msg="";

include 'header.php';
include 'sidebar.php';
?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Responsive Table</title>
  <script src="http://s.codepen.io/assets/libs/modernizr.js" type="text/javascript"></script>


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

  
      <link rel="stylesheet" href="css/style.css">

  
</head>

<body>
  <h1 style="padding-bottom: 50px">Member Records</h1>
<table style="margin: auto;" class="rwd-table" >
  <tr>
    <th>Name</th>
    <th>Address</th>
    <th>City</th>
    <th>DOB</th>
    <th>Gender</th>
    <th>Identity</th>
    <th>Contact No</th>
  </tr>

  <?php
$sql_query = mysql_query("select * from user_registration order by id asc");
while($fetch = mysql_fetch_assoc($sql_query))
{
?>
<tr>
<td><b><font color="green"><?php echo $fetch['name']; ?></font></b></td>
<td><?php echo $fetch['address']; ?></td>
<td><?php echo $fetch['city']; ?></td>
<td><?php echo $fetch['dob']; ?></td>
<td><?php echo $fetch['gender']; ?></td>
<td><?php echo $fetch['identity']; ?></td>
<td><?php echo $fetch['phone']; ?></td>

</tr>
<?php } ?>
  
</table>


  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    

</body>
</html>
