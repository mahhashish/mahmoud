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
<tr>
   <td>Name</td>
    <td><?php echo $name; ?></td>
</tr>
 <tr>
        <td>Founded</td>
        <td><?php echo $foundedYear; ?></td>
 </tr>
 <tr>
        <td>employeeCountRange</td>
         <td><?php echo $employeeCountRange; ?></td>
 </tr>
 <tr>
        <td>Specialties<td>
        <td>
              <ul>
              <?php foreach ($specialties as $specialty): ?>
	              <li><?php echo $specialty; ?></li>
		  <?php endforeach; ?>
              </ul>
        </td>
 </tr>
<tr>
       <td>Website</td>
       <td><a href="<?php echo $websiteUrl; ?>">Website</a></td>
</tr>
<tr>
        <td>numFollowers</td>
         <td><?php echo $numFollowers; ?></td>
</tr>
</table>

<div style="margin-top: 10px;">
   <a href="<?php echo site_url('linkedinfo/company_updates/7919'); ?>">Updates</a>
</div>

<div style="margin-top: 10px;">
    <a href="<?php echo site_url('linkedinfo/company_first/3495'); ?>">1st connections</a>
</div>

</body>
</html>