<?php

class Sql
{
	var $host		= 'localhost';
	var $database;
	var $username;
	var $password;
	var $link;		// Holds the database resource
	var $result;	// Contains the result resource

	function Sql($host, $database, $username, $password) 
	{
		$this->host = $host;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		if ( !empty($host) )
			$this->connect();
	}

	function connect() 
	{
		$this->link = mysql_connect($this->host, $this->username, $this->password);
		mysql_select_db($this->database);
	}

	function query($query) 
	{
		$this->result = mysql_query($query, $this->link);
	}

	function nextRow() 	
	{
		if ( $this->result ) {
			if ( $this->row = mysql_fetch_assoc($this->result) )
				return true;
			else 
				return false;
		}
	}

	function getField($key)
	{
		return $this->row[$key];
	}
}

?>
