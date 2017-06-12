<!DOCTYPE html>
<html>
<head>
	<title>Add New User</title>
</head>
<body>
<form action="saveuser.php" method="post">
<table align="center" style="width: 60%">
	<tr>
		<td>Name:</td>
  		<td><input type="text" name="username"></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" name="password"></td>
	</tr>
	<tr>
		<td>Email:</td>
  		<td><input type="email" name="email"></td>
	</tr>
	<tr>
	<td><input type="submit" name="submit" value="Submit"></input></td>
	</tr>
</table>
</form>
</body>
</html>

