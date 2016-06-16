<?php

/* A basic Aliro installer */

class aliroInstall {

	private $config = array();
	private $credentials = array();
	private $corecredentials = array();
	private $header = '';
	private $title = '';
	private $checkDirs = array(
	// Each entry is hide/visible, paath, essential, admin also
	array (false, '/cache/html', true, false),
	array (false, '/cache/singleton', true, false),
	array (false, '/configs', true, false),
	array (true, '/images/stories', true, false),
	array (true, '/images/banners', true, false),
	array (false, '/media', true, false),
	array (false, '/classes', false, true),
	array (false, '/components', false, true),
	array (false, '/includes', false, true),
	array (false, '/modules', false, true),
	array (true, '/templates', false, true),
	array (true, '/language', false, false),
	array (false, '/mambots', false, false),
	array (false, '/parameters', false, false)
	);
	private $checkStatus = array();
	
	public function __construct () {
		$this->header = $this->T_('install the next generation Content Management System');
		$this->title = $this->T_('Install Aliro');
	}

	public function install () {

	    $this->config['offline'] = '0';
	    $this->config['lang'] = 'english';
	    $this->config['sitename'] = $this->T_('Aliro Powered Site');
	    $this->config['shownoauth'] = '0';
	    $this->config['useractivation'] = '1';
	    $this->config['uniquemail'] = '1';
	    $this->config['offline_message'] = $this->T_('This site is down for maintenance.<br /> Please check back again soon.');
	    $this->config['error_message'] = $this->T_('This site is temporarily unavailable.<br /> Please notify the System Administrator');
	    $this->config['debug'] = '0';
	    $this->config['lifetime'] = '900';
	    $this->config['adminlife'] = '3600';
	    $this->config['MetaDesc'] = $this->T_('Aliro - the accessible, dynamic portal engine and content management system');
	    $this->config['MetaKeys'] = 'aliro, Aliro';
	    $this->config['MetaAuthor'] = '1';
	    $this->config['MetaTitle'] = '1';
	    $this->config['locale'] = 'en';
	    $this->config['offset'] = '0';
	    $this->config['hideAuthor'] = '1';
	    $this->config['hideCreateDate'] = '1';
	    $this->config['hideModifyDate'] = '1';
	    $this->config['hidePdf'] = '1';
	    $this->config['hidePrint'] = '1';
	    $this->config['hideEmail'] = '1';
	    $this->config['enable_log_items'] = '0';
	    $this->config['enable_log_searches'] = '0';
	    $this->config['enable_stats'] = '0';
	    $this->config['sef'] = '0';
	    $this->config['vote'] = '0';
	    $this->config['gzip'] = '0';
	    $this->config['multipage_toc'] = '1';
	    $this->config['link_titles'] = '0';
	    $this->config['error_reporting'] = '-1';
	    $this->config['register_globals'] = '0';
	    $this->config['list_limit'] = '15';
	    $this->config['caching'] = '0';
	    $this->config['cachepath'] = '';
	    $this->config['cachetime'] = '600';
	    $this->config['mailer'] = 'mail';
	    $this->config['mailfrom'] = '';
	    $this->config['fromname'] = $this->T_('Aliro Powered Site');
	    $this->config['sendmail'] = '/usr/sbin/sendmail';
	    $this->config['smtpauth'] = '0';
	    $this->config['smtpuser'] = '';
	    $this->config['smtppass'] = '';
	    $this->config['smtphost'] = 'localhost';
	    $this->config['back_button'] = '0';
	    $this->config['item_navigation'] = '0';
	    $this->config['pagetitles'] = '1';
	    $this->config['readmore'] = '1';
	    $this->config['hits'] = '0';
	    $this->config['icons'] = '1';
	    $this->config['favicon'] = 'favicon.ico';
	    $this->config['fileperms'] = '0644';
	    $this->config['dirperms'] = '0755';
	    $this->config['mbf_content'] = '0';
	    $this->config['charset'] = 'utf-8';
	    $this->config['locale_use_iconv'] = '0';
	    $this->config['locale_use_gettext'] = '0';
	    $this->config['locale_debug'] = '0';
	    if (isset($_REQUEST['installform']) AND 'yes' == $_REQUEST['installform']) {
	    	if ($password = $this->createConfigs()) {
				$core = aliroCore::getInstance();
				$this->completionHTML($password, $core->getCfg('live_site'), $core->getCfg('admin_site'));
	    	    return;
            }
		}
	    $this->showInstallForm();
	}
	
