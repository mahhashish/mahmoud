<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $mega_title ?></title>
</head>
<body>

<table>
<tr>
  <td>Key</td>
  <td>Value</td>
</tr>

<?php foreach ($rows as $key => $value): ?>
<tr>
  <td><?php echo $key ; ?></td>
  <td><?php echo $value ; ?></td>
</tr> 
<?php endforeach; ?>
</table>

</body>
</html>