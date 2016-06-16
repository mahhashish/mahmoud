<?php

abstract class aliroAbstractRequest {
	// Singleton object holder - will contain the single instance of aliroUserRequest or aliroAdminRequest
	protected static $instance = null;

	// Request attributes
	protected $option = '';
	protected $isHome = false;
	protected $formcheck = 0;
	protected $component_name = '';
	protected $bestmatch = null;
	protected $aliroVersion = '';
	protected $urlerror = false;
	protected $title = '';
	protected $metatags = array();
	protected $customtags = array();
	protected $templateName = '';
	protected $templateObject = null;
	protected $do_gzip = false;
	protected $error_message = array();
	protected $overlib = false;

	// Core singleton objects providing key information resources
	protected $user = null;
	protected $critical = null;
	protected $configuration = null;
	protected $pathway = null;
	protected $version = null;

	// Singleton "handler" objects
	protected $mhandler = null;
	protected $chandler = null;
	protected $xhandler = null;
	protected $purifier = null;


	protected function __construct () {
		// This is not necessarily right - but should avoid getting a notice
		if (function_exists('date_default_timezone_set')) date_default_timezone_set('UTC');
        @set_magic_quotes_runtime( 0 );
        //require_once(criticalInfo::getInstance()->absolute_path.'/includes/phpgettext/phpgettext.class.php');
		// Note that none of the things called here can use aliroAbstractRequest!
		// Otherwise, a loop will be created and Aliro will fail!
		// Ensure session started straight away

		aliroSessionFactory::getSession();
		// Check for problems with globals - do after session has started to be able to handle session variables
		$this->handleGlobals();
		$this->setUsefulObjects();
        if (extension_loaded('zlib') AND $this->configuration->getCfg('gzip')) $this->do_gzip = true;
		$this->setHandlers();
		if (count($_POST)) $this->fixPostItems();
		$this->option = $this->component_name = strtolower($this->getParam($_REQUEST, 'option'));
		if ($this->option != 'login' AND $this->option != 'logout') $this->user = aliroUser::getInstance();
		if ($message = $this->getParam($_REQUEST, 'mosmsg')) {
			$severity = $this->getParam($_REQUEST, 'severity', _ALIRO_ERROR_INFORM);
			$this->setErrorMessage ($message, intval($severity));
		}
	}

	private function setHandlers () {
        $this->mhandler = aliroMenuHandler::getInstance();
        $this->chandler = aliroComponentHandler::getInstance();
        $this->xhandler = aliroExtensionHandler::getInstance();
	}

	private function setUsefulObjects () {
		$this->critical = criticalInfo::getInstance();
        // Initiate HTML Purifier autoloading
		if (function_exists('spl_autoload_register') AND function_exists('spl_autoload_unregister')) {
    		// HTML Purifier needs unregister for our pre-registering functionality
    		HTMLPurifier_Bootstrap::registerAutoload();
	        // Be polite and ensure that userland autoload gets retained
    	    spl_autoload_register('__autoload');
		}
		// End of HTML Purifier related code       
    	$this->version = version::getInstance();
   		$this->aliroVersion = $this->version->RELEASE.'/'.$this->version->DEV_STATUS.'/'.$this->version->DEV_LEVEL;
		$this->configuration = aliroCore::getInstance();
		$this->configuration->fixLanguage();
	}

	protected function fixPostItems () {
		$this->formcheck = $this->checkFormStamp();
		if (_ALIRO_FORM_CHECK_EXPIRED == $this->formcheck OR _ALIRO_FORM_CHECK_FAIL == $this->formcheck) {
			$this->setErrorMessage(T_('Sorry, your request used an invalid or expired form, please try again'));
			$_POST = array();
		}
		if (_ALIRO_FORM_CHECK_REPEAT == $this->formcheck) {
			$this->setErrorMessage(T_('This form submission has already been processed'));
			$_POST = array();
		}
		if ($params = $this->getParam($_POST, 'params', null, _MOS_ALLOWHTML)) {
			$pobject = new aliroParameters();
			$pobject->processInput($params);
			$_POST['params'] = $pobject->asString();
		}
		if (isset($_POST['alironstask']) AND (!isset($_REQUEST['task']) OR !$_REQUEST['task'])) $_POST['task'] = $_REQUEST['task'] = $_POST['alironstask'];
	}

	protected function __clone () {
		// Declared to enforce singleton
	}

