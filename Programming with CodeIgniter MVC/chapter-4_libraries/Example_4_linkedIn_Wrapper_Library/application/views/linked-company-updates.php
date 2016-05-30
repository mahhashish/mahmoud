<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Company</title>
</head>
<body>

<div>
    <p><a href="<?php echo site_url('') ; ?>">Back to Menu</a> </p>
</div>

<table>
<?php foreach ($updates as $update): ?>
<tr>
   <td>
	<ul>
      <?php foreach ($update as $key => $val): ?>
		<li><?php echo $key; ?>: <?php echo $val; ?></li>
		<?php endforeach; ?>
	</ul>
   </td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>