	private function T_($string) {
		// To be elaborated to provide translation for installation process
		return function_exists('T_') ? T_($string) : $string;
	}
	
	private function completionHTML ($password, $livesite, $adminsite) {
		$adminlogin = $this->T_('Administrator login');
		$installed = $this->T_('Installation');
		$completed = $this->T_('Installation completed');
   	    $adusertxt = $this->T_('The default administrator name is <strong>admin</strong>.  You can change this if you have user management installed.');
   	    $adpwtxt = $this->T_('The initial password for the default administrator is: ')."<strong>$password</strong>";
   	    $instmsg1 = sprintf($this->T_('The basic installation is complete.  You can visit the administrator side of the new site %s here %s.'), '<a href="'.$adminsite.'">', '</a>');
   	    $instmsg2 = $this->T_('You are strongly recommended to login as administrator right away and review the site configuration');
   	    $instmsg3 = sprintf($this->T_('Or you can visit the empty user side of the site %s here %s.'), '<a href="'.$livesite.'">', '</a>');
   	    echo <<<COMPLETION
{$this->topHTML()}
		<h2>$completed</h2>
		<fieldset>
			<legend>$adminlogin</legend>
    	    $adusertxt
    	    $adpwtxt
		</fieldset>
    	<fieldset id="installdone">
			<legend>$installed</legend>
    	    $instmsg1
    	    $instmsg2
    	    $instmsg3
		</fieldset>
{$this->bottomHTML()}

COMPLETION;

	}

	private function createConfigs () {
		$this->credentials = $this->checkDatabaseConfig('gen');
		if (!$this->credentials) echo '<br />'.$this->T_('The general database details were not valid');
		$this->corecredentials = $this->checkDatabaseConfig('core');
		if (!$this->corecredentials) echo '<br />'.$this->T_('The core database details were not valid');
		if ($this->corecredentials AND $this->credentials) {
			$this->storeConfig ($this->corecredentials, 'corecredentials.php');
			$this->storeConfig ($this->credentials, 'credentials.php');

			$database = aliroCoreDatabase::getInstance();
			$info = criticalInfo::getInstance();
			$sql = file_get_contents($info->absolute_path.'/administrator/sql/aliro_core.sql');
			$database->setQuery($sql);
			$database->query_batch();

			$database = aliroDatabase::getInstance();
			$sql = file_get_contents($info->absolute_path.'/administrator/sql/aliro_general.sql');
			$database->setQuery($sql);
			$database->query_batch();

			$database = aliroCoreDatabase::getInstance();
			$database->setQuery ("SELECT COUNT(*) FROM #__core_users");
			if ($database->loadResult()) {
				$this->storeConfig($this->config, 'configuration.php');
				echo '<br />'.$this->T_('Not an empty user table - left untouched');
				echo '<br />'.$this->T_('Site name and admin email not stored - please review config from admin interface');
				$password = $this->T_('same as it was before');
				echo aliroRequest::trace();
			}
			else {
				$authenticator = aliroAdminAuthenticator::getInstance();
				if (isset($_REQUEST['sitetitle'])) $this->config['sitename'] = $_REQUEST['sitetitle'];
				if (isset($_REQUEST['adminemail'])) $this->config['mailfrom'] = $_REQUEST['adminemail'];
				if (isset($_REQUEST['adminpassword'])) $password = $_REQUEST['adminpassword'];
				else $password = $authenticator->makePassword();
				$salt = $authenticator->makeSalt();
				$md5password = md5($salt.$password);
				$today = date('Y-m-d H:i:s');
				$database->doSQL ("INSERT INTO #__core_users (password, salt, activation) VALUES ('$md5password', '$salt', '')");
				$id = $database->insertid();
				$database->doSQL ("INSERT INTO #__assignments VALUES (0, 'aUser', $id, 'Super Administrator')");

				$database = aliroDatabase::getInstance();
				$database->doSQL ("INSERT INTO #__users VALUES ($id, 'Super Administrator', 'admin', '{$this->config['mailfrom']}', 'Super Administrator', 0, 1, 25, '$today', '$today', '')");
				
				aliroFileManager::getInstance()->mosChmodRecursive($info->absolute_path);
			}
			$this->storeConfig($this->config, 'configuration.php');
			aliro::getInstance()->installed = true;
			$this->errorMessage();

			return $password;
		}
		else return false;
	}

