<?php
if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if($_id == 0){
	die('NO ID == 0');
}

include_once('forumAPI.php');
include_once('postsAPI.php');

$forum = tinyf_forum_get_by_id($_id);

if(!$forum){
	tf_db_close();
	die('No Forum Id');
}



?>
<!DOCTYPE html>
<html>
<head>
	<title>Add New Post</title>
</head>
<body>
<h1 align="center"><?php echo $forum->title; ?></h1>
<form action="savepost.php?id=<?php echo $forum->id; ?>" method="post">
<table align="center" style="width: 60%">
	<tr>
		<td>Title:</td>
  		<td><input type="text" name="title"></td>
	</tr>
	<tr>
		<td>Content:</td>
  		<td><textarea name="content" style="height: 100px; width: 168px">Your post ....</textarea></td>
	</tr>
	<tr>
	<td><input type="submit" name="submit" value="Add Post"></input></td>
	</tr>
</table>
</form>
</body>
</html>

