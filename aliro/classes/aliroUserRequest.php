<?php

class aliroUserRequest extends aliroAbstractRequest {
	protected $prefix = 'user';
	protected $core_item = '';
	protected $path_side = 'front';
	protected $siteBaseURL = '';

	protected function __construct () {
		parent::__construct();
		$this->pathway = aliroPathway::getInstance();
		$this->siteBaseURL = $this->getCfg('live_site');
	}
	public static function getInstance () {
    	if (self::$instance == null) self::$instance = new aliroUserRequest();
		return self::$instance;
	}

	// Called only by the user side index.php
	public function doControl () {
		if ($this->option == 'login' OR $this->option == 'logout') {
			if ($this->formcheck) $this->redirect($_SERVER['REQUEST_URI'], $this->getFormCheckError(), _ALIRO_ERROR_WARN);
			$authenticator = aliroUserAuthenticator::getInstance();
			if ($this->option == 'logout') $authenticator->logout();
			else $authenticator->userLogin();
		}
		if (strlen($_SERVER['REQUEST_URI']) < 2) $this->isHome = true;
		aliroSessionFactory::getSession()->rememberMe($this);
	    $this->configuration->setRedirectHere();
		$indextype = $this->getParam($_REQUEST, 'indextype', 1);
	    if ($indextype == 2) $this->urlerror = 0;
	    else {
		    $sef = aliroSEF::getInstance();
	    	$this->urlerror = $sef->sefRetrieval();
	    	$indextype = 1;
			$this->option = $this->component_name = strtolower($this->getParam($_REQUEST, 'option'));
	    }
	    $this->bestmatch = $this->mhandler->matchURL();
        if ($this->configuration->getCfg('offline') AND (!aliroSession::isAdminPresent())) {
            @session_write_close();
           	new aliroOffline();
           	exit();
        }
	    if ($indextype == 2 AND $this->getParam( $_REQUEST, 'do_pdf', 0 ) == 1 ) {
	        include_once('includes/pdf.php');
	        exit();
	    }
	    /** detect first visit */
		// Should there be a condition on this?
		// $editor = aliroEditor::getInstance();
		// $this->initeditor = $editor->initEditor();
        $diagnostics = ob_get_clean();
		$this->invokeComponent($this->bestmatch);
		$this->userHeaders();
		ob_start();
        $mambothandler = aliroMambotHandler::getInstance();
        // This is an opportunity for a mambot that writes additional headers
        $mambothandler->trigger('onHeaders', array($this));
        // Shouldn't be any real output, but flush any diagnostics now headers are written
        $diagnostics .= ob_get_clean();
	    if ($this->do_gzip) ob_start('ob_gzhandler');
	    echo trim($diagnostics);
    	$template = $this->getTemplateObject();
	    if ($indextype == 1) {
	    	$this->runTemplate($template);
	        $mambothandler->trigger('afterTemplate', array($this->configuration));
	    }
	    elseif ($indextype == 2) $this->runNonTemplate($template);
		if ($this->do_gzip) ob_end_flush();
	}
	
	public function isHome () {
		return $this->isHome;
	}

	protected function userHeaders () {
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Content-type: text/html; '._ISO);
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
		if ('HEAD' == $_SERVER['REQUEST_METHOD']) exit;
	}

	protected function runTemplate ($template) {
        // loads template file
        aliroScreenArea::prepareTemplate($template);
        @session_write_close();
        $template->render();
        echo "<!-- ".time()." -->";
	}

	protected function runNonTemplate ($template) {
	    @session_write_close();
        if ($this->getParam($_REQUEST, 'no_html')) echo $this->chandler->mosMainBody();
        else $template->component_render();
	}

    public function getTemplateObject () {
    	if ($this->templateObject == null) {
	    	$templateclass = aliroTemplateHandler::getInstance()->getDefaultTemplateClass();
    	   	$this->templateObject = new $templateclass();
    	}
		return $this->templateObject;
    }

    protected function getComponentClass ($component) {
    	return $component->class;
    }

    public function notAuthorised () {
		new aliroPage404();
    }

}
