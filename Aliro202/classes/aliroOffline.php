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
 * aliroOffline simply makes a special display when the site is offline.
 *
 */


class aliroOffline {
	private $config = null;
	private $live_site = '';
	private $sitename = '';
	private $offline = 0;
	private $offline_message = '';
	private $error_message = '';
	private $install_warn = '';
	private $iso = '';
	private $request = null;

	private $error_text = '';

	public function __construct () {
		$this->config = aliroCore::getInstance();
		$this->config->fixlanguage();
		$this->live_site = $this->config->getCfg('live_site');
		$this->sitename = $this->config->getCfg('sitename');
		$this->offline = $this->config->getCfg('offline');
		$this->offline_message = $this->config->getCfg('offline_message');
		$this->error_message = $this->config->getCfg('error_message');
		$this->iso = _ISO;
		$this->install_warn = 'The site is offline for an unknown reason.';
		$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP 1.1';
		header ($protocol.' 500 Site Offline');
	}

	private function setError ($databaseSystemError=0) {
		$trace = $this->config->getCfg('debug') ? aliroBase::trace() : '';
		if ($this->offline) $this->error_text = <<<OFFLINE_BY_ADMIN

		<h2>
			$this->offline_message
		</h2>

OFFLINE_BY_ADMIN;

		elseif ($databaseSystemError) $this->error_text = <<<OFFLINE_BY_DATABASE

		<h2>
			$this->error_message
		</h2>
		<p>
			$databaseSystemError
		</p>
	
OFFLINE_BY_DATABASE;

		else $this->error_text = <<<OFFLINE_DEFAULT

		<h2>
		<p>
			$this->install_warn
		</p>
		<p>
			$trace
		</p>
		</h2>

OFFLINE_DEFAULT;

	}

	public function show ($databaseSystemError=0, $exception=null) {
		// needed to seperate the ISO number from the language file constant _ISO
		// $iso = explode( '=', _ISO );
		// xml prolog
		// echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';

		$this->setError($databaseSystemError);
		$trace = $this->config->getCfg('debug') ? aliroBase::trace() : '';
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$offline = <<<OFFLINE_HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; $this->iso" />
<title>$this->sitename - Offline</title>
<meta name="robots" content="noindex, nofollow" />
<link href="$this->live_site/templates/default.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>

<div id="offline">
	<div id="headerwrapper">
		<div id="header">
			<div id="logo">
				<h1><a href="$this->live_site">$this->sitename</a></h1>
				<h2><a href="http://www.aliro.org">powered by Aliro</a></h2>
			</div>
			
		</div>

	</div>
	<div id="page">
		<!-- start content -->
		<div id="content">
			<div id="offlinemessage">
				$this->error_text
			</div>
		</div>
	</div>

</div>
<div>
	$uri <br />
	$trace
</div>

</body>
</html>

OFFLINE_HTML;

		echo $offline;
		if ($exception) var_dump($exception);
		exit;
	}

}