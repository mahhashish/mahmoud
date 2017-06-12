<?php
if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if($_id == 0){
	die('NO ID == 0');
}

include_once('postsAPI.php');

$post = tinyf_post_get_by_id($_id);
if($post == null){
	tf_db_close();
	die('NULL!');
}

?>
<!DOCTYPE html>
<html dir="rtl">
<head>
	<title><?php echo $post->title; ?></title>
</head>
<body>
<ul type="square">
<?php
	echo"<li><h1 style=\"color: #00ff99\">$post->title</h1><h3 style=\"color: #ff0099\">$post->content</h3><a href=\"deletepost.php?id=$post->id\">Delete</a> | <a href=\"modifypost.php?id=$post->id\">Modify</a><hr/></li>";
?>
</ul>
<?php

$r = tinyf_post_get_reply_by_id($_id);
tf_db_close();

if($r){
	$rcount = count($r);
	for ($i=0; $i < $rcount; $i++) { 
		$reply = $r[$i];
		echo"<h2 style=\"color: #ff0099\">$reply->title</h2><h4 style=\"color: #990000\">$reply->content</h4>";
	}
}

?>
<form action="savereply.php?id=<?php echo $post->id; ?>" method="post">
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
	<td><input type="submit" name="submit" value="Add Reply"></input></td>
	</tr>
</table>
</form>


</body>
</html>

