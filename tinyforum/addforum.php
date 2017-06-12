<!DOCTYPE html>
<html>
<head>
	<title>Add New Forum</title>
</head>
<body>
<form action="saveforum.php" method="post">
<table align="center" style="width: 60%">
	<tr>
		<td>Title:</td>
  		<td><input type="text" name="title"></td>
	</tr>
	<tr>
		<td>Description:</td>
  		<td><textarea name="desc" style="width: 168px; height: 100px"></textarea></td>
	</tr>
	<tr>
	<td><input type="submit" name="submit" value="Add"></input></td>
	</tr>
</table>
</form>
</body>
</html>

