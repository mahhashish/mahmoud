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
 * This file is mainly to hold the user side smart class mapper, but it also has
 * the aliroDebug class.  The latter is a simple singleton class that handles
 * debug data.  It receives debut data from the class mapper and the database,
 * and could be used by other functions.
 *
 * The smartClassMapper is used to find classes.  It has written into it the
 * locations for permanent classes on the user side, and separately holds locations
 * for external classes from third parties outside the Aliro project.  These are
 * from other open source projects.  The third source for class information is the
 * database, which contains details of installed classes.  On the user side, classes
 * that are in a file with the same name as the class are found automatically.
 *
 */

//function __autoload ($classname) {
//	if (HTMLPurifier_Bootstrap::autoload($classname)) return true;
//    aliro::getInstance()->requireClass($classname);
//}

// Debug Data Handler
class aliroDebug {
	private static $instance = __CLASS__;
	
	private $debug_log = array();

	private function __construct () { /* Enforce singleton */ }

	private function __clone () { /* Enforce singleton */ }

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	public function setDebugData ($info) {
		$this->debug_log[] = $info;
	}

	public function getLogged () {
		$text = '<h4>'.count($this->debug_log).' classes loaded</h4>';
	 	foreach ($this->debug_log as $k=>$class) $text .= "\n".($k+1)."<br />".$class.'<hr />';
		return $text;
	}

}

class smartClassMapper extends cachedSingleton {

	protected static $instance = __CLASS__;

	protected $dynamap = array();
	protected $debug_log = array();
	protected $timer = null;
	protected $populating = false;
	protected $classlist = array();
	protected $customlist = array();
	protected $oemlist = array();
	protected $subclasses = array();

	protected $classSQL = 'SELECT * FROM #__classmap WHERE side != "admin"';
	protected $admindir = '';

	protected $classmap = array (
	'mamboCore' => 'aliroCore',
	'database' => 'aliroDatabase',
	'mamboDatabase' => 'aliroDatabase',
	'joomlaDatabase' => 'aliroDatabase',
	'mosDBTable' => 'aliroDatabaseRow',
	'aliroDBRowFactory' => 'aliroDatabaseRow',
	'aliroUserTemplateBase' => 'aliroTemplateBase',
	'aliroMainTemplateBase' => 'aliroTemplateBase',
	'mosAdminMenus' => 'compatibilityClasses',
	'mosToolBar' => 'compatibilityClasses',
	'mosPathway' => 'aliroPathway',
	'aliroAnyUser' => 'aliroUser',
	'aliroCurrentUser' => 'aliroUser',
	'mosUser' => 'aliroUser',
	'mosMailer' => 'aliroMailer',
	//'aliroMailMessage' => 'aliroMailer',
	'mosCache' => 'aliroCache',
	'aliroFolderHandler' => 'aliroFolder',
	'aliroSessionData' => 'aliroSession',
	'aliroCommonExtHandler' => 'aliroExtensionHandler',
	'aliroUserPageNav' => 'aliroPageNav',
	'aliroAbstractPageNav' => 'aliroPageNav',
	'mosPageNav' => 'aliroPageNav',
	'cmsapiAuthorisationAdmin' => 'aliroAuthorisationAdmin',
	'cmsapiAuthoriser' => 'aliroAuthoriser',
	'cmsapiAuthoriserCache' => 'aliroAuthoriser',
	'cmsapiPaveNav' => 'aliroPageNav',
	'cmsapiMenuBar' => 'mosMenuBar',
	'cmsapiPane' => 'aliroTabs',
	'mosController' => 'mosRenderer',
	'mosCommand' => 'mosRenderer',
	'mosView' => 'mosRenderer',
	'aliroMenuItem' => 'aliroMenuHandler',
	'mosTabs' => 'aliroTabs',
	'aliroOldTabs' => 'aliroTabs',
	'aliroComponentHandler' => 'aliroComponent',
	'aliroApplicationHandler' => 'aliroComponent',
	'aliroFriendlyBase' => 'aliroComponentManager',
	'aliroComponentUserManager' => 'aliroComponentManager',
	'aliroUserScreenArea' => 'aliroScreenArea',
	'aliroAdminScreenArea' => 'aliroScreenArea',
	'aliroMambotHandler' => 'aliroMambot',
	'mosMambotHandler' => 'aliroMambot',
	'aliroModuleHandler' => 'aliroModule',
	//'aliroBasicXML' => 'aliroXML',
	//'aliroXMLDescription' => 'aliroXML',
	//'aliroXMLDefaultParams' => 'aliroXML',
	'aliroAdminParameters' => 'aliroParameters',
	'mosAdminParameters' => 'aliroParameters',
	'aliroSpecialAdminParameters' => 'aliroParameters',
	'mosParameters' => 'aliroParameters',
	'aliroLoginDetails' => 'aliroAuthenticator',
	'aliroUserAuthenticator' => 'aliroAuthenticator',
	'JApplicationHelper' => 'aliroJoomla',
	'JFactory' => 'aliroJoomla',
	'JRequest' => 'aliroJoomla',
	'JError' => 'aliroJoomla',
	'JURI' => 'aliroJoomla'
	);

