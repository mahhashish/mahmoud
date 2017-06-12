<?php

include_once('db.php');

function tinyf_user_get($extra = ''){
	global $tf_handle;
	$query = sprintf("SELECT * FROM `user` %s", $extra);
	$qresult = mysql_query($query);

	if(!$qresult){
		return null;
	}

	$rcount = mysql_num_rows($qresult);
	if($rcount == 0){
		return null;
	}

	$user = array();
	for($i = 0; $i < $rcount; $i++){
		$user[count($user)] = mysql_fetch_object($qresult);
	}

	mysql_free_result($qresult);

	return $user;
}

function tinyf_user_get_by_id($uid){
	$id = (int) $uid;
	if($id == 0){
		return null;
	}
	$result = tinyf_user_get(' WHERE `id` = '.$id);
	if ($result == null){
		return null;
	}
	$user = $result[0];
	return $user;
}

function tinyf_user_get_by_name($name){
	global $tf_handle;
	$n_name = mysql_real_escape_string(strip_tags($name), $tf_handle);
	$result = tinyf_user_get("WHERE `name` = '$n_name'");
	if($result !== NULL){
		$user = $result[0];
	}
	else{
		$user = NULL;
	}
	return $user;
}

function tinyf_user_get_by_email($email){
	global $tf_handle;
	$n_email = mysql_real_escape_string(strip_tags($email), $tf_handle);
	$result = tinyf_user_get("WHERE `email` = '$n_email'");
	if($result !== NULL){
		$user = $result[0];
	}
	else{
		$user = NULL;
	}
	return $user;
}

function tinyf_user_add($name, $password, $email, $isadmin){
	global $tf_handle;
	if((empty($name)) || (empty($password)) || (empty($email)) ){
		return false;
	}
	$n_name    = mysql_real_escape_string(strip_tags($name), $tf_handle);

	$n_email   = mysql_real_escape_string(strip_tags($email), $tf_handle);
	if(!filter_var($n_email, FILTER_VALIDATE_EMAIL)){
		return false;
	}

	$n_pass    = md5(mysql_real_escape_string(strip_tags($password), $tf_handle));
	$n_isadmin = (int)$isadmin;

	if(($n_isadmin !== 0 ) && ($n_isadmin !== 1)){
		$n_isadmin = 0;
	}

	$query = sprintf("INSERT INTO `user` VALUE(NULL, '%s', '%s', '%s', %d)", $n_name, $n_pass, $n_email, $n_isadmin);

	$qresult = mysql_query($query);

	if(!$qresult){
		return false;
	}
	return true;
}

function tinyf_user_delete($uid){
	$id = (int) $uid;
	if($id == 0){
		return false;
	}
	$query = sprintf("DELETE FROM `user` WHERE `id` = %d", $id);
	$qresult = mysql_query($query);

	if(!$qresult){
		return false;
	}
	return true;
}

function tinyf_user_update($uid, $name = NULL, $password = NULL, $email = NULL, $isadmin = -1){
	global $tf_handle;
	$n_isadmin = (int) $isadmin;
	$id = (int) $uid;
	if($id == 0){
		return false;
	}
	
	$user = tinyf_user_get_by_id($id);
	if(!$user){
		return false;
	}
	
	if((empty($name)) && (empty($password)) && (empty($email)) && ($user->isadmin == $n_isadmin)){
		return false;
	}

	$fields = array();


	$query = "UPDATE `user` SET ";

	if(!empty($name)){
		$n_name = mysql_real_escape_string(strip_tags($name), $tf_handle);
		$fields[count($fields)] = "`name` = '$n_name'";	
	}
	

	if(!empty($password)){
		$n_pass = md5(mysql_real_escape_string(strip_tags($password), $tf_handle));
		$fields[count($fields)] = "`password` = '$n_pass'";
	}

	if(!empty($email)){
		
		$n_email = mysql_real_escape_string(strip_tags($email), $tf_handle);
		if(!filter_var($n_email, FILTER_VALIDATE_EMAIL)){
			return false;
		}

		$fields[count($fields)] = "`email` = '$n_email'";
	}

	if($n_isadmin == -1){
		$n_isadmin = $user->isadmin;
	}
	$fields[count($fields)] = "`isadmin` = '$n_isadmin'";

	$fcount = count($fields);

	if($fcount == 1){
		$query .= $fields[0]."WHERE `id` = ".$id;
		$qresult = mysql_query($query);
		if(!$qresult){
			return false;
		}else{
			return true;
		}
	}
	

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