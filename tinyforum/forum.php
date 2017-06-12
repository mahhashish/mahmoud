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
<html dir="rtl">
<head>
	<title><?php echo $forum->title; ?></title>
</head>
<body>
<h1><?php echo $forum->title; ?></h1>
<ul type="square">
<?php
$posts = tinyf_post_get("WHERE `fid` =".$_id);

if($posts == null){
	tf_db_close();
	die('<a href="addpost.php?id='.$_id.'">add post</a>');
}

$fcount = count($posts);

if($fcount == 0){
	tf_db_close();
	die('Error Count Posts');
}

for($i = 0; $i < $fcount; $i++){
	$forums = $posts[$i];
	echo"<li><a href=\"showpost.php?id=$forums->id\"><h2>$forums->title</h2></a><a href=\"deletepost.php?id=$forums->id\">Delete</a> | <a href=\"modifypost.php?id=$forums->id\">Modify</a></li>";
}
?>
</ul>


</body>
</html>