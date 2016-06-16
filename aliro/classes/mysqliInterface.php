<?php

class mysqliInterface {
	private $resource = null;
	private $cursor = null;

	public function __construct () {
	}

	public function connect ($host, $user, $pass, $db) {
		$this->resource = @mysqli_connect($host, $user, $pass, $db);
		return $this->resource;
	}

	public function connectError () {
		return mysqli_connect_error();
	}

	public function setCharset ($charset) {
		$this->resource->set_charset($charset);
	}

	public function query ($sql) {
		$this->cursor = mysqli_query($this->resource, $sql);
		return $this->cursor;
	}

	public function errno () {
		return mysqli_errno($this->resource);
	}
	
	public function error () {
		return mysqli_error($this->resource);
	}

	public function getEscaped($text) {
		return mysqli_real_escape_string($this->resource, (string) $text);
	}

	public function getNumRows ($cur=null) {
		return mysqli_num_rows( $cur ? $cur : $this->cursor );
	}

	public function getAffectedRows () {
		return mysqli_affected_rows($this->resource);
	}

	public function insertid() {
		return mysqli_insert_id($this->resource);
	}

	public function getVersion() {
		return mysqli_get_server_info($this->resource);
	}

	public function getFetchFunc() {
		return 'mysqli_fetch_';
	}

	public function freeResultSet ($cur=null) {
		mysqli_free_result($cur ? $cur : $this->cursor);
	}

	public function multiQuery ($sql) {
		return mysqli_multi_query($this->resource, $sql);
	}

	public function storeResult () {
		return mysqli_store_result($this->resource);
	}

	public function nextResult () {
		return mysqli_next_result($this->resource);
	}

}