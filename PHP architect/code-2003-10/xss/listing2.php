<?
$token = md5(time());
$_SESSION['token'] = $token;
$_SESSION['token_timestamp'] = time();
?>
<form action="/add_post.php" method="post">
<input type="hidden" name="token" value="<? echo $token; ?>" />
<p>Subject: <input type="text" name="post_subject" /></p>
<p>Message: <textarea name="post_message"></textarea></p>
<p><input type="submit" value="Add Post" /></p>
</form>
