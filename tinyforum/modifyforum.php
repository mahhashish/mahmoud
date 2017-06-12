<?php
include_once 'session.php';
if($_SESSION['user_info']->isadmin != 1){
    header("location: showforum.php");
}

if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if($_id == 0){
	die('NO ID == 0');
}

include_once('forumAPI.php');

$forum = tinyf_forum_get_by_id($_id);
tf_db_close();

if($forum == null){
	die('NO forum');
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Modify Forum: <?php echo $forum->title; ?></title>
</head>
<body>
<form action="updateforum.php?id=<?php echo $_id; ?>" method="post">
<table align="center" style="width: 60%">
	<tr>
		<td>Title:</td>
  		<td><input type="text" value="<?php echo $forum->title; ?>" name="title"></td>
	</tr>
	<tr>
		<td>Description:</td>
  		<td><input type="text" value="<?php echo $forum->desc; ?>" name="desc"></td>
	</tr>
	<tr>
	<td><input type="submit" name="submit" value="Modify: <?php echo $forum->title; ?>"></input></td>
	</tr>
</table>
</form>
</body>
</html>