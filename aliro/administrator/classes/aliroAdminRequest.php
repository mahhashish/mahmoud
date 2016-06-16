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
		return $this->siteBaseURL.'/index.php?'.($this->core_item ? 'core='.$this->core_item : 'option='.$this->option);
	}

	// Called only by the admin side index.php
	public function doControl () {
		aliroExtensionHandler::getInstance()->checkStarterPack();
		if ($this->option == 'login' OR $this->option == 'logout') {
			$authenticator = aliroAdminAuthenticator::getInstance();
			if ($this->option == 'logout') $authenticator->logout();
			else {
				$this->user = $authenticator->login();
				$this->alironoscript = $this->getStickyAliroParam($_POST, 'alironoscript');
				if (count($_POST)) $this->fixPostItems();
				$this->option = $this->component_name = strtolower($this->getParam($_REQUEST, 'option'));
				$this->core_item = strtolower($this->getParam($_REQUEST, 'core'));
			}
		}
		// Handle special admin side options
	    // If this is not login, we should already have a valid admin session
        if (is_object($this->user) AND $this->user->id) $this->adminActiveUser();
	    // If a valid user was not set, the only possibility is to ask for an admin side login
	    else {
	    	// Flush any diagnostic output
			ob_end_flush();
        	$template = $this->getTemplateObject();
        	$template->login();
		}
	}

	protected function adminActiveUser () {
		if ($this->core_item OR $this->option) $this->invokeComponent ();
		else {
			$moduleid = $this->getParam($_REQUEST, 'moduleid', 0);
        	$template = $this->getTemplateObject();
        	aliroScreenArea::prepareTemplate($template);
		}

		$diagnostics = ob_get_clean();
	    if ($this->do_gzip) ob_start('ob_gzhandler');
	    echo $diagnostics;
	    @session_write_close();

        // If no_html is set, we avoid starting the template, and go straight to the component
        if ($this->getParam($_REQUEST, 'no_html', '')) {
        	echo $this->chandler->mosMainBody();
        	exit;
        }
        else {
        	$template = $this->getTemplateObject();
        	// aliroTemplate::prepareTemplate($template);
        	$template->render();
        }
		if ($this->do_gzip) ob_end_flush();
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