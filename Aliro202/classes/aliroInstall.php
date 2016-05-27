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
 * aliroInstall is the class that is instantiated to carry out initial installation of Aliro
 *
 * aliroInstallerFactory is a factory class that will provide an installer class - it will either
 * be the standard aliroInstall or oemInstall (if it exists) which allows for the creation of
 * applications built on Aliro that do not reveal Aliro or allow for other addons.
 *
 */

final class aliroInstall {

	protected $config = array();
	protected $configmade = false;
	protected $credentials = array();
	protected $corecredentials = array();
	protected $adminemail = '';
	protected $password = '';
	protected $gensamecore = false;
	protected $agreelicence = false;
	protected $error_message = array();
	protected $header = '';
	protected $title = '';
	protected $warnings = '';
	protected $productname = 'Aliro';
	protected $checkDirs = array(
	// Each entry is hide/visible, path, essential, admin also
	// Not used - needs more development
	array (false, '/cache/html', true, false),
	array (false, '/cache/singleton', true, false),
	array (false, '/cache/rssfeeds', true, false),
	array (false, '/cache/HTMLPurifier', true, false),
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
	protected $checkStatus = array();
	
	public function __construct () {
		$this->header = $this->headerText();
		$this->title = sprintf($this->T_('Install %s'),$this->productname);
	}
	
	protected function headerText () {
		return $this->T_('install the next generation Content Management System');
	}

	public function install () {
		aliroCache::deleteAll();
		$this->makeLocalConfig();
	    if (isset($_REQUEST['installform']) AND 'yes' == $_REQUEST['installform']) {
			$password = $this->createConfigs();
	    	if ($password) {
				$core = aliroCore::getInstance();
				$this->completionHTML($password);
	    	    return;
            }
		}
	    $this->showInstallForm();
	}
	
	protected function makeLocalConfig () {
		if ($this->configmade) return;
		else $this->configmade = true;
		$this->config['live_site'] = '';
		$this->config['secure_site'] = '';
		$this->config['unsecure_site'] = '';
		$this->config['subdirlength'] = 0;
	    $this->config['offline'] = false;
	    $this->config['lang'] = 'english';
	    $this->config['sitename'] = $this->T_('Aliro Powered Site');
	    $this->config['site_slogan'] = $this->T_('Next generation technology');
	    $this->config['offline_message'] = $this->T_('This site is down for maintenance.<br /> Please check back again soon.');
	    $this->config['error_message'] = $this->T_('This site is temporarily unavailable.<br /> Please notify the System Administrator');
	    $this->config['debug'] = false;
	    $this->config['lifetime'] = '900';
	    $this->config['adminlife'] = '3600';
		$this->config['privateip'] = false;
	    $this->config['MetaDesc'] = $this->T_('Aliro - the accessible, dynamic portal engine and content management system');
	    $this->config['MetaKeys'] = 'aliro, Aliro';
	    /*
	    $this->config['MetaAuthor'] = '1';
	    $this->config['MetaTitle'] = '1';
	    */
	    $this->config['locale'] = 'en';
	    $this->config['offset'] = '0';
	    $this->config['timezone'] = 'UTC';
	    /*
	    $this->config['hideAuthor'] = '1';
	    $this->config['hideCreateDate'] = '1';
	    $this->config['hideModifyDate'] = '1';
	    $this->config['hidePdf'] = '1';
	    $this->config['hidePrint'] = '1';
	    $this->config['hideEmail'] = '1';
	    $this->config['enable_stats'] = '0';
	    $this->config['vote'] = '0';
	    $this->config['multipage_toc'] = '1';
	    $this->config['link_titles'] = '0';
	    */
	    $this->config['error_reporting'] = '-1';
		$this->config['no_user_session'] = '0';
		$this->config['max_load'] = '0';
		$this->config['max_query_time'] = '0';
	    $this->config['list_limit'] = '15';
	    $this->config['caching'] = false;
	    $this->config['cachetype'] = 'Disk';
		$this->config['cache_server'] = 'localhost';
	    $this->config['cachepath'] = '';
	    $this->config['cachetime'] = '600';
	    $this->config['mailer'] = 'mail';
	    $this->config['mailfrom'] = '';
	    $this->config['fromname'] = $this->T_('Aliro Powered Site');
		$this->config['errormailto'] = '';
	    $this->config['sendmail'] = '/usr/sbin/sendmail';
	    $this->config['smtpauth'] = false;
	    $this->config['smtpuser'] = '';
	    $this->config['smtppass'] = '';
	    $this->config['smtphost'] = 'localhost';
	    // $this->config['item_navigation'] = '0';
	    // $this->config['pagetitles'] = '1';
	    // $this->config['readmore'] = '1';
	    // $this->config['icons'] = '1';
	    $this->config['favicon'] = 'favicon.ico';
	    $this->config['fileperms'] = '0644';
	    $this->config['dirperms'] = '0755';
	    // $this->config['mbf_content'] = '0';
	    $this->config['charset'] = 'utf-8';
	    $this->config['locale_use_iconv'] = false;
	    $this->config['locale_use_gettext'] = false;
	    $this->config['locale_debug'] = false;
	    // $this->config['mollom_pub'] = '';
	    // $this->config['mollom_priv'] = '';
		$this->config['s3accesskey'] = '';
		$this->config['s3secretkey'] = '';
		$this->config['s3bucket'] = '';
	}
	
	protected function T_($string) {
		// To be elaborated to provide translation for installation process
		return function_exists('T_') ? T_($string) : $string;
	}
	
    protected function setErrorMessage ($message, $severity=_ALIRO_ERROR_FATAL) {
    	$this->error_messages[$severity][] = $message;
    }

	protected function completionHTML ($password) {
		$adminsite = $_SERVER['SCRIPT_NAME'];
		$livesite = dirname(dirname($adminsite));
		$adminlogin = $this->T_('Administrator login');
		$installed = $this->T_('Installation');
		$completed = $this->T_('Installation completed');
   	    $adusertxt = $this->T_('The default administrator name is <strong>admin</strong>.  You can change this if you have user management installed.');
   	    $adpwtxt = $this->T_('The initial password for the default administrator is: ')."<strong>$password</strong>";
   	    $instmsg1 = sprintf($this->T_('The basic installation is complete.  You can visit the administrator side of the new site <a href="%s"> here </a>.'), $adminsite);
   	    $instmsg2 = $this->T_('You are strongly recommended to login as administrator right away and review the site configuration');
   	    $instmsg3 = sprintf($this->T_('Or you can visit the empty user side of the site %s here %s.'), '<a href="'.$livesite.'">', '</a>');
   	    echo <<<COMPLETION
{$this->topHTML()}
		<h2>$completed</h2>
		{$this->errorMessage()}
		<fieldset>
			<legend>$adminlogin</legend>
    	    $adusertxt
    	    $adpwtxt
		</fieldset>
    	<fieldset id="installdone">
			<legend>$installed</legend>
			$this->warnings
    	    $instmsg1
    	    $instmsg2
   	    	$instmsg3
		</fieldset>
{$this->bottomHTML()}

COMPLETION;

	}

	protected function createConfigs () {
		if (isset($_REQUEST['sitetitle'])) $this->config['sitename'] = $_REQUEST['sitetitle'];
		if (isset($_REQUEST['mailfrom'])) $this->config['mailfrom'] = $_REQUEST['mailfrom'];
		if (isset($_REQUEST['adminemail'])) $this->adminemail = $_REQUEST['adminemail'];
		if (!empty($_REQUEST['adminpassword'])) $this->password = $_REQUEST['adminpassword'];
		else $this->password = aliroAuthenticator::makePassword();
		if (!empty($_REQUEST['gensamecore'])) $this->gensamecore = true;
		if (empty($_REQUEST['agreelicence'])) $this->setErrorMessage($this->T_('Please agree to the Licence to continue'));
		else $this->agreelicence = true;
		$coreresult = $this->checkDatabaseConfig('core', $this->corecredentials);
		if (!$coreresult) $this->setErrorMessage($this->T_('The core database details were not valid'));
		if ($this->gensamecore) $genresult = $this->checkDatabaseConfig('core', $this->credentials);
		else $genresult = $this->checkDatabaseConfig('gen', $this->credentials);
		if (!$genresult) $this->setErrorMessage($this->T_('The general database details were not valid'));
		if ($coreresult AND $genresult AND $this->agreelicence) {
			$this->storeConfig ($this->corecredentials, 'corecredentials.php');
			$this->storeConfig ($this->credentials, 'credentials.php');

			try {
				$database = aliroCoreDatabase::getInstance();
				$sql = file_get_contents(_ALIRO_ADMIN_CLASS_BASE.'/sql/aliro_core.sql');
				$database->setQuery($sql);
				$database->query_batch();
				$database->clearCache();
	    	} catch (databaseException $exception) {
	    		printf($this->T_('A database error occurred on %s at %s while installing Aliro core tables: %s'), date('Y-M-d'), date('H:i:s'), $exception->getMessage());
	    		exit;
	    	}

	    	try {
				$database = aliroDatabase::getInstance();
				$sql = file_get_contents(_ALIRO_ADMIN_CLASS_BASE.'/sql/aliro_general.sql');
				$database->setQuery($sql);
				$database->query_batch();
				$database->clearCache();
				$database->setQuery ("SELECT COUNT(*) FROM #__users");
				$genusercount = $database->loadResult();
	    	} catch (databaseException $exception) {
	    		printf($this->T_('A database error occurred on %s at %s while installing Aliro general tables: %s'), date('Y-M-d'), date('H:i:s'), $exception->getMessage());
	    		exit;
	    	}

			$database = aliroCoreDatabase::getInstance();
			$database->setQuery ("SELECT COUNT(*) FROM #__core_users");
			$coreusercount = $database->loadResult();
			if ($coreusercount AND $genusercount) {
				$this->setErrorMessage($this->T_('Not an empty user table - left untouched. '), _ALIRO_ERROR_WARN);
				$this->setErrorMessage($this->T_('Site name and admin email not stored - please review config from admin interface. '),_ALIRO_ERROR_WARN);
				$this->password = $this->T_('same as it was before');
			}
			else {
				if ($coreusercount) $database->doSQL('TRUNCATE TABLE #__core_users');
				$adminuser = new aliroAnyUser();
				$adminuser->name = $adminuser->usertype = 'Super Administrator';
				$adminuser->username = 'admin';
				$adminuser->email = $this->adminemail;
				$adminuser->sendEmail = 1;
				$adminuser->gid = 25;
				$adminuser->registerDate = $adminuser->lastvisitDate = date('Y-m-d H:i:s');
				$adminuser->userStore($this->password);
				
				aliroAuthorisationAdmin::assignSuperAdmin($adminuser->id);

				aliroFileManager::getInstance()->mosChmodRecursive(_ALIRO_ABSOLUTE_PATH);
				aliroFileManager::getInstance()->mosChmodRecursive(_ALIRO_CLASS_BASE);
			}
			$this->addLiveSite($this->config, $_SERVER['REQUEST_URI']);
			// Initialise SEF configuration
			aliro::getInstance()->installed = true;
			sefAdminConfig::getInstance()->saveTask(false);

			return $this->password;
		}
		else return false;
	}

	protected function errorMessage () {
		if (empty($this->error_messages)) return '';
		$title = $this->T_('Install Errors');
		$colours = array (
		_ALIRO_ERROR_FATAL => 'red',
		_ALIRO_ERROR_SEVERE => 'red',
		_ALIRO_ERROR_WARN => 'orange',
		_ALIRO_ERROR_INFORM => 'gren'
		);
		if (count($this->error_messages)) {
			$messagehtml = '';
			foreach ($colours as $severity=>$colour) if (isset($this->error_messages[$severity])) {
				foreach ($this->error_messages[$severity] as $text) {
					$messagehtml .= <<<ONE_ERROR_MESSAGE
							<div style="color:$colour">
								$text
							</div>
ONE_ERROR_MESSAGE;
				}
			}
			$html = <<<FULL_MESSAGE_SET
					<!-- start Error Message area -->
					<fieldset>
						<legend>$title</legend>
							$messagehtml
					</fieldset>
					<!-- end Error Message area -->
FULL_MESSAGE_SET;

		}
		else $html = '';
		return $html;
	}
	
	public function getDefaultProperty ($property, &$isfound) {
		$this->makeLocalConfig();
		$isfound = isset($this->config[$property]);
		return $isfound ? $this->config[$property] : false;
	}

	public function storeConfig ($config, $configname, $overwrite=false) {
		$this->makeLocalConfig();
		foreach ($config as $key=>$value) {
			if ('configuration.php' != $configname OR isset($this->config[$key])) $config[$key] = base64_encode($value);
			else unset($config[$key]);
		}
		$packed = serialize($config);
		$filename = $this->makeFileName ($configname);
		$filepath = _ALIRO_CLASS_BASE.'/configs/'.$filename;
		clearstatcache();
		if (file_exists($filepath) AND !$overwrite) {
			$this->warnings .= '<span class="warning">'.sprintf($this->T_('Configuration file %s (encoded) already exists. ').'</span>', $configname);
		}
		$f = fopen ($filepath, 'wb');
		if ($f) {
			fwrite ($f, '<?php');
			fwrite ($f, "\n\t".'$packed = \''.$packed."';");
			fwrite ($f, "\n?>");
			fclose($f);
		}
		else $this->setErrorMessage(sprintf($this->T_('Unable to write configuration file %s'), $filepath), _ALIRO_ERROR_FATAL);
	}
	
	// Can only be called when there is reasonable confidence in $_SERVER['SCRIPT_NAME']
	// Must be called from the admin side as we are assumed to be running from a subdirectory of the home page
	public function addLiveSite (&$config, $uri) {
		$oldconfig = $config;
		$splituri = preg_split('#/index[0-9]?\.php#', $uri);
		$subdirectory = dirname($splituri[0]);
		if (1 == strlen($subdirectory)) $subdirectory = '';
		$config['subdirlength'] = strlen($subdirectory);
	    $_SERVER['HTTP_HOST'] = str_replace('joomla.', '', $_SERVER['HTTP_HOST']);
	    $_SERVER['SERVER_NAME'] = str_replace('joomla.', '', $_SERVER['SERVER_NAME']);
		$scheme = isset($_SERVER['HTTP_SCHEME']) ? $_SERVER['HTTP_SCHEME'] : ((isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS'] != 'off')) ? 'https' : 'http');
		if (isset($_SERVER['HTTP_HOST'])) {
			$withport = explode(':', $_SERVER['HTTP_HOST']);
			$servername = $withport[0];
			if (isset($withport[1])) $port = ':'.$withport[1];
		}
		elseif (isset($_SERVER['SERVER_NAME'])) $servername = $_SERVER['SERVER_NAME'];
		else trigger_error($this->T_('Impossible to determine the name of this server'), E_USER_ERROR);
		if (!isset($port) AND !empty($_SERVER['SERVER_PORT'])) $port = ':'.$_SERVER['SERVER_PORT'];
		if (isset($port)) {
			if (($scheme == 'http' AND $port == ':80') OR ($scheme == 'https' AND $port == ':443')) $port = '';
		}
		else $port = '';
		$afterscheme = '://'.$servername.$port.$subdirectory;
		$config['live_site'] = $config['secure_site'] = $_SESSION['aliro_live_site'] = $scheme.$afterscheme;
		$config['unsecure_site'] = $_SESSION['aliro_unsecure_site'] = 'http'.$afterscheme;
		if ($config != $oldconfig) $this->storeConfig($config, 'configuration.php');
	}

	protected function makeFileName ($configname) {
		$filename = md5(_ALIRO_ABSOLUTE_PATH.'/'.$configname).'.php';
		return $filename;
	}

	public function checkDatabaseConfig ($suffix, &$credentials) {
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
			$valid = aliroDatabaseHandler::validateCredentials($credentials['dbhost'], $credentials['dbusername'], $credentials['dbpassword'], $credentials['dbname']);
			if (_ALIRO_DB_CONNECT_FAILED == $valid OR _ALIRO_DB_NO_INTERFACE == $valid) $result = false;
		}
		return $result;
	}

	protected function showInstallForm () {
		$coredb = $this->T_('Core Database details');
		$gendb = $this->T_('General Database details');
		$submit = sprintf($this->T_('Install %s'), $this->productname);
		echo <<<FULL_HTML
{$this->topHTML()}
		{$this->errorMessage()}
		<form action="index.php" method="post">
		{$this->sayWelcome()}
		{$this->showLicence()}
		{$this->makeGeneralForm()}
		{$this->makeDBForm($coredb, 'core', $this->corecredentials, $this->T_('Credentials for a database table are always required here'))}
		{$this->makeDBForm($gendb, 'gen', $this->credentials, $this->T_('Ignore this section if you have ticked "General DB same as core DB"'))}
		<div class="clear"></div>
		{$this->extraFormFields()}
		<div>
			<input type="hidden" name="installform" value="yes" />
			<input type="submit" id="installaliro" value="{$this->submitButtonValue()}" />
		</div>
		</form>
{$this->bottomHTML()}

FULL_HTML;

	}
	
	// Can be overriden by oemInstall class
	protected function extraFormFields () {
		return '';
	}
	
	// Can be overriden by oemInstall class
	protected function submitButtonValue () {
		return sprintf($this->T_('Install %s'), $this->productname);
	}
	
	public function tellUserNotInstalled () {
		$header = $this->T_('welcomes you to a new web site');
		$title = $this->T_('New Aliro site');
		$message = $this->T_('This will be an Aliro based site, but installation is not yet completed.  Please call back later');
		$wheretogo = $this->T_("By default, the installation is started in the <a href=\"administrator\"><strong>administrator subdirectory</strong></a> but Aliro suppports altering the name of that directory.  If Aliro has been put here with a modified administrator directory name, the link given above will not work.");
		echo <<<NOT_INSTALLED
{$this->topHTML($header, $title, true)}
		<fieldset>
			<legend>$title</legend>
			<div id="inputset">
				<p>
					$message
				</p>
				<p>
					$wheretogo
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
	
	protected function topHTML ($header='', $title='', $userside=false) {
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
		<!-- Doesn't appear these are being used.  If so replace with YUI. -->
		<!--
		<script type="text/javascript" src="../includes/js/add-event.js"></script>	
		<script type="text/javascript" src="../includes/js/popup.js"></script>
		-->
	</head>
	<body>
	<div id="headerwrapper">
		<div id="header">
			<div id="logo">
				<h1>$this->productname</h1>
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
	
	protected function bottomHTML () {
		$version = new version();
		return <<<BOTTOM_HTML
	</div>
	<div id="footer">
		<p class="legal">Copyright &copy; 2008-12 Aliro Software Limited. All Rights Reserved.</p>
		{$version->footer()}
		<p class="credit">Template based on a design by <a href="http://www.freecsstemplates.org/">Free CSS Templates</a></p>
	</div>	
	</body>
</html>

BOTTOM_HTML;

	}
	
	protected function sayWelcome () {
		$welcome = sprintf($this->T_('Welcome to installing %s'), $this->productname);
		$wtext = $this->initialInstructions();
		return <<<SAY_WELCOME
		<fieldset>
			<legend>$welcome</legend>
			<p class="welcome">
				$wtext
			</p>
		</fieldset>
		
SAY_WELCOME;

	}
		
	protected function initialInstructions () {
		return $this->T_('You only need to fill in a few simple fields to get Aliro started!  If in doubt, please visit the <a href="http://www.aliro.org/index.php?option=com_text&amp;task=view&amp;id=5&amp;indextype=1" rel="popup standard 800 600"> Aliro Quick Start Guide</a>.');
	}
	
	protected function showLicence () {
		$licence = sprintf($this->T_('%s Licensing'), $this->productname);
		$agree = $this->licenceText();
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
	
	protected function licenceText () {
		return $this->T_('Aliro is in general distributed under the GNU Public Licence version 3+, but parts of Aliro are distributed under the Lesser GNU Public Licence 2.1+ so as to ensure that extension software is relatively unrestricted.  If in doubt about this matter, please contact Aliro.  Please tick the box to agree.');
	}

	protected function showFileNames () {
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

	public function makeDBForm ($legend, $suffix, $values, $intro='') {
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
			<p>
				$intro
			</p>
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

	protected function makeGeneralForm () {
		$legend = $this->T_('General Site Information');
		$titles = array (
		'sitetitle' => $this->T_('Site Title'),
		'adminpassword' => $this->T_('Admin Password'),
		'adminemail' => $this->T_('Admin Email'),
		'mailfrom' => $this->T_('Address from which System Emails should be sent'),
		'gensamecore' => $this->T_('General DB same as core DB (use only a single database)')
		);
		$html = <<<GENERAL_HTML

		<div id="installgeneral">
		<fieldset>
		<legend>$legend</legend>
			<div class="inputset"><label for="sitetitle">{$titles['sitetitle']}</label><br />
				<input id="sitetitle" type="text" name="sitetitle" value="{$this->config['sitename']}" size="80" />
			</div>
			<div class="inputset"><label for="adminpassword">{$titles['adminpassword']}</label><br />
				<input id="adminpassword" type="text" name="adminpassword" value="$this->password" size="80" />
			</div>
			<div class="inputset"><label for="adminemail">{$titles['adminemail']}</label><br />
				<input id="adminemail" type="text" name="adminemail" value="$this->adminemail" size="80" />
			</div>
			<div class="inputset"><label for="mailfrom">{$titles['mailfrom']}</label><br />
				<input id="mailfrom" type="text" name="mailfrom" value="{$this->config['mailfrom']}" size="80" />
			</div>
			<div class="inputset"><label for="gensamecore">{$titles['gensamecore']}</label><br />
				<input id="gensamecore" type="checkbox" {$this->isChecked($this->gensamecore)} name="gensamecore" value="1" />
			</div>
		</fieldset>
		</div>

GENERAL_HTML;

		return $html;
	}
	
	protected function isChecked ($value) {
		if ($value) return 'checked="checked"';
	}
}

