<?

/***
//
//		PDF Distiller
//		By Marco Tabini
//
//		From the December, 2002 Issue of php|architect
//
//		Copyright (c) Marco Tabini and Associates, Inc.
//
/***/

?>

<html>
<body>
<h1>PDF Distiller</h1>
Please upload your PostScript file here.
<form enctype="multipart/form-data" action="postps.php" method="post">
<table cellpadding=3 cellspacing=0>
	<tr>
		<td>
			Please choose a file
		</td>
		<td>
			<input class=EditTextBox type="file" name="infile">
		</td>
	</tr>
</table>	
<p><input type="submit" class="EditSubmitButton" value="Submit" >
</form>
</body>
</html>
