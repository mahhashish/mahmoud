<!DOCTYPE html">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<html>
<head>

</head>
<body>
<H1>Welcome <?=$user_name; ?>! </H1>
<H1>You are looged in! </H1>
<HR></HR>
<H3>Your User ID is: 	<?=$uid; ?></H3>
<H3>Your System Role is :<?=$role; ?></H3>
<H3>Your Menu options  :<?=$menu; ?></H3>


<?php echo anchor ('auth/logout', 'Logout' ) ?>
		
</body>
</html>


