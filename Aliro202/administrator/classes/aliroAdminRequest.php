<?php

class aliroAdminRequest extends aliroAbstractRequest {
	protected $prefix = 'admin';
	protected $core_item = '';
	protected $path_side = 'admin';
	protected $siteBaseURL = '';

	protected function __construct () {
		parent::__construct();
		if ($this->core_item = strtolower($this->getParam($_REQUEST, 'core'))) $this->component_name = $this->core_item;
		$this->siteBaseURL = $this->getCfg('admin_site');
	}

	public static function getInstance () {
    	if (self::$instance == null) self::$instance = new aliroAdminRequest();
		return self::$instance;
	}

	public function simpleURL () {
		$act = $this->getParam($_REQUEST, 'act');
		$task = $this->getParam($_REQUEST, 'task');
		$link = $this->siteBaseURL.'/index.php?'.($this->core_item ? 'core='.$this->core_item : 'option='.$this->option);
		if ($act) $link .= '&amp;act='.$act;
		if ($task) $link .= '&amp;task='.$task;
		return $link;
	}

	// Called only by the admin side index.php
	public function doControl () {
		aliroExtensionHandler::getInstance()->checkStarterPack();
		if ($this->option == 'login' OR $this->option == 'logout') {
			$authenticator = aliroAdminAuthenticator::getInstance();
			if ($this->option == 'logout') $authenticator->logout();
			else {
				$this->checkIP();
				$user = $authenticator->login();
				$this->alironoscript = $this->getStickyAliroParam($_POST, 'alironoscript');
				if (count($_POST)) $this->fixPostItems();
				$this->option = $this->component_name = strtolower($this->getParam($_REQUEST, 'option'));
				$this->core_item = strtolower($this->getParam($_REQUEST, 'core'));
			}
		}
		if (empty($user)) $user = aliroUser::getInstance();
		// Handle special admin side options
	    // If this is not login, we should already have a valid admin session
        if ($user->id) {
			if (isset($_SESSION['_aliro_admin_uri'])) {
				// Analyse the URI from admin login to set the live site
				$install = new aliroInstall();
				$config = aliroCore::getConfigData('configuration.php');
				$install->addLiveSite($config, $_SESSION['_aliro_admin_uri']);
				unset($_SESSION['_aliro_admin_uri']);
			}
			$this->adminActiveUser();
		}
	    // If a valid user was not set, the only possibility is to ask for an admin side login
	    else {
			$this->checkIP();
	    	// Renew the determination of the live site
			$_SESSION['_aliro_admin_uri'] = $_SERVER['REQUEST_URI'];
			if (!empty($_GET['oldpath']) AND 1 == count($_GET)) {
				aliroCache::deleteAll();
				unset($_GET['oldpath'], $install);
			}
			aliroCoreDatabase::getInstance()->changeDBContents();
	    	// Flush any diagnostic output
			ob_end_flush();
        	$template = $this->getTemplateObject();
        	$template->login();
			flush();
		}
	}
	
	protected function checkIP () {
		$database = aliroCoreDatabase::getInstance();
		if ($database->tableExists('#__admin_ip')) {
			$database->setQuery("SELECT address FROM #__admin_ip");
			$validip = $database->loadResultArray();
		}
		if (!empty($validip)) {
			$clientip = $this->getIP();
			foreach ($validip as $ip) {
				if (!filter_var($ip, FILTER_VALIDATE_IP)) $ip = gethostbyname($ip);
				if ($ip == $clientip) break;
			}
			if ($ip != $clientip) {
				$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP 1.1';
				header($protocol.' 404 Not Found');
				exit;
			}
		}
	}

	protected function adminActiveUser () {
        $mambothandler = aliroMambotHandler::getInstance();
        $mambothandler->trigger('beforeSystemStart', array($this));
        $mambothandler->trigger('onSystemStartAdmin', array($this));
		$this->commonHeaders();
		// If new system marker present, make sure database classes are started, then try to delete
		$newmarker = _ALIRO_ADMIN_CLASS_BASE.'/bootstrap/newsystem.html';
		if (file_exists($newmarker)) {
			if ($this->upgradeDatabase('aliroDatabase') AND $this->upgradeDatabase('aliroCoreDatabase')) unlink($newmarker);
		}
		if (file_exists($newmarker)) $this->setErrorMessage(sprintf(T_('New system marker %s cannot be deleted or DB upgrade failed, reduced efficiency'), $newmarker), _ALIRO_ERROR_WARN);
		if ($this->core_item OR ($this->option AND 'login' != $this->option)) $this->invokeComponent ();
		else {
			$moduleid = $this->getParam($_REQUEST, 'moduleid', 0);
			if ($moduleid) {
				$template = $this->getTemplateObject();
				aliroScreenArea::prepareTemplate($template);
			}
			else aliroAdminDashboard::showSQLdata();
		}

		$diagnostics = ob_get_clean();
		// Abandoned gzip within Aliro - better handled using Apache's mod_deflate
	    // if ($this->do_gzip) ob_start('ob_gzhandler');
	    echo $diagnostics;

        // If no_html is set, we avoid starting the template, and go straight to the component
        if ($this->getParam($_REQUEST, 'no_html', '')) {
		    @session_write_close();
        	echo $this->chandler->getBuffer();
			flush();
        	exit;
        }
        else {
	    	$this->insertMessageFromSession();
		    @session_write_close();
        	$template = $this->getTemplateObject();
        	// aliroTemplate::prepareTemplate($template);
        	$template->render();
        }
		// if ($this->do_gzip) ob_end_flush();
		flush();
    }
    
    protected function upgradeDatabase ($dbclass) {
    	$database = call_user_func(array($dbclass, 'getInstance'));
		if (method_exists($database, 'DBUpgrade')) try {
			$database->DBUpgrade();
			return true;
    	} catch (databaseException $exception) {
    		$message = sprintf(T_('A database error occurred on %s at %s while upgrading in %s'), date('Y-M-d'), date('H:i:s'), $dbclass);
    		$errorkey = "SQL/{$exception->getCode()}/$exception->dbname/{$exception->getMessage()}/$exception->sql";
    		aliroErrorRecorder::getInstance()->recordError($message, $errorkey, $message, $exception);
    		aliroRequest::getInstance()->setErrorMessage($message, _ALIRO_ERROR_SEVERE);
    		return false;
    	}
    }

    public function getTemplateObject () {
    	if ($this->templateObject == null) {
	    	$templateclass = aliroTemplateHandler::getInstance()->getDefaultTemplateClass();
    	   	$this->templateObject = new $templateclass();
    	}
		return $this->templateObject;
    }

    protected function getComponentClass ($component) {
    	return $component->adminclass;
    }

}