	private function errorMessage () {
		$colours = array (
		_ALIRO_ERROR_FATAL => 'red',
		_ALIRO_ERROR_SEVERE => 'red',
		_ALIRO_ERROR_WARN => 'orange',
		_ALIRO_ERROR_INFORM => 'gren'
		);
		$messages = aliroRequest::getInstance()->pullErrorMessages();
		if (count($messages)) {
			$messagehtml = '';
			foreach ($colours as $severity=>$colour) if (isset($messages[$severity])) {
				foreach ($messages[$severity] as $text) {
					$messagehtml .= <<<ONE_ERROR_MESSAGE
							<div style="color:$colour>
								$text
							</div>
ONE_ERROR_MESSAGE;
				}
			}
			$html = <<<FULL_MESSAGE_SET
					<!-- start Error Message area -->
					<div id="errormessage">
						$messagehtml
					</div>
					<!-- end Error Message area -->
FULL_MESSAGE_SET;

		}
		else $html = '';
		echo $html;
	}

	public function storeConfig ($config, $configname, $overwrite=false) {
		static $counter = 0;
		foreach ($config as &$item) $item = base64_encode($item);
		$packed = serialize($config);
		$filename = $this->makeFileName ($configname);
		$filepath = criticalInfo::getInstance()->class_base.'/configs/'.$filename;
		if (file_exists($filepath) AND !$overwrite) {
			echo '<br />'.sprintf($this->T_('Configuration file %s (encoded) already exists'), $configname);
		}
		if ($f = fopen ($filepath, 'wb')) {
			fwrite ($f, '<?php');
			fwrite ($f, "\n\t".'$packed = \''.$packed."';");
			fwrite ($f, "\n?>");
			fclose($f);
		}
		else echo '<br />'.sprintf($this->T_('Unable to write configuration file %s'), $filepath);
	}

	private function makeFileName ($configname) {
		$info = criticalInfo::getInstance();
		$filename = md5($info->absolute_path.'/'.$configname).'.php';
		return $filename;
	}

	public function checkDatabaseConfig ($suffix) {
		$result = true;
		if (isset($_REQUEST['dbhost'.$suffix])) $credentials['dbhost'] = $_REQUEST['dbhost'.$suffix];
		else $result = false;
		if (isset($_REQUEST['dbname'.$suffix])) $credentials['dbname'] = $_REQUEST['dbname'.$suffix];
		else $result = false;
		if (isset($_REQUEST['dbusername'.$suffix])) $credentials['dbusername'] = $_REQUEST['dbusername'.$suffix];
		else $result = false;
		if (isset($_REQUEST['dbpassword'.$suffix])) $credentials['dbpassword'] = $_REQUEST['dbpassword'.$suffix];
		else $result = false;
		if (isset($_REQUEST['dbprefix'.$suffix])) $credentials['dbprefix'] = $_REQUEST['dbprefix'.$suffix];
		else $result = false;
		if ($result) {
			$database = new database ($credentials['dbhost'], $credentials['dbusername'], $credentials['dbpassword'], $credentials['dbname'], $credentials['dbprefix'], true);
			if (_ALIRO_DB_CONNECT_FAILED == $database->getErrorNum() OR _ALIRO_DB_NO_INTERFACE == $database->getErrorNum()) $result = false;
		}
		if ($result) return $credentials;
		else return false;
	}