	protected $extmap = array(
	'ArchieHTTP' => 'ArchieHTTP',
	'UniversalFeedCreator' => 'feedcreator.class',
	'cURL' => 'eac_curl_stream/eac_curl.class',
	'stream' => 'eac_curl_stream/eac_streams.class',
	'eacHttpRequest_auth' => 'eac_httprequest/eac_httprequest.auth',
	'eacHttpRequest_cache' => 'eac_httprequest/eac_httprequest.cache',
	'eacHttpRequest' => 'eac_httprequest/eac_httprequest.class',
	'eacCurlRequest' => 'eac_httprequest/eac_httprequest.curl',
	'eacSocketRequest' => 'eac_httprequest/eac_httprequest.socket',
	'eacStreamRequest' => 'eac_httprequest/eac_httprequest.stream',
	'htmlMimeMail5' => 'htmlMimeMail5',
	'Mail_MIMEPart' => 'mimePart',
	'Mail_RFC822' => 'RFC822',
	'smpt' => 'smtp',
	'HTMLPurifier' => 'HTMLPurifier',
	'HTMLPurifier_Config' => 'HTMLPurifier',
	'vCard' => 'vCard',
	'PclZip' => 'pclzip.lib',
	'Archive_Tar' => 'Tar',
	'PEAR' => 'PEAR',
	'HTMLPurifier_AttrTransform_ScriptRequired' => 'HTMLPurifier/HTMLPurifier_Script_Extension',
	'HTMLPurifier_HTMLModule_Scripting' => 'HTMLPurifier/HTMLPurifier_Script_Extension',
	'charsetmapping' => 'ConvertTables/charsetmapping',
	'PHPGettextFile' => 'phpgettext/phpgettext.file',
	'PHPGettextFilePOT' => 'phpgettext/phpgettext.file.pot',
	'PHPGettextFilePO' => 'phpgettext/phpgettext.file.po',
	'PHPGettextFileGLO' => 'phpgettext/phpgettext.file.glo',
	'PHPGettextFileMO' => 'phpgettext/phpgettext.file.mo',
	'PHPGettext' => 'phpgettext/phpgettext.class',
	'PHPGettextAdmin' => 'phpgettext/phpgettext.admin',
	'PHPGettext_Message' => 'phpgettext/phpgettext.message',
	'aliroUnaccent' => 'aliroUnaccent',
	'ConvertCharset' => 'ConvertCharset',
	'zipfile' => 'zipfile',
	'Services_JSON' => 'JSON',
	'Services_JSON_Error' => 'JSON',
	'xml2json' => 'xml2json',
	'YAHOO_util_Loader' => 'yui/phploader/loader',
	'JSMin' => 'minify/min/lib/JSMin',
	'Minify_CSS_Compressor' => 'minify/min/lib/Minify/CSS/Compressor',
	'swekey' => 'swekey/swekey',
	'my_swekey' => 'swekey/my_swekey',
	'Swift_DependencyContainer' => 'SwiftMailer/lib/classes/Swift/DependencyContainer',
	'Swift_Preferences' => 'SwiftMailer/lib/classes/Swift/Preferences'
	);

	protected function __construct () {
		$this->timer = new aliroProfiler('Time so far');
		$this->classlist = $this->listDir(_ALIRO_CLASS_BASE.'/classes');
		$this->customlist = $this->listDir(_ALIRO_CLASS_BASE.'/customclasses');
		$this->oemlist = $this->listDir(_ALIRO_CLASS_BASE.'/oemclasses');
	}
	
	protected function T_ ($string) {
		return function_exists('T_') ? T_($string) : $string;
	}

	protected function listDir ($path) {
		$dir = @opendir($path);
		if ($dir) {
	        while (false != ($file = readdir($dir))) if ('.php' == substr($file,-4)) $result[] = $file;
		    closedir($dir);
		}
        return isset($result) ? $result : array();
 	}

	public static function getInstance () {
		if (!is_object(self::$instance)) {
			self::$instance = parent::getCachedSingleton(self::$instance);
			self::$instance->reset();
		}
		self::$instance->checkDynamic();
		return self::$instance;
	}

