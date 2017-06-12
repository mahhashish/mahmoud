<?php
if(!isset($_GET['id'])){
	die('NO ID');
}

$_id = (int)$_GET['id'];

if($_id == 0){
	die('NO ID == 0');
}

include_once 'postsAPI.php';
include_once 'forumAPI.php';

$post = tinyf_post_get_by_id($_id);

if($post == null){
    tf_db_close();
	die('NO post');
}

$forum = tinyf_forum_get_by_id($post->fid);

if($post == null){
    tf_db_close();
    die('NO post');
}

?>
<!DOCTYPE html>
<html dir="rtl">
<head>
	<title>تعديل بوست: <?php echo $post->title; ?></title>
</head>
<body>
<form action="updatepost.php?id=<?php echo $_id; ?>" method="post">
<table align="center" style="width: 50%">
    <tr>
        <td align="center">
        <select name="forum" tabindex="1">
            <?php
            $forums = tinyf_forum_get();
            tf_db_close();
            
            $fcount = count($forums);
            for($i = 0; $i < $fcount; $i++){
                $forumx = $forums[$i];
                if($forumx->id == $forum->id){
                echo "<option value=\"$forumx->id\" selected>$forumx->title</option>";
                }
                else{
                    echo "<option value=\"$forumx->id\">$forumx->title</option>";
                }
            }
            ?>
        </select>
        </td>
    </tr>
	<tr>
		<td>العنوان:</td>
  		<td><input type="text" value="<?php echo $post->title; ?>" name="title"></td>
	</tr>
	<tr>
		<td>الوصف:</td>
  		<td><input type="text" value="<?php echo $post->content; ?>" name="content"></td>
	</tr>
	<tr>
	<td><input type="submit" name="submit" value="تعديل: <?php echo $post->title; ?>"></input></td>
	</tr>
</table>
</form>
</body>
</html>