	private function showInstallForm () {
		$coredb = $this->T_('Core Database details');
		$gendb = $this->T_('General Database details');
		$submit = $this->T_('Install Aliro');
		echo <<<FULL_HTML
{$this->topHTML()}
		<form action="index.php" method="post">
		{$this->sayWelcome()}
		{$this->showLicence()}
		{$this->makeGeneralForm()}
		{$this->makeDBForm($coredb, 'core', $this->corecredentials)}
		{$this->makeDBForm($gendb, 'gen', $this->credentials)}
		<div>
			<input type="hidden" name="installform" value="yes" />
			<input type="submit" id="installaliro" value="$submit" />
		</div>
		</form>
{$this->bottomHTML()}

FULL_HTML;

	}
	
	public function tellUserNotInstalled () {
		$header = $this->T_('welcomes you to a new web site');
		$title = $this->T_('New Aliro site');
		$message = $this->T_('This will be an Aliro based site, but installation is not yet completed.  Please call back later');
		echo <<<NOT_INSTALLED
{$this->topHTML($header, $title, true)}
		<fieldset>
			<legend>$title</legend>
			<div id="inputset">
				<p>
					$message
				</p>
			</div>
		</fieldset>
{$this->bottomHTML()}
		
NOT_INSTALLED;
	}
		
	// Not yet sure if this will be needed
	public function installPage ($header, $title, $text, $userside=false) {
		echo <<<INSTALL_PAGE
{$this->topHTML($header, $title)}
	$text
{$this->bottomHTML()}
		
INSTALL_PAGE;
	}
	
	private function topHTML ($header='', $title='', $userside=false) {
		if (!$header) $header = $this->header;
		if (!$title) $title = $this->title;
		$predir = $userside ? '' : '../';
		return <<<TOP_HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<title>$title</title>
		<link href="{$predir}templates/install.css" rel="stylesheet" type="text/css" media="screen" />
		<script type="text/javascript" src="../includes/js/add-event.js"></script>	
		<script type="text/javascript" src="../includes/js/popup.js"></script>
	</head>
	<body>
	<div id="headerwrapper">
		<div id="header">
			<div id="logo">
				<h1>Aliro</h1>
				<h2>$header</h2>
				<a href="http://www.opensource.org/docs/definition.php">
					<img src="http://www.opensource.org/trademarks/opensource/web/opensource-75x65.gif" alt="Open Source (OSI) Logo" width="75" height="65" />
				</a>
			</div>
		</div>
	</div>
	<!-- end #headerwrapper -->
	<div id="page">

TOP_HTML;

	}
	
	private function bottomHTML () {
		$version = new version();
		return <<<BOTTOM_HTML
	</div>
	<div id="footer">
		<p class="legal">Copyright &copy; 2008 Aliro Software Limited. All Rights Reserved.</p>
		{$version->footer()}
		<p class="credit">Template based on a design by <a href="http://www.freecsstemplates.org/">Free CSS Templates</a></p>
	</div>	
	</body>
</html>

BOTTOM_HTML;

	}
	
	private function sayWelcome () {
		$welcome = $this->T_('Welcome to installing Aliro');
		$wtext = $this->T_('You only need to fill in a few simple fields to get Aliro started!  If in doubt, please visit the <a href="http://www.aliro.org/index.php?option=com_content&amp;task=view&amp;id=5&amp;indextype=2" rel="popup standard 800 600"> Aliro Quick Start Guide</a>.');
		return <<<SAY_WELCOME
		<fieldset>
			<legend>$welcome</legend>
			<p class="welcome">
				$wtext
			</p>
		</fieldset>
		
SAY_WELCOME;

	}
		
		
	
	private function showLicence () {
		$licence = $this->T_('Aliro Licensing');
		$agree = $this->T_('Aliro is in general distributed under the GNU Public Licence version 3+, but parts of Aliro are distributed under the Lesser GNU Public Licence 2.1+ so as to ensure that extension software is relatively unrestricted.  If in doubt about this matter, please contact Aliro.  Please tick the box to agree.');
		return <<<SHOW_LICENCE
		<fieldset>
			<legend>$licence</legend>
			<div id="inputset">
				<label for="agreelicence">$agree</label><br />
				<input type="checkbox" name="agreelicence" id="agreelicence" />
			</div>
		</fieldset>
		
SHOW_LICENCE;

	}