	protected function checkDynamic () {
		if (aliro::getInstance()->installed AND 0 == count($this->dynamap) AND !$this->populating) {
			$this->populating = true;
			$this->populateMap();
			$this->populating = false;
			$this->cacheNow();
		}
	}
	
	public function reset () {
		$this->timer->reset();
	}

	protected function clearClassCaches ($immediate=false) {
		$this->dynamap = array();
		$this->populateMap();
		parent::clearCache($immediate);
	}

	public function __print () {
		return sprintf($this->T_('SmartClassMapper, %s dynamic items, % logs'), count($this->dynamap), count($this->debug_log));
	}
	
	protected function populateMap () {
	    $maps = aliroCoreDatabase::getInstance()->doSQLget($this->classSQL);
	    foreach ($maps as $map) {
			if ($map->extends) $this->subclasses[$map->extends][] = trim($map->classname);
	    	switch ($map->type) {
	    		case 'component':
	    		case 'application':
					$path = 'components/'.$map->formalname.'/';
					break;
	    		case 'module':
					$path = 'modules/'.$map->formalname.'/';
					break;
	    		case 'mambot':
					$path = 'mambots/'.$map->formalname.'/';
					break;
	    		case 'template':
					$path = 'templates/'.$map->formalname.'/';
					break;
	    		default: continue;
	    	}
			$this->saveMap(('admin' == $map->side ? $this->admindir.$path : $path), $map);
		}
		unset($maps);
	}

	public function getSubclasses ($classname='') {
		return isset($this->subclasses[$classname]) ? $this->subclasses[$classname] : ($classname ? array() : $this->subclasses);
	}
	
	public function timeSoFar () {
		return $this->timer->mark('seconds');
	}

	protected function getClassPath ($classname) {
		aliroDebug::getInstance()->setDebugData (sprintf('About to load %s, current used memory %s', $classname, (is_callable('memory_get_usage') ? memory_get_usage() : $this->T_('not known')).$this->timeSoFar()));
		$base = _ALIRO_CLASS_BASE.'/';
		if (in_array($classname.'.php', $this->customlist)) return $base.'customclasses/'.$classname.'.php';
	    if (isset($this->dynamap[$classname])) return $base.$this->dynamap[$classname].'.php';
		if (isset($this->classmap[$classname])) return $base.'classes/'.$this->classmap[$classname].'.php';
		if (isset($this->extmap[$classname])) return $base.'extclasses/'.$this->extmap[$classname].'.php';
		if (in_array($classname.'.php', $this->classlist)) return $base.'classes/'.$classname.'.php';
		if (in_array($classname.'.php', $this->oemlist)) return $base.'oemclasses/'.$classname.'.php';
	    return '';
	}

	public function requireClass ($classname) {
		$path = $this->getClassPath($classname);
		// echo '<br />Loading a class '; var_dump($path);
		if ($this->getClassModule($classname, $path)) return;
		usleep(200);
		if ($this->getClassModule($classname, $path)) return;
		$message = sprintf('Class %s not found, trying with path = %s', $classname, $path);
		trigger_error($message);
	}
	
	protected function getClassModule ($classname, $path) {
		if ($path AND is_readable($path)) require_once($path);
		return class_exists($classname, false);
	}
	
	public function classExists ($classname) {
		return $this->getClassPath($classname) ? true : false;
	}

	protected function saveMap ($path, $map) {
		$path .= $map->filename;
		$map->classname = trim($map->classname);
		if (false !== strpos($map->classname, '..')) {
			var_dump($map);
			die($this->T_('Class mapping includes illegal "..".'));
		}
		if (!isset($this->dynamap[$map->classname])) $this->dynamap[$map->classname] = $path;
		else trigger_error (sprintf('Class %s defined in %s but already defined in %s',$map->classname, $path, $this->dynamap[$map->classname]));
	}
	
	public static function autoloadClass ($classname='') {
		$mapper = _ALIRO_IS_ADMIN ? call_user_func(array('smartAdminClassMapper', 'getInstance')) : call_user_func(array('smartClassMapper', 'getInstance'));
		if ($classname) $mapper->requireClass($classname);
	}

	public static function insertClass ($type, $formalname, $side, $filename, $classname, $extends) {
		aliroCoreDatabase::getInstance()->doSQL("INSERT INTO #__classmap (type, formalname, side, filename, classname, extends)"
		." VALUES ('$type', '$formalname', '$side', '$filename', '$classname', '$extends')");
	}

	public static function clearAllCaches () {
		smartAdminClassMapper::getInstance()->clearClassCaches(true);
		smartClassMapper::getInstance()->clearClassCaches(true);
	}
}
