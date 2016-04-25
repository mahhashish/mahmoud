<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Details</title>
</head>
<body>

<table>
<tr>
    <td>Full Name:</td>
    <td><?php echo $my_details->firstName . " " . $my_details->lastName ; ?></td>
 </tr>

 <tr>
    <td>Title</td>
     <td><?php echo $my_details->headline ; ?></td>
 </tr>

<tr>
    <td>My Linkedin profile</td>
    <td><a href="<?php echo $profile_url ?>" target="_blank">Link</a> </td>
</tr>
</table>

<div>
    <p><a href="<?php echo site_url('') ; ?>">Back to Menu</a> </p>
</div>

</body>
</html>