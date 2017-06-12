<?php

//POSTS API.

include_once('db.php');

function tinyf_post_get($extra = ''){
	global $tf_handle;
	$query = sprintf("SELECT * FROM `posts` %s", $extra);
	$qresult = mysql_query($query);

	if(!$qresult){
		return null;
	}

	$rcount = mysql_num_rows($qresult);
	if($rcount == 0){
		return null;
	}

	$post = array();
	for($i = 0; $i < $rcount; $i++){
		$post[count($post)] = mysql_fetch_object($qresult);
	}

	mysql_free_result($qresult);

	return $post;
}

function tinyf_post_get_by_id($pid){
	$id = (int) $pid;
	if($id <= 0){
		return null;
	}
	$result = tinyf_post_get(' WHERE `id` = '.$id);
	if ($result == null){
		return null;
	}
	$post = $result[0];
	return $post;
}

function tinyf_post_get_reply_by_id($pid){
	$id = (int) $pid;
	if($id == 0){
		return null;
	}
	$result = tinyf_post_get(' WHERE `pid` = '.$id);
	if ($result == null){
		return null;
	}
	return $result;
}

function tinyf_post_add($fid, $pid, $uid, $title, $content){
	global $tf_handle;

	$_fid = (int)$fid;
	$_pid = (int)$pid;
	$_uid = (int)$uid;

	if(($fid == 0) /*|| ($uid == 0)*/){
		return false;
	}

	if((empty($title)) || (empty($content)) ){
		return false;
	}

	$n_title   = mysql_real_escape_string(strip_tags($title), $tf_handle);

	$n_content = mysql_real_escape_string(strip_tags($content), $tf_handle);

	$query = sprintf("INSERT INTO `posts` VALUES(NULL, %d, %d, %d, '%s', '%s')", $_fid, $_pid, $_uid, $n_title, $n_content);

	$qresult = mysql_query($query);

	if(!$qresult){
		return false;
	}
	return true;
}

function tinyf_reply_add($pid, $uid, $title, $content){
    global $tf_handle;

    $_pid = (int)$pid;
    $_uid = (int)$uid;

    if((empty($title)) || (empty($content)) ){
        return false;
    }

    $n_title   = mysql_real_escape_string(strip_tags($title), $tf_handle);

    $n_content = mysql_real_escape_string(strip_tags($content), $tf_handle);

    $query = sprintf("INSERT INTO `posts` VALUES(NULL, 0, %d, %d, '%s', '%s')", $_pid, $_uid, $n_title, $n_content);

    $qresult = mysql_query($query);

    if(!$qresult){
        return false;
    }
    return true;
}

function tinyf_post_delete_reply($pid){
	$id = (int) $pid;
	if($id == 0){
		return false;
	}
	$query = sprintf("DELETE FROM `posts` WHERE `pid` = %d", $id);
	$qresult = mysql_query($query);

	if(!$qresult){
		return false;
	}
	return true;
}

function tinyf_post_delete($pid){
	$id = (int) $pid;
	if($id == 0){
		return false;
	}
	tinyf_post_delete_reply($id);

	$query = sprintf("DELETE FROM `posts` WHERE `id` = %d", $id);
	$qresult = mysql_query($query);

	if(!$qresult){
		return false;
	}
	return true;
}

function tinyf_post_update($_id, $_fid = 0, $_pid = 0, $_uid = 0, $title = null, $content = null){
	global $tf_handle;

	$id  = (int) $_id;
	$fid = (int) $_fid;
	$pid = (int) $_pid;
	$uid = (int) $_uid;

	if($id <= 0){
		return false;
	}
	
	$post = tinyf_post_get_by_id($id);
	if(!$post){
		return false;
	}
	
	if((empty($title)) && (empty($content)) && ($post->fid == $fid) && ($post->pid == $pid) && ($post->uid == $uid) ){
		return false;
	}

	if($post->pid <= 0){
		$pid = 0;
		if($fid <= 0){
			$fid = $post->fid;
		}
	}
	else{
		$fid = $post->fid;
		if($pid <= 0){
			$pid = $post->pid;
		}
	}

	if($uid <= 0){
		$uid = $post->uid;
	}

	$fields = array();


	$query = "UPDATE `posts` SET ";

	if(!empty($title)){
		$n_title = mysql_real_escape_string(strip_tags($title), $tf_handle);
		$fields[count($fields)] = "`title` = '$n_title'";	
	}

	if(!empty($content)){
		$n_content = mysql_real_escape_string(strip_tags($content), $tf_handle);
		$fields[count($fields)] = "`content` = '$n_content'";
	}

	$fields[count($fields)] = "`fid` = $fid";
	$fields[count($fields)] = "`pid` = $pid";
	$fields[count($fields)] = "`uid` = $uid";

	$fcount = count($fields);

	for($i = 0; $i < $fcount; $i++){
		$query .= $fields[$i];
		if($i != ($fcount - 1)){
			$query .= ' , ';
		}
	}

	$query .= " WHERE `id` = ".$id;
	$qresult = mysql_query($query);

	if(!$qresult){
		return false;
	}else{
		return true;
	}	
}

?>