<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more
 * information on this, please see the index.php file or visit
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * aliroHTTPHeaders is a singleton class that supports the handling of
 * HTTP headers, both input and output.
 *
 */

class aliroHTTPHeaders {
	protected $nulldate = '';
	protected $http_protocol = 'HTTP/1.1';
	protected $myHeaders = array();
	
	public function __construct ($standardize=array('Authorization', 'If-Modified-Since', 'If-Unmodified-Since', 'Date', 'X-Date', 'Accept')) {
		$this->nulldate = aliroDatabase::getInstance()->defaultDate();
		if (isset($_SERVER['SERVER_PROTOCOL'])) $this->http_protocol = $_SERVER['SERVER_PROTOCOL'];
		$headers = apache_request_headers();
		foreach ($headers as $name=>$header) {
			foreach ($standardize as $standard) if (0 == strcasecmp($name, $standard)) {
				$this->myHeaders[$standard] = $header;
				continue 2;
			}
			$this->myHeaders[$name] = $header;
		}
	}
	
	public function getInputHeader ($name) {
		return isset($this->myHeaders[$name]) ? $this->myHeaders[$name] : false;
	}

	public function getAuthorization () {
		return $this->getInputHeader('Authorization');
	}
	
	public function getIfModifiedSince () {
		return $this->getInputHeader('If-Modified-Since');
	}
	
	public function getIfUnmodifiedSince () {
		return $this->getInputHeader('If-Unmodified-Since');
	}
	
	public function getDate () {
		return $this->getInputHeader('Date');
	}
	
	public function getAccept () {
		return $this->getInputHeader('Accept');
	}
	
	public function sendOK () {
		header($this->http_protocol.' 200 OK');
	}
	
	public function sendSimpleError ($message) {
		header($this->http_protocol.' '.$message);
		if ($this->willAcceptType('text/html')) echo $message;
	}
	
	public function unauthorizedRequest () {
		$this->sendSimpleError('401 Unauthorized');
		exit;
	}
	
	public function forbiddenRequest () {
		$this->sendSimpleError('403 Forbidden');
		exit;
	}
	
	public function redirectPermanent ($uri) {
		header($this->http_protocol.' 301 Moved Permanently');
		header('Location: '.$uri);
	}
	
	public function willAcceptType ($type) {
		$parts = explode(';', $this->getAccept());
		$accepts = array_map('trim', explode(',',$parts[0]));
		foreach ($accepts as $accept) if (0 == strcasecmp($accept, $type)) return true;
		return false;
	}

	public function compressedAccepted () {
		return $this->willAcceptType('application/cjson');
	}
	
	public function handleModifiedSince ($modified) {
		if ($this->nulldate == $modified) return;
		$since = $this->getIfModifiedSince();
		if ($since AND strtotime($since) > strtotime($modified)) {
			header($this->http_protocol.' 304 Not Modified');
			exit;
		}
		$unsince = $this->getIfUnmodifiedSince();
		if ($unsince AND strtotime($unsince) < strtotime($modified)) {
			header($this->http_protocol.' 412 Precondition Failed');
			exit;
		}
	}
	
	public function setCacheHeaders ($lifetime, $modified) {
		header('Expires: '.date('r', time()+$lifetime));
		header("Cache-Control: max-age=$lifetime, must-revalidate");
		if ($this->nulldate != $modified) header('Last-Modified: '.date('r', strtotime($modified)));
	}
	
	public function retryAfter ($seconds) {
		$random = mt_rand(75, 150);
		$retry = $seconds * ($random/100.0);
		header ('Retry-After: '.(int)$retry);
		$this->sendSimpleError('503 Service Unavailable');
		exit;
	}
}