	public function __call ($method, $args) {
		// May want to add language
		foreach (array($this->configuration, $this->pathway) as $object) {
			if (method_exists($object, $method)) return call_user_func_array(array($object, $method), $args);
		}
		trigger_error (sprintf(T_('Invalid method call on aliroRequest - %s'), $method));
		echo aliroRequest::trace();
		return null;
	}

	public function __get ($property) {
		if (isset($this->critical->$property)) return $this->critical->$property;
		trigger_error (sprintf(T_('Invalid property request on aliroAbstractRequest - %s'), $property));
		return null;
	}

    private function handleGlobals () {
        $superglobals = array($_SERVER, $_ENV, $_FILES, $_COOKIE, $_POST, $_GET, $_SESSION);

        // Emulate register_globals on
        if (!ini_get('register_globals') AND aliroCore::getInstance()->getCfg('register_globals')) {
        	foreach ($_GET as $key=>$value) {
                if (!isset($GLOBALS[$key])) $GLOBALS[$key]=$value;
            }
            foreach ($_POST as $key=>$value) {
                if (!isset($GLOBALS[$key])) $GLOBALS[$key]=$value;
            }
        }
        // Emulate register_globals off
        elseif (ini_get('register_globals') AND !$this->getCfg('register_globals')) {
            foreach ($superglobals as $superglobal) {
                foreach ($superglobal as $key=>$value) {
                    unset( $GLOBALS[$key]);
                }
            }
        }
    }

	public function getComponentName () {
		return $this->component_name;
	}

    public function showHead () {
        $html = aliroSEF::getInstance()->getHead($this->title, $this->metatags, $this->customtags);
        if ($this->getCfg('sef')) $html .= "<base href=\"{$this->getCfg('live_site')}/\" />\r\n";
        if ( $this->user->id ) $html .= "<script src='{$this->getCfg('live_site')}/includes/js/alirojavascript.js' type='text/javascript'></script>";
		return $html;
    }

    public function getFavIcon () {
        // Default favourites icon
        return $this->getCfg('live_site').'/images/favicon.ico';
    }

    public function getItemid () {
    	return isset($this->bestmatch) ? $this->bestmatch->id : 0;
    }

    public function getOption () {
    	return $this->option;
    }

    public function redirect ($url='', $message='', $severity=_ALIRO_ERROR_INFORM) {
    	if (is_null($url) OR !$url) $url = '';
    	else {
    		$url = $this->stripFromURL($url, 'mosmsg');
    		$url = $this->stripFromURL($url, 'severity');
    	}
    	if ($message AND !$url) $url = 'index.php';
		if (strpos($url, 'http') !== 0) {
			if ($url AND $url[0] != '/') $url = '/'.$url;
			$url = $this->siteBaseURL.$url;
		}
        if ($message) {
        	$url .= (strpos($url, '?') ? '&' : '?').'mosmsg='.urlencode($message);
        	if ($severity) $url .= '&severity='.intval($severity);
        }
        @session_write_close();
        if (headers_sent()) printf (T_('Please click on %s this link %s to continue'), "<a href='$url'>", '</a>');
        else {
            @ob_end_clean(); // clear output buffer
            header( "Location: $url" );
        }
        exit();
    }

    public function redirectSame ($message='', $severity=_ALIRO_ERROR_INFORM) {
    	$url = 'index.php?'.$_SERVER['QUERY_STRING'];
    	$this->redirect ($url, $message, $severity);
    }

    public function stripFromURL ($url, $property) {
    	if ($position = strpos($url, $property)) {
    		if ($endpos = strpos($url, '&', $position)) $url = substr($url, 0, $position).substr($url, $endpos+1);
    		else $url = substr($url, 0, $position-1);
    	}
		return $url;
    }

    public function setErrorMessage ($message, $severity=_ALIRO_ERROR_FATAL) {
    	$this->error_message[$severity][] = $message;
    }

    public function isErrorLevelSet ($severity) {
    	return isset($this->error_message[$severity]);
    }

    public function pullErrorMessages () {
    	$messages = $this->error_message;
    	$this->error_message = array();
    	return $messages;
    }

    public function getUserState( $var_name ) {
        return is_array($_SESSION["aliro_{$this->prefix}state"]) ? $this->getParam($_SESSION["aliro_{$this->prefix}state"], $var_name) : null;
    }

