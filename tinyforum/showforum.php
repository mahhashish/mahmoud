<!DOCTYPE html>
<html dir="rtl">
<head>
	<title>Forum LIST</title>
</head>
<body>
<?php
include_once('forumAPI.php');
include_once('session.php');

if($_SESSION['user_info'] == false){
    echo'<button><a href="login.php">Login</a></button>';
}else{
    $uname = $_SESSION['user_info']->name;
    echo'<button><a href="logout.php">Logout '.$uname.'</a></button>';
}

$forum = tinyf_forum_get();
if($forum == null){
	die('NULL!');
}

$fcount = count($forum);

if($fcount == 0){
	die('There Is No Forum');
}

?>

<ul type="square">
<?php
for($i = 0; $i < $fcount; $i++){
	$forums = $forum[$i];
	echo"<li><a href=\"forum.php?id=$forums->id\">
	<h2>$forums->title</h2></a>
	<h3>$forums->desc</h3>";
    
	if(($_SESSION['user_info'] != false) && ($_SESSION['user_info']->isadmin == 1)){
	    echo"<a href=\"deleteforum.php?id=$forums->id\">Delete</a>
     | <a href=\"modifyforum.php?id=$forums->id\">Modify</a>";
     echo"</li>";
	}
	
}
?>
</ul>

</body>
</html>

