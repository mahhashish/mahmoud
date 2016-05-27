<?php

/*******************************************************************
* This file is a generic interface to Aliro, Joomla 1.5+, Joomla 1.0.x and Mambo
* Copyright (c) 2008-10 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://acmsapi.org
* To contact Martin Brampton, write to martin@remository.com
*
*/

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

// Aliro error levels
if (!defined('_ALIRO_ERROR_INFORM')) define ('_ALIRO_ERROR_INFORM', 0);
if (!defined('_ALIRO_ERROR_WARN')) define ('_ALIRO_ERROR_WARN', 1);
if (!defined('_ALIRO_ERROR_SEVERE')) define ('_ALIRO_ERROR_SEVERE', 2);
if (!defined('_ALIRO_ERROR_FATAL')) define ('_ALIRO_ERROR_FATAL', 3);

if (!defined('_ALIRO_HTML_CACHE_TIME_LIMIT')) define('_ALIRO_HTML_CACHE_TIME_LIMIT', 600);
if (!defined('_ALIRO_HTML_CACHE_SIZE_LIMIT')) define('_ALIRO_HTML_CACHE_SIZE_LIMIT', 100000);
if (!defined('_ALIRO_OBJECT_CACHE_TIME_LIMIT')) define('_ALIRO_OBJECT_CACHE_TIME_LIMIT', 600);
if (!defined('_ALIRO_OBJECT_CACHE_SIZE_LIMIT')) define('_ALIRO_OBJECT_CACHE_SIZE_LIMIT', 100000);

if (!defined('_MOS_NOTRIM')) define( '_MOS_NOTRIM', 0x0001 );  		// prevent getParam trimming input
if (!defined('_MOS_ALLOWHTML')) define( '_MOS_ALLOWHTML', 0x0002 );		// cause getParam to allow HTML - purified on user side
if (!defined('_MOS_ALLOWRAW')) define( '_MOS_ALLOWRAW', 0x0004 );		// suppresses forcing of integer if default is numeric

if (!defined('_CMSAPI_CHARSET')) define ('_CMSAPI_CHARSET', 'utf-8');
if ('utf-8' == _CMSAPI_CHARSET) define ('_CMSAPI_LANGFILE', 'language-utf/');
else define ('_CMSAPI_LANGFILE', 'language/');

define ('_CMSAPI_PARAMETER_CLASS', 'aliroParameters');


class cmsapiInterface {
	protected static $instances = array();
	protected static $langdone = array();

	protected $cname = '';

	protected $magic_quotes_value = 0;
	protected $mainframe;

	protected function __construct ($cname) {
		$this->cname = $cname;
		$this->mainframe = aliroRequest::getInstance();
		// Is magic quotes on?
		if (get_magic_quotes_gpc()) {
		 	// Yes? Strip the added slashes
			$this->remove_magic_quotes($_REQUEST);
			$this->remove_magic_quotes($_GET);
			$this->remove_magic_quotes($_POST);
			$this->remove_magic_quotes($_FILES, 'name');
		}
		$this->magic_quotes_value = ini_get('magic_quotes_runtime');
		ini_set('magic_quotes_runtime', 0);
	}

	public function __destruct () {
		ini_set('magic_quotes_runtime',$this->magic_quotes_value);
	}

	public static function getInstance ($cname) {
		return (@self::$instances[$cname] instanceof self) ? self::$instances[$cname] : self::$instances[$cname] = new self($cname);
	}

	public function getVersion () {
		return '2.0.0';
	}

	public function getCMSVersion () {
		$version = version::getInstance();
		return $version->RELEASE.' '.$version->DEV_STATUS.' '.$version->DEV_LEVEL;
	}

	public function loadLanguageFile ($configuration=null, $forcelang=false, $alternative='', $special='') {
		if (empty(self::$langdone[$this->cname])) {
			$lang = $special ? $special : ($forcelang ? (empty($configuration->language) ? $this->getCfg('lang') : $configuration->language) : $this->getCfg('lang'));
			// May need config values for language files
			$mosConfig_sitename = $this->getCfg('sitename');
			if (is_object($configuration)) foreach (get_object_vars($configuration) as $k=>$v) $$k = $configuration->$k;
			if ($alternative AND is_readable(_CMSAPI_ABSOLUTE_PATH."/$alternative/$lang.php")) require_once(_CMSAPI_ABSOLUTE_PATH."/$alternative/$lang.php");
			if (is_readable(_CMSAPI_ABSOLUTE_PATH."/components/$this->cname/"._CMSAPI_LANGFILE.$lang.'.php')) require_once(_CMSAPI_ABSOLUTE_PATH."/components/$this->cname/"._CMSAPI_LANGFILE.$lang.'.php');
			elseif (is_readable(_CMSAPI_ABSOLUTE_PATH."/components/$this->cname/language/".$lang.'.php')) require_once(_CMSAPI_ABSOLUTE_PATH."/components/$this->cname/language/".$lang.'.php');
			require_once(_CMSAPI_ABSOLUTE_PATH."/components/$this->cname/language/"."english.php");
			self::$langdone[$this->cname] = true;
		}
	}

	protected function remove_magic_quotes (&$array, $keyname=null) {
		foreach ($array as $k => $v) {
			if (is_array($v)) $this->remove_magic_quotes($array[$k], $keyname);
			elseif (is_object($v)) continue;
			elseif (empty($keyname) OR $k == $keyname) $array[$k] = stripslashes($v);
		}
	}

	public function indexFileName ($name='index2') {
		return 'index.php';
	}

	public function doPurify ($string) {
		return $this->mainframe->doPurify($string);
	}

	public function class_exists ($string, $autoload=false) {
		return class_exists($string, $autoload);
	}

	public function getItemid ($component='com_remository') {
		return 0;
	}

