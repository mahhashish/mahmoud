<?php

class mysqlInterface {
	private $resource = null;
	private $queries = array();
	private $qcursor = 0;

	public function __construct () {
	}

	public function connect ($host, $user, $pass, $db) {
		if ($this->resource = @mysql_connect( $host, $user, $pass ) AND mysql_select_db($db)) return $this->resource;
		return null;
	}

	public function connectError () {
		return mysql_error();
	}

	public function setCharset ($charset) {
		mysql_query ("SET CHARSET '$charset'");
	}

	public function query ($sql) {
		$this->cursor = mysql_query ($sql);
		return $this->cursor;
	}

	public function errno () {
		return mysql_errno();
	}
	
	public function error () {
		return mysql_error();
	}

	public function getEscaped($text) {
		return mysql_real_escape_string ((string) $text);
	}

	public function getNumRows ($cur=null) {
		return mysql_num_rows( $cur ? $cur : $this->cursor );
	}

	public function getAffectedRows () {
		return mysql_affected_rows();
	}

	public function insertid() {
		return mysql_insert_id($this->resource);
	}

	public function getVersion() {
		return mysql_get_server_info();
	}

	public function getFetchFunc() {
		return 'mysql_fetch_';
	}

	public function freeResultSet ($cur=null) {
		mysql_free_result($cur ? $cur : $this->cursor);
	}

	public function multiQuery ($sql) {
		$this->queries = explode (';', $sql);
		$this->qcursor = 0;
		return count($this->queries);
	}

	public function storeResult () {
		if (!empty($this->queries[$this->qcursor])) return $this->query ($this->queries[$this->qcursor]);
		return null;
	}

	public function nextResult () {
		$this->qcursor++;
		return isset($this->queries[$this->qcursor]);
	}

}