	public function setUserState( $var_name, $var_value ) {
        $_SESSION["aliro_{$this->prefix}state"][$var_name] = $var_value;
    }

    protected function isUserStateSet ($var_name) {
    	return isset($_SESSION["aliro_{$this->prefix}state"][$var_name]);
    }

    public function getUserStateFromRequest($var_name, $req_name, $var_default=null) {
        if (isset($_REQUEST[$req_name])) {
        	if ((string) $var_default == (string) (int) $var_default) $_REQUEST[$req_name] = intval($_REQUEST[$req_name]);
        	$this->setUserState($var_name, $_REQUEST[$req_name]);
        }
        elseif (isset($var_default) AND !$this->isUserStateSet($var_name)) $this->setUserState($var_name, $var_default);
        return $this->getUserState($var_name);
    }

    public function makeFormStamp () {
    	$formid = md5(uniqid(mt_rand(), true));
		$checker = md5(uniqid(mt_rand(), true));
		$_SESSION['aliro_formid_'.$formid] = $checker;
		$_SESSION['aliro_formdone_'.$formid] = 0;
		$html = <<<FORM_STAMP
		<input type="hidden" name="aliroformid" value="$formid" />
		<input type="hidden" name="alirochecker" value="$checker" />
FORM_STAMP;
		return $html;
    }

    public function getFormCheckError () {
    	$messages = array (
    	_ALIRO_FORM_CHECK_EXPIRED => T_('Sorry, the form you used has expired, please try again'),
    	_ALIRO_FORM_CHECK_FAIL => T_('Sorry, the form you used is invalid'),
    	_ALIRO_FORM_CHECK_NULL => T_('Sorry, the form you used did not have a required authentication'),
    	_ALIRO_FORM_CHECK_REPEAT => T_('The form you used has already been processed')
    	);
    	if ($this->formcheck) {
	    	if (isset($messages[$this->formcheck])) return $messages[$this->formcheck];
	    	else return T_('Internal error - invalid form check value');
    	}
    	else return '';
    }

    private function checkFormStamp () {
    	$formid = $this->getParam($_POST, 'aliroformid');
    	$checker = $this->getParam($_POST, 'alirochecker');
    	if ($formid) {
    		if (!isset($_SESSION['aliro_formid_'.$formid])) return _ALIRO_FORM_CHECK_EXPIRED;
    		if ($_SESSION['aliro_formid_'.$formid] == $checker) {
    			if ($_SESSION['aliro_formdone_'.$formid]) return _ALIRO_FORM_CHECK_REPEAT;
    			else {
    				$_SESSION['aliro_formdone_'.$formid] = 1;
    				return _ALIRO_FORM_CHECK_OK;
    			}
    		}
    		else {
    			$this->setErrorMessage(T_('Form failed consistency check'), _ALIRO_ERROR_FATAL);
    			return _ALIRO_FORM_CHECK_FAIL;
    		}
    	}
    	else return _ALIRO_FORM_CHECK_NULL;
    }

	public function getParam( &$arr, $name, $def=null, $mask=0 ) {
	    if (isset( $arr[$name] )) {
	        if (is_array($arr[$name])) foreach ($arr[$name] as $key=>$element) {
	        	$result[$key] = $this->getParam ($arr[$name], $key, $def, $mask);
	        }
	        else {
	            $result = $arr[$name];
	            if (!($mask&_MOS_NOTRIM)) $result = trim($result);
	            if (!is_numeric($result)) {
	            	if (get_magic_quotes_gpc() AND !($mask & _MOS_NOSTRIP)) $result = stripslashes($result);
	                if (!($mask&_MOS_ALLOWRAW) AND is_numeric($def)) $result = $def;
	                elseif ($result) {
	                	if ($mask & _MOS_ALLOWHTML) $result = $this->doPurify($result);
		                else {
							$result = strip_tags($result);
							// $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
						}
	                }
	            }
	        }
	        return $result;
	    }
	    return $def;
	}

	public function doPurify ($string) {
		if (null == $this->purifier) {
	  		$config = HTMLPurifier_Config::createDefault();
	   		if (criticalInfo::getInstance()->isAdmin) $config->set('HTML', 'Trusted', true);
	  		$this->purifier = new HTMLPurifier($config);
		}
		return $this->purifier->purify($string);
	}

