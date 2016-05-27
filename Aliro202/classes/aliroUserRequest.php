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
 * aliroUserRequest is the singleton request object for a user side request,
 * and contains the control logic for user side requests.  Most of its methods
 * are acquired by inheritance from aliroAbstractRequest
 *
 */

define ('_ALIRO_DATABASE_MONITOR_TIMEOUT', 30);

class aliroUserRequest extends aliroAbstractRequest {
	protected $prefix = 'user';
	protected $core_item = '';
	protected $path_side = 'front';
	protected $siteBaseURL = '';
	protected $error404 = false;

	protected function __construct () {
		parent::__construct();
		$max_query_time = (float) aliroCore::getInstance()->getCfg('max_query_time');
		if ($max_query_time > 0.01) $this->checkQueryTime($max_query_time);
		$this->pathway = aliroPathway::getInstance();
		$this->siteBaseURL = $this->getCfg('live_site');
	}
	public static function getInstance () {
    	if (self::$instance == null) self::$instance = new aliroUserRequest();
		return self::$instance;
	}
	
	public function set404 () {
		$this->error404 = true;
	}
	
	public function is404 () {
		return $this->error404;
	}

	// Called only by the user side index.php
	public function doControl () {
        $mambothandler = aliroMambotHandler::getInstance();
        $mambothandler->trigger('beforeSystemStart', array($this));
		if ('commandline' == @$_REQUEST['option']) {
			global $argv;
			if (isset($argv[1]) AND isset($argv[2])) {
				$commandobject = $this->getClassObject($argv[1]);
				if (is_object($commandobject) AND method_exists($commandobject, $argv[2])) {
					$args = array_slice($argv,3);
					exit (intval(call_user_func_array(array($commandobject, $argv[2]), $args)));
				}
			}
			exit (99);
		}
        $mambothandler->trigger('onSystemStartUser', array($this));
		if ($this->option == 'login' OR $this->option == 'logout') {
			if ($this->formcheck) $this->redirect(@$_SERVER['REQUEST_URI'], $this->getFormCheckError(), _ALIRO_ERROR_WARN);
			$authenticator = aliroUserAuthenticator::getInstance();
			if ($this->option == 'logout') $authenticator->logout();
			else $authenticator->userLogin();
		}
		if (strlen(@$_SERVER['REQUEST_URI']) < 2) {
			$this->isHome = true;
			$sefconfig = aliroComponentConfiguration::getInstance('cor_sef');
			if (!empty($sefconfig->google_verify)) $this->addMetaTag('verify-v1', $sefconfig->google_verify);
		}
		aliroSession::getSession()->rememberMe($this);
	    if ('com_login' != $this->option) $this->configuration->setRedirectHere();
		$this->commonHeaders();
		$indextype = $this->getParam($_REQUEST, 'indextype', 1);
	    if ($indextype == 2) {
			$this->urlerror = false;
			if ($this->getParam( $_REQUEST, 'do_pdf', 0 ) == 1 ) {
				aliroPDF::getInstance()->createPDF();
				flush();
				exit();
			}
		}
	    else {
		    $sef = aliroSEF::getInstance();
			list($appclass, $appmethod, $notemplate, $nohtml, $urilistid, $linkparm) = $sef->despatcher();
			if ($nohtml) $_REQUEST['no_html'] = 1;
			if ($appclass AND is_object($appobject = $this->getClassObject($appclass))) {
				$this->urilinkid = $urilistid;
				if (!$appmethod) $appmethod = 'activate';
		    	try {
					$this->chandler->startBuffer();
					// parameter allows standard component methods to be called - usually null
					$appobject->$appmethod($linkparm);
					$this->chandler->endBuffer();
		    	} catch (databaseException $exception) {
		    		$target = $this->core_item ? $this->core_item : $this->option;
		    		$message = sprintf(T_('A database error occurred on %s at %s while processing %s'), date('Y-M-d'), date('H:i:s'), $target);
		    		$errorkey = "SQL/{$exception->getCode()}/$appclass/$exception->dbname/{$exception->getMessage()}/$exception->sql";
		    		aliroErrorRecorder::getInstance()->recordError($message, $errorkey, $message, $exception);
					$protocol = empty($_SERVER['SERVER_PROTOCOL']) ? 'HTTP/1.1' : $_SERVER['SERVER_PROTOCOL'];
					header ($protocol.' 500 Server Error');
					exit;
		    	}
			}
			else {
				$this->urlerror = $sef->sefRetrieval();
				$indextype = 1;
				$this->option = $this->component_name = strtolower($this->getParam($_REQUEST, 'option'));
				$this->bestmatch = $this->mhandler->matchURL();
			}
	    }
	    /** detect first visit */
		// Should there be a condition on this?
		// $editor = aliroEditor::getInstance();
		// $this->initeditor = $editor->initEditor();
        $diagnostics = ob_get_clean();
		if (empty($appclass)) $this->invokeComponent($this->bestmatch);
		ob_start();
        // This is an opportunity for a mambot that writes additional headers
        $mambothandler->trigger('onHeaders', array($this));
        // Shouldn't be any real output, but flush any diagnostics now headers are written
        $diagnostics .= ob_get_clean();
        // Abandoned gzip within Aliro - better handled by Apache mod_deflate
	    // if ($this->do_gzip) ob_start('ob_gzhandler');
	    echo trim($diagnostics);
    	$template = $this->getTemplateObject();
	    if ($indextype == 1 AND empty($notemplate)) {
	    	$this->insertMessageFromSession();
	    	$this->runTemplate($template);
	        $mambothandler->trigger('afterTemplate', array($this->configuration));
	    }
	    elseif ($indextype == 2 OR !empty($notemplate)) $this->runNonTemplate($template);
		// if ($this->do_gzip) ob_end_flush();
		flush();
		aliroSEF::getInstance()->saveCache();
	}
	
	public function isHome () {
		return $this->isHome;
	}

	/*
	protected function userHeaders () {
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Content-type: text/html; '._ISO);
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
        if ($this->error404) header ($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
		if ('HEAD' == $_SERVER['REQUEST_METHOD']) exit;
	}
	*/

	protected function runTemplate ($template) {
        // loads template file
        aliroScreenArea::prepareTemplate($template);
        @session_write_close();
        $template->render();
        echo "<!-- ".time()." -->";
	}

	protected function runNonTemplate ($template) {
	    @session_write_close();
        if ($this->getParam($_REQUEST, 'no_html')) echo $this->chandler->getBuffer();
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
		new aliroPage403();
    }
	
	protected function checkQueryTime ($max_query_time) {
		$cache = new aliroCache('aliroUserRequest');
		$timeout = _ALIRO_DATABASE_MONITOR_TIMEOUT;
		$cache->setTimeout($timeout);
		$current = $cache->get('SQL_Average_Mean');
		if (is_null($current)) {
			$database = aliroCoreDatabase::getInstance();
			$database->setQuery("SELECT AVG(mean) FROM #__query_stats WHERE stamp > SUBDATE(NOW(), INTERVAL $timeout SECOND)");
			$current = (float) $database->loadResult();
			$cache->save($current);
		}
		if ((float)$current > $max_query_time) {
			$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP 1.1';
			header($protocol.' 503 Too busy, try again later');
			die($protocol.' 503 Server too busy. Please try again later.');
		}
	}
}
