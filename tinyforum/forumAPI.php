<?php

//FORUMS API.

include_once('db.php');

function tinyf_forum_get($extra = ''){
	global $tf_handle;
	$query = sprintf("SELECT * FROM `forums` %s", $extra);
	$qresult = mysql_query($query);

	if(!$qresult){
		return null;
	}

	$rcount = mysql_num_rows($qresult);
	if($rcount == 0){
		return null;
	}

	$forum = array();
	for($i = 0; $i < $rcount; $i++){
		$forum[count($forum)] = mysql_fetch_object($qresult);
	}

	mysql_free_result($qresult);

	return $forum;
}

function tinyf_forum_get_by_id($fid){
	$id = (int) $fid;
	if($id == 0){
		return null;
	}
	$result = tinyf_forum_get(' WHERE `id` = '.$id);
	if ($result == null){
		return null;
	}
	$forum = $result[0];
	return $forum;
}

function tinyf_forum_add($title, $desc){
	global $tf_handle;
	if((empty($title)) || (empty($desc)) ){
		return false;
	}
	$n_title   = mysql_real_escape_string(strip_tags($title), $tf_handle);

	$n_desc   = mysql_real_escape_string(strip_tags($desc), $tf_handle);

	$query = sprintf("INSERT INTO `forums` VALUES(NULL, '%s', '%s')", $n_title, $n_desc);

	$qresult = mysql_query($query);

	if(!$qresult){
		return false;
	}
	return true;
}

function tinyf_forum_delete($fid){
	$id = (int) $fid;
	if($id == 0){
		return false;
	}

	tinyf_forum_delete_all_posts($id);

	$query = sprintf("DELETE FROM `forums` WHERE `id` = %d", $id);
	$qresult = mysql_query($query);

	if(!$qresult){
		return false;
	}
	return true;
}

function tinyf_forum_update($fid, $title = NULL, $desc = NULL){
	global $tf_handle;
	
	$id = (int) $fid;
	if($id == 0){
		return false;
	}
	
	$forum = tinyf_forum_get_by_id($id);
	if(!$forum){
		return false;
	}
	
	if((empty($title)) && (empty($desc)) ){
		return false;
	}

	$fields = array();


	$query = "UPDATE `forums` SET ";

	if(!empty($title)){
		$n_title = mysql_real_escape_string(strip_tags($title), $tf_handle);
		$fields[count($fields)] = "`title` = '$n_title'";	
	}
	

	if(!empty($desc)){
		$n_desc = mysql_real_escape_string(strip_tags($desc), $tf_handle);
		$fields[count($fields)] = "`desc` = '$n_desc'";
	}

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

function tinyf_forum_delete_all_posts($fid){
	$id = (int) $fid;
	if($id == 0){
		return false;
	}
	$forum = tinyf_forum_get_by_id($id);
	if(!$forum){
		return false;
	}
	$topicq = "SELECT * FROM `posts` WHERE `fid` =".$id;
	$tresult = mysql_query($topicq);
	if(!$tresult){
		return false;
	}

	$tcount = mysql_num_rows($tresult);

	for($i = 0; $i < $tcount; $i++){
		$topic = mysql_fetch_object($tresult);
		mysql_query("DELETE FROM `posts` WHERE `pid` = ".$topic->id);
		mysql_query("DELETE FROM `posts` WHERE `id` = ".$topic->id);
	}
	mysql_free_result($tresult);
}

?>