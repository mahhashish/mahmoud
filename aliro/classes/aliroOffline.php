<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Aliro is open source software, free to use, and licensed under GPL.
 * You can find the full licence at http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * The author freely draws attention to the fact that Aliro derives from Mambo,
 * software that is controlled by the Mambo Foundation.  However, this section
 * of code is totally new.  If it should contain any fragments that are similar
 * to Mambo, please bear in mind (1) there are only so many ways to do things
 * and (2) the author of Aliro is also the author and copyright owner for large
 * parts of Mambo 4.6.
 *
 * Tribute should be paid to all the developers who took Mambo to the stage
 * it had reached at the time Aliro was created.  It is a feature rich system
 * that contains a good deal of innovation.
 *
 * Your attention is also drawn to the fact that Aliro relies on other items of
 * open source software, which is very much in the spirit of open source.  Aliro
 * wishes to give credit to those items of code.  Please refer to
 * http://aliro.org/credits for details.  The credits are not included within
 * the Aliro package simply to avoid providing a marker that allows hackers to
 * identify the system.
 *
 * Copyright in this code is strictly reserved by its author, Martin Brampton.
 * If it seems appropriate, the copyright will be vested in the Aliro Organisation
 * at a suitable time.
 *
 * Copyright (c) 2007 Martin Brampton
 *
 * http://aliro.org
 *
 * counterpoint@aliro.org
 *
 * aliroOffline simply makes a special display when the site is offline.
 *
 */


class aliroOffline {
	private $live_site = '';
	private $sitename = '';
	private $offline = 0;
	private $offline_message = '';
	private $error_message = '';
	private $install_warn = '';
	private $iso = '';

	private $error_text = '';

	public function __construct ($mosSystemError=0) {
		$config = aliroCore::getInstance();
		$config->fixlanguage();
		$this->live_site = $config->getCfg('live_site');
		$this->sitename = $config->getCfg('sitename');
		$this->offline = $config->getCfg('offline');
		$this->offline_message = $config->getCfg('offline_message');
		$this->error_message = $config->getCfg('error_message');
		$this->iso = _ISO;
		$this->install_warn = 'For your security please completely remove the installation directory including all files and sub-folders  - then refresh this page';
		$this->setError($mosSystemError);
		$this->show();
	}

	private function setError ($mosSystemError) {
		if ($this->offline) $this->error_text = <<<OFFLINE_BY_ADMIN

	<tr>
		<td width="39%" align="center">
		<h2>
		$this->offline_message
		</h2>
		</td>
	</tr>

OFFLINE_BY_ADMIN;

		elseif ($mosSystemError) $this->error_text = <<<OFFLINE_BY_DATABASE

	<tr>
		<td width="39%" align="center">
		<h2>
		$this->error_message
		</h2>
		$mosSystemError
		</td>
	</tr>

OFFLINE_BY_DATABASE;

		else $this->error_text = <<<OFFLINE_DEFAULT

	<tr>
		<td width="39%" align="center">
		<h2>
		$this->install_warn
		</h2>
		</td>
	</tr>

OFFLINE_DEFAULT;

	}

	private function show () {
		// needed to seperate the ISO number from the language file constant _ISO
		// $iso = split( '=', _ISO );
		// xml prolog
		// echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';

		$offline = <<<OFFLINE_HTML

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>$this->sitename - Offline</title>
<meta http-equiv="Content-Type" content="text/html; $this->iso" />
</head>
<body>

<p>&nbsp;</p>
<table width="550" align="center" style="background-color: #ffffff; border: 1px solid">
<tr>
	<td width="60%" height="50" align="center">
	<img src="$this->live_site/images/logo.png" alt="Aliro Logo" align="middle" />
	</td>
</tr>
<tr>
	<td align="center">
	<h1>
	$this->sitename
	</h1>
	</td>
</tr>
$this->error_text
</table>

</body>
</html>

OFFLINE_HTML;

		echo $offline;
	}

}