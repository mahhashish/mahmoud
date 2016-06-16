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

function __autoload ($classname) {
	if (HTMLPurifier_Bootstrap::autoload($classname)) return true;
    aliro::getInstance()->requireClass($classname);
}

// Debug Data Handler
class aliroDebug {
	private static $instance = __CLASS__;
	
	private $debug_log = array();

	private function __construct () { /* Enforce singleton */ }

	private function __clone () { /* Enforce singleton */ }

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
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

	private static $instance = __CLASS__;

	protected $dynamap = array();
	protected $debug_log = array();
	protected $timer = null;
	protected $populating = false;

	protected $classmap = array (
	'mamboCore' => 'aliroCore',
	'aliroAbstractDatabase' => 'aliroDatabase',
	'aliroDatabaseHandler' => 'aliroDatabase',
	'aliroCoreDatabase' => 'aliroDatabase',
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
	'mosUser' => 'aliroUser',
	'mosMailer' => 'aliroMailer',
	'aliroSimpleCache' => 'aliroCache',
	'mosCache' => 'aliroCache',
	'aliroFolderHandler' => 'aliroFolder',
	'aliroSessionFactory' => 'aliroSession',
	'aliroSessionData' => 'aliroSession',
	'aliroExtension' => 'aliroExtensionHandler',
	'aliroCommonExtHandler' => 'aliroExtensionHandler',
	'aliroUserPageNav' => 'aliroPageNav',
	'aliroAbstractPageNav' => 'aliroPageNav',
	'mosPageNav' => 'aliroPageNav',
	'aliroDirectory' => 'aliroFileManager',
	'mosController' => 'mosRenderer',
	'mosCommand' => 'mosRenderer',
	'mosView' => 'mosRenderer',
	'aliroMenuItem' => 'aliroMenuHandler',
	'mosTabs' => 'aliroTabs',
	'aliroComponentHandler' => 'aliroComponent',
	'aliroFriendlyBase' => 'aliroComponentManager',
	'aliroComponentUserManager' => 'aliroComponentManager',
	'aliroUserScreenArea' => 'aliroScreenArea',
	'aliroAdminScreenArea' => 'aliroScreenArea',
	'aliroMambotHandler' => 'aliroMambot',
	'mosMambotHandler' => 'aliroMambot',
	'aliroModuleHandler' => 'aliroModule',
	'aliroXMLParamsDefault' => 'aliroXMLParams',
	//'aliroBasicXML' => 'aliroXML',
	//'aliroXMLDescription' => 'aliroXML',
	//'aliroXMLDefaultParams' => 'aliroXML',
	'aliroAdminParameters' => 'aliroParameters',
	'mosAdminParameters' => 'aliroParameters',
	'aliroSpecialAdminParameters' => 'aliroParameters',
	'mosParameters' => 'aliroParameters',
	'aliroLoginDetails' => 'aliroAuthenticator',
	'aliroUserAuthenticator' => 'aliroAuthenticator',
	'aliroAdminAuthenticator' => 'aliroAuthenticator',
	'JApplicationHelper' => 'aliroJoomla',
	'JFactory' => 'aliroJoomla',
	'JRequest' => 'aliroJoomla',
	'JError' => 'aliroJoomla'
	);

	protected $extmap = array(
	'ArchieHTTP' => 'ArchieHTTP',
	'UniversalFeedCreator' => 'feedcreator.class',
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
	'zipfile' => 'zipfile'
	);

	protected function __construct () {
		$this->timer = new aliroProfiler('Time so far');
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

	public function clearCache () {
		$this->dynamap = array();
		$this->populateMap();
		parent::clearCache();
	}

	public function __print () {
		return sprintf(T_('SmartClassMapper, %s dynamic items, % logs'), count($this->dynamap), count($this->debug_log));
	}
	
	protected function populateMap () {
	    $database = aliroCoreDatabase::getInstance();
	    $database->setQuery('SELECT * FROM #__classmap WHERE side != "admin"');
	    $maps = $database->loadObjectList();
	    if ($maps) foreach ($maps as $map) {
	    	switch ($map->type) {
	    		case 'component':
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
			$this->saveMap($path, $map);
		}
	}
	
	public function timeSoFar () {
		return $this->timer->mark('seconds');
	}

	protected function getClassPath ($classname) {
		aliroDebug::getInstance()->setDebugData (sprintf('About to load %s, current used memory %s', $classname, (is_callable('memory_get_usage') ? memory_get_usage() : T_('not known')).$this->timeSoFar()));
		$base = criticalInfo::getInstance()->class_base.'/';
	    if (isset($this->dynamap[$classname])) return $base.$this->dynamap[$classname].'.php';
		if (isset($this->classmap[$classname])) return $base.'classes/'.$this->classmap[$classname].'.php';
		if (isset($this->extmap[$classname])) return $base.'extclasses/'.$this->extmap[$classname].'.php';
		if (file_exists($base.'classes/'.$classname.'.php')) return $base.'classes/'.$classname.'.php';
	    return '';
	}

	public function requireClass ($classname) {
		$path = $this->getClassPath($classname);
		if ($path AND file_exists($path)) require_once($path);
		else {
			$message = sprintf('Class %s not found, trying with path = %s', $classname, $path);
			trigger_error($message);
		}
	}
	
	public function classExists ($classname) {
		return $this->getClassPath($classname) ? true : false;
	}

	protected function saveMap ($path, $map) {
		$path .= $map->filename;
		$map->classname = trim($map->classname);
		if (false !== strpos($map->classname, '..')) {
			var_dump($map);
			die(T_('Class mapping includes illegal "..".'));
		}
		if (!isset($this->dynamap[$map->classname])) $this->dynamap[$map->classname] = $path;
		else trigger_error (sprintf('Class %s defined in %s but already defined in %s',$map->classname, $path, $this->dynamap[$map->classname]));
	}

}