	// Cannot be applied to items that return an array, only to a scalar
	public function getStickyParam (&$arr, $name, $def=null, $mask=0) {
		$var = 'aliro_sticky_'.$this->getComponentName().'_'.$name;
		return $this->getSticky ($var, $arr, $name, $def=null, $mask=0);
	}

	public function getStickyAliroParam (&$arr, $name, $def=null, $mask=0) {
		$var = 'aliro_sticky_aliro_'.$name;
		return $this->getSticky ($var, $arr, $name, $def=null, $mask=0);
	}

	private function getSticky ($var, &$arr, $name, $def, $mask) {
		if ((!isset($arr[$name]) OR !$arr[$name]) AND isset($_SESSION[$var])) return $_SESSION[$var];
		$provided = $this->getParam($arr, $name, $def, $mask);
		if ($provided) $_SESSION[$var] = $provided;
		return $provided;
	}

	public function unstick ($name) {
		$var = 'aliro_sticky_'.$this->getComponentName().'_'.$name;
		if (isset($_SESSION[$var]))	unset ($_SESSION[$var]);
	}

	public function getTemplate() {
		if (!$this->templateName) $this->templateName = aliroTemplateHandler::getInstance()->getDefaultTemplateName();
		return $this->templateName;
    }

    public function setPageTitle ($title=null) {
        if ($this->getCfg('pagetitles')) {
            $title = trim($title);
            $base = $this->getCfg('sitename');
            $this->title = $title ?  $title.' - '.$base : $base;
        }
    }

    public function getPageTitle () {
        return $this->title;
    }

    protected function fix_metatag ($operation, $name, $content, $prepend='', $append='') {
    	$content = trim(htmlspecialchars($content));
		if (!$content) return;
    	$name = trim(htmlspecialchars($name));
        $prepend = trim($prepend);
        $append = trim($append);
    	if ('new' == $operation) $this->metatags[$name] = array($content, $prepend, $append);
    	else {
    		$tag = isset($this->metatags[$name]) ?  $this->metatags[$name] : array('', '', '');
    		if ('pre' == $operation) $tag[0] = $content.$tag[0];
			else $tag[0] = $content.(($tag[0] AND $content) ? ',' : '').$tag[0];
			$this->metatags[$name] = $tag;
    	}
    }

	public function addMetaTag($name, $content, $prepend='', $append='') {
		$this->fix_metatag ('new', $name, $content, $prepend, $append);
	}

    public function appendMetaTag ($name, $content) {
    	$this->fix_metatag ('post', $name, $content);
    }

    public function prependMetaTag ($name, $content) {
    	$this->fix_metatag ('pre', $name, $content);
    }

    public function addCustomHeadTag ($html) {
        $this->customtags[] = trim ($html);
    }

	public function addScript ($relativeFile) {
		$link = <<<SCRIPT_LINK

	<script type="text/javascript" src="{$this->getCfg('live_site')}$relativeFile"></script>

SCRIPT_LINK;

		$this->addCustomHeadTag($link);
	}

	public function addCSS ($relativeFile, $media='screen') {
		$link = <<<CSS_LINK

	<link href="{$this->getCfg('live_site')}$relativeFile" rel="stylesheet" type="text/css" media="$media" />

CSS_LINK;

		$this->addCustomHeadTag($link);
	}

    public function setMetadataInCache (&$cache_object) {
    	$cache_object->title = $this->title;
    	$cache_object->metatags = $this->metatags;
    	$cache_object->customtags = $this->customtags;
    }

    public function setMetadataFromCache ($cache_object) {
    	$this->title = $cache_object->title;
    	$this->metatags = $cache_object->metatags;
    	$this->customtags = $cache_object->customtags;
    }

    public function requestOverlib () {
    	if ($this->overlib) return;
		$html = <<<OVERLIB
		<script type="text/javascript" src="{$this->getCfg('live_site')}/includes/js/overlib_mini.js"></script>
OVERLIB;
		$this->addCustomHeadTag ($html);
		$this->overlib = true;
    }

    public function divOverlib () {
    	if ($this->overlib) return '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>';
    	return '';
    }

    public function getDebug () {
    	if ($this->getCfg('debug')) {
			$database = aliroDatabase::getInstance();
			$log = $database->getLogged();
			$database = aliroCoreDatabase::getInstance();
			$log .= $database->getLogged();
			$loader = aliroDebug::getInstance();
			$log .= $loader->getLogged();
			return $log;
    	}
    	else return '';
    }