	public function getIP() {
		return $this->mainframe->getIP();
	}

	public function getParameters () {
		$menu = $this->mainframe->getMenu();
		if ($menu) $params = new aliroParameters($menu->params);
		else $params = new aliroParameters();
		return $params;
	}
	
	public function getLocale () {
		// Assume it has been set correctly on the system
		return setlocale(LC_ALL, '0');
	}

	public function getCfg ($string) {
		return $this->mainframe->getCfg($string);
	}

	public function isFrontPage () {
		return 'com_frontpage' == $this->getParam($_REQUEST, 'option');
	}

	public function getTemplate () {
		return $this->mainframe->getTemplate();
	}

	public function setBase ($ref='') {}

	public function appendPathWay ($name, $link) {
		$this->mainframe->appendPathWay('<a href="'.$this->sefRelToAbs($link).'">'.$name.'</a>');
	}

	public function getDB () {
		return aliroDatabase::getInstance();
	}

	public function getEscaped ($string) {
		return aliroDatabase::getInstance()->getEscaped($string);
	}

	public function getParam ($arr, $name, $def='', $mask=0) {
		return $this->mainframe->getParam($arr, $name, $def, $mask);
	}

	public function getUser () {
		return aliroUser::getInstance();
	}

	public function getIdentifiedUser ($id) {
		$user = new aliroAnyUser();
		$user->load($id);
		if (!isset($user->groups)) $user->groups = array();
		return $user;
	}

	public function getCurrentItemid () {
		return 0;
	}

	public function getFromConfig ($component, $name, $default='') {
		// Remository does not support configuration via menu parameters
		return $default;
	}

	public function getUserStateFromRequest ($var_name, $req_name, $var_default=null) {
		return $this->mainframe->getUserStateFromRequest ($var_name, $req_name, $var_default);
	}

	public function getPath ($name, $option='') {
		return $this->mainframe->getPath($name, $option);
	}

	public function setPageTitle ($title) {
		$this->mainframe->SetPageTitle($title);
	}

	public function adminPageHeading ($text, $logo='generic') {
		if ('Joomla' == _CMSAPI_CMS_BASE) {
			JToolBarHelper::title($text, $logo);
		}
		else return <<<ADMIN_HEADING
			
				<tr>
					<th>
							$text
					</th>
				</tr>

ADMIN_HEADING;
		
	}
	public function prependMetaTag ($tag, $content) {
		$this->mainframe->prependMetaTag($tag, $content);
	}

	public function addCustomHeadTag ($tag) {
		$this->mainframe->addCustomHeadTag($tag);
	}

	public function addMetaTag ($name, $content, $prepend='', $append='') {
		$this->mainframe->addMetaTag($name, $content, $prepend='', $append='');
	}

	public function redirect ($url, $msg='') {
    	$this->mainframe->redirect($url, $msg);
    }

    function makePageNav ($total, $limitstart, $limit) {
		$pagenav = new cmsapiPageNav($total, $limitstart, $limit, $this->cname);
    	return $pagenav;
    }

    public function triggerMambots ($group, $event, $args=null, $doUnpublished=false) {
		// Aliro does not need group - accepted for compatibility
    	return aliroMambotHandler::getInstance()->trigger($event, $args, $doUnpublished);
    }

    public function invokeContentPlugins ($text) {
		$class = _CMSAPI_PARAMETER_CLASS;
		$param = new $class();
		$row = new stdClass();
		$row->text = $text;
		$results = aliroMambotHandler::getInstance()->trigger('onPrepareContent', array($row, $param), true);
		return $row->text;
    }

    public function getEditorContents ($hiddenField) {
		aliroEditor::getInstance()->getEditorContents ($hiddenField, $hiddenField);
    }

	public function editorArea($name, $content, $hiddenField, $width, $height, $col, $row) {
		echo $this->editorAreaText($name, $content, $hiddenField, $width, $height, $col, $row);
	}

	public function editorAreaText ($name, $content, $hiddenField, $width, $height, $col, $row) {
		$results = aliroMambotHandler::getInstance()->trigger('onEditorArea', array( $name, $content, $hiddenField, $width, $height, $col, $row ) );
		$html = '';
		foreach ($results as $result) $html .= trim($result);
		return $html;
	}

	public function makeImageURI ($imageName, $width=32, $height=32, $title='') {
		$element = '<img src="';
		$element .= $this->getCfg('live_site')."/components/{$this->cname}/images/".$imageName;
		$element .= '" width="';
		$element .= $width;
		$element .= '" height="';
		$element .= $height;
		if ($title) {
			$element .= '" title="';
			$element .= $title;
		}
		$element .= '" alt="" />';
		return $element;
	}

	public function objectSort ($objarray, $property, $direction='asc') {
		$GLOBALS['cmsapiSortProperty'] = $property;
		$GLOBALS['cmsapiDirection'] = strtolower($direction);
		usort( $objarray, create_function('$a,$b','
	        global $cmsapiSortProperty, $cmsapiDirection;
	        $result = strcmp($a->$cmsapiSortProperty, $b->$cmsapiSortProperty);
	        return \'asc\' == $cmsapiDirection ? $result : -$result;' ));
		return $objarray;
	}

	public function sefRelToAbs ($link) {
		return aliroSEF::getInstance()->sefRelToAbs($link);
	}

	public function sendMail ($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
		return mosMail ($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
	}

	public static function nameForURL ($name, $alias='') {
		if ($alias) $name = $alias;
		return aliroSEF::getInstance()->nameForURL($name);
	}

	public static function addDirectories ($directories, $cname, $admin=false, $initialize=false) {}

	public static function setAutoload ($path, $recurse=true) {}
	
	public static function setNewAutoload ($path, $recurse=true) {}
}
