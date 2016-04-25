<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Connections</title>
</head>
<body>

<h1>My Total connections:  <?php echo $connections_count ; ?></h1>

<div>
    <p><a href="<?php echo site_url('') ; ?>">Back to Menu</a> </p>
</div>

<table>
<tr>
     <td>Picture</td>
     <td>Name</td>
    <td>Headline</td>
    <td>Industry</td>
 </tr>

<?php foreach ($connections as $connection): ?>
  <tr>
    <td><img src="<?php echo $connection['picture_url'] ;  ?>"> </td>
    <td><a href="<?php echo $connection['url'];?>" target="_blank"><?php echo $connection['name'] ?></a> </td>
    <td><?php echo $connection['headline'] ; ?></td>
    <td><?php echo $connection['industry'] ; ?></td>
  </tr>
 <?php endforeach; ?>
</table>

</body>
</html>