    public function getCustomTags () {
        if (count($this->customtags)) return implode("\n", $this->customtags);
        return '';
	}

    public function getComponentObject () {
    	if ($this->core_item) {
			$component = new aliroComponent();
			$component->option = $component->extformalname = $this->core_item;
			$component->name = $this->core_item;
			$component->adminclass = 'aliroComponentAdminManager';
    	}
    	else $component = $this->chandler->getComponentByFormalName($this->option);
    	return $component;
    }

    protected function invokeComponent ($menu=null) {
    	try {
			$this->chandler->startBuffer();
			if (!$this->option AND $menu AND $menu->component) $this->option = $menu->component;
			$component = $this->getComponentObject();
			$message = T_('At entry of aliroRequest::invokeComponent');
			if (!$this->urlerror AND ($this->option OR $this->core_item)) {
				$componentname = $this->option? $this->option : $this->core_item;
				define ('_ALIRO_COMPONENT_NAME', $componentname);
				if ($component) {
					if ($this->pathway) {
						$cname = aliroSEF::getInstance()->sefComponentName($component->option);
						$this->pathway->addItem($cname, 'index.php?option='.$component->option);
					}
					$class = $this->getComponentClass($component);
					if ($class) $this->standardCall ($component, $class, $menu);
					else $this->urlerror = $this->retroCall ($menu);
					if ($this->urlerror) trigger_error(T_('Retro call was unable to find component: ').$this->option);
				}
				else {
					$this->urlerror = true;
					$message = T_('Unable to find component object for ').$this->option;
				}
			}
			else {
				$this->urlerror = true;
				if ($this->chandler->componentCount() AND $this->mhandler->getMenuCount('mainmenu')) {
    				$message = sprintf(T_('Failed on urlerror from SEF or no option (%s)'), $this->option);
				}
			}
			if ($this->urlerror) new aliroPage404($message);
			$this->chandler->endBuffer();
    	} catch (databaseException $exception) {
    		$target = $this->core_item ? $this->core_item : $this->option;
    		$message = sprintf(T_('A database error occurred on %s at %s while processing %s'), date('Y-M-d'), date('H:i:s'), $target);
    		$errorkey = "SQL/{$exception->getCode()}/$target/$exception->dbname/{$exception->getMessage()}/$exception->sql";
    		aliroErrorRecorder::getInstance()->recordError($message, $errorkey, $message, $exception);
    		$this->redirect('', $message, _ALIRO_ERROR_FATAL);
    	}
    }

    protected function standardCall ($component, $class, $menu) {
		$worker = new $class ($component, 'Aliro', $this->aliroVersion, $menu);
		$worker->activate();
    }

    protected function retroCall ($menu) {
		$mainframe = mosMainFrame::getInstance();
		$path = $mainframe->getPath($this->path_side);
		if (!$path) return true;
       	$this->invokeRetroCode($path, null, $menu);
       	return false;
    }

    public function invokeRetroCode ($path, $function=null, $menu=null) {
       	$GLOBALS['task'] = $task = $this->getParam($_REQUEST, 'task');
       	$GLOBALS['act'] = $act = $this->getParam($_REQUEST, 'act');
   		$GLOBALS['id'] = $id = $this->getParam($_REQUEST, 'id', 0);
   		$GLOBALS['section'] = $section = $this->getParam($_REQUEST, 'section');
		require_once ($this->critical->absolute_path.'/includes/mambofunc.php');
       	$GLOBALS['acl'] = $acl = aliroAuthoriser::getInstance();
       	$GLOBALS['my'] = $my = aliroUser::getInstance();
		$GLOBALS['gid'] = $gid = $my->gid;
       	$GLOBALS['mainframe'] = $mainframe = mosMainFrame::getInstance();
       	$GLOBALS['database'] = $database = aliroDatabase::getInstance();
       	$GLOBALS['Itemid'] = $Itemid = $this->getItemid();
       	$GLOBALS['option'] = $option = $this->option;
       	$GLOBALS['_VERSION'] = $this->version;

       	// This will not do - what should happen??
       	$GLOBALS['mosConfig_lang'] = 'english';

       	error_reporting(E_ALL);
       	$this->globalizeConfig();
       	foreach ($GLOBALS as $key=>$value) if ('mosConfig_' == substr($key,0,10)) $$key = $value;
       	require($path);
       	if ($function) $function();
       	error_reporting(E_ALL|E_STRICT);
    }

}