	private function showFileNames () {
		$configfiles = $this->T_('Configuration files');
		$cfile = $this->T_('Configuration file');
		$gdbcred = $this->T_('General DB credentials file');
		$cdbcred = $this->T_('Core DB credentials file');
		$html = <<<FILE_NAMES
		<fieldset>
			<legend>$configfiles</legend>
			<p><label for="configfilename">$cfile</label>
			<input type="text" id="configfilename" size="40" readonly="readonly" value="{$this->makeFileName('configuration.php')}" /></p>
			<p><label for="generalDBfilename">$gdbcred</label>
			<input type="text" id="generalDBfilename" size="40" readonly="readonly" value="{$this->makeFileName('credentials.php')}" /></p>
			<p><label for="coreDBfilename">$cdbcred</label>
			<input type="text" id="coreDBfilename" size="40" readonly="readonly" value="{$this->makeFileName('corecredentials.php')}" /></p>
		</fieldset>
		
FILE_NAMES;

		return $html;
	}

	public function makeDBForm ($legend, $suffix, $values) {
		$titles = array (
		'dbhost' => $this->T_('Host for database (commonly localhost)'),
		'dbname' => $this->T_('Database name'),
		'dbusername' => $this->T_('Database user name'),
		'dbpassword' => $this->T_('Database user password'),
		'dbprefix' => $this->T_('Prefix for database')
		);
		if (!isset($values['dbhost'])) $values['dbhost'] = 'localhost';
		if (!isset($values['dbname'])) $values['dbname'] = '';
		if (!isset($values['dbusername'])) $values['dbusername'] = '';
		if (!isset($values['dbpassword'])) $values['dbpassword'] = '';
		if (!isset($values['dbprefix'])) $values['dbprefix'] = 'aliro_';
		$values['dbdebug'] = 0;

		$html = <<<DB_HTML

		<div id="installdb$suffix">
		<fieldset>
		<legend>$legend</legend>
			<div class="inputset"><label for="dbhost$suffix">{$titles['dbhost']}</label><br />
				<input id="dbhost$suffix" type="text" name="dbhost$suffix" value="{$values['dbhost']}" size="40" />
			</div>
			<div class="inputset"><label for="dbname$suffix">{$titles['dbname']}</label><br />
				<input id="dbname$suffix" type="text" name="dbname$suffix" value="{$values['dbname']}" size="40" />
			</div>
			<div class="inputset"><label for="dbusername$suffix">{$titles['dbusername']}</label><br />
				<input id="dbusername$suffix" type="text" name="dbusername$suffix" value="{$values['dbusername']}" size="40" />
			</div>
			<div class="inputset"><label for="dbpassword$suffix">{$titles['dbpassword']}</label><br />
				<input id="dbpassword$suffix" type="text" name="dbpassword$suffix" value="{$values['dbpassword']}" size="40" />
			</div>
			<div class="inputset"><label for="dbprefix$suffix">{$titles['dbprefix']}</label><br />
				<input id="dbprefix$suffix" type="text" name="dbprefix$suffix" value="{$values['dbprefix']}" size="40" />
			</div>
		</fieldset>
		</div>

DB_HTML;

		return $html;
	}

	private function makeGeneralForm () {
		$legend = $this->T_('General Site Information');
		$titles = array (
		'sitetitle' => $this->T_('Site Title'),
		'adminpassword' => $this->T_('Admin Password'),
		'adminemail' => $this->T_('Admin Email')
		);
		$html = <<<GENERAL_HTML

		<div id="installgeneral">
		<fieldset>
		<legend>$legend</legend>
			<div class="inputset"><label for="sitetitle">{$titles['sitetitle']}</label><br />
				<input id="sitetitle" type="text" name="sitetitle" size="80" />
			</div>
			<div class="inputset"><label for="adminpassword">{$titles['adminpassword']}</label><br />
				<input id="adminpassword" type="text" name="adminpassword" size="80" />
			</div>
			<div class="inputset"><label for="adminemail">{$titles['adminemail']}</label><br />
				<input id="adminemail" type="text" name="adminemail" size="80" />
			</div>
		</fieldset>
		</div>

GENERAL_HTML;

		return $html;
	}
}