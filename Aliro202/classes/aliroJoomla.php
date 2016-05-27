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
 * aliroJoomla provides a limited emulation of the Joomla 1.5+ environment
 *
 */

define ('JPATH_ADMINISTRATOR', _ALIRO_ADMIN_PATH);
define ('JPATH_ROOT', _ALIRO_ABSOLUTE_PATH);
define( 'JREQUEST_NOTRIM', _MOS_NOTRIM );
define( 'JREQUEST_ALLOWRAW' , _MOS_ALLOWRAW );
define( 'JREQUEST_ALLOWHTML', _MOS_ALLOWHTML );

class aliroJoomla extends aliroFriendlyBase {
	public function getValue ($property) {
		$parts = explode('.', $property);
		$p = count($parts) > 1 ? $parts[1] : $parts[0];
		return $this->getCfg($p);
	}
}

$mainframe = mosMainFrame::getInstance();
$path_side = _ALIRO_IS_ADMIN ? 'admin' : 'front';
define ('JPATH_COMPONENT', dirname($mainframe->getPath($path_side)));

define ('_J_ALLOWHTML', _MOS_ALLOWHTML);


function jimport () {}

class JApplicationHelper {
	public static function getPath ($name) {
		$mainframe = mosMainFrame::getInstance();
		return $mainframe->getPath($name);
	}
}

class JRoute {
	public static function _ ($uri) {
		return aliroSEF::getInstance()->sefRelToAbs($uri);
	}
}

class JFactory {
	public static function getDBO () {
		return joomlaDatabase::getInstance();
	}
	public static function getconfig () {
		return new aliroJoomla();
	}
	public static function getuser () {
		return aliroUser::getInstance();
	}
	public static function getsession () {
		return aliroSession::getSession();
	}
	public static function getlanguage () {
		return aliroLanguage::getInstance();
	}
}

class JDatabase extends aliroExtendedDatabase {

	public function __construct ($options) {
		parent::__construct($options['host'], $options['user'], $options['password'], $options['database'], $options['prefix']);
	}
}

class JRequest {
	public static function getvar ($name, $default=null, $arr='default', $type='none', $flags=0) {
		$mapname = array ('POST' => '_POST', 'GET' => '_GET', 'REQUEST' => '_REQUEST');
		$arr = strtoupper($arr);
		if (!isset($mapname[$arr])) {
			trigger_error(T_('Call on JRequest with invalid array code'));
			return null;
		}
		eval('$source =& '."$$mapname[$arr];");
		$result = aliroRequest::getInstance()->getParam($source, $name, $default, $flags);
		if ('params' == $name) {
			$p = new aliroParameters($result);
			return $p->asPost();
		}
		else return $result;
	}
	public static function getstring ($name, $default=null, $arr='default', $type='none', $flags=0) {
		return JRequest::getvar($name, $default, $arr, $type, $flags);
	}
}

class JURI {
	public static function base () {
		return aliroCore::getInstance()->getCfg('live_site');
	}
}

class JError {
	public static function raiseWarning ($number, $text) {
		aliroRequest::getInstance()->setErrorMessage($text, _ALIRO_ERROR_WARN);
	}
	public static function raiseNotice ($number, $text) {
		aliroRequest::getInstance()->setErrorMessage($text, _ALIRO_ERROR_INFORM);
	}
	public static function raiseError ($number, $text) {
		aliroRequest::getInstance()->setErrorMessage($text, _ALIRO_ERROR_SEVERE);
	}
	public static function raise ($level, $number, $text) {
		$setlevel = array (
		E_ERROR => _ALIRO_ERROR_SEVERE,
		E_WARN => _ALIRO_ERROR_WARN,
		E_NOTICE => _ALIRO_ERROR_INFORM
		);
		$errortype = isset($setlevel[$level]) ? $setlevel[$level] : _ALIRO_ERROR_WARN;
		aliroRequest::getInstance()->setErrorMessage($text, $errortype);
	}
	public static function isError ($object) {
		return ($object instanceof JException OR $object instanceof JError OR $object instanceof Exception) ? true : false;
	}
}

class JALanguage {
	private static $instance = __CLASS__;
	public $translations = array();
	private function __construct () {
		$cname = _ALIRO_COMPONENT_NAME;
		$lang = _JOOMLA_LANGUAGE;
		$fp = @fopen(_ALIRO_CURRENT_PATH."/components/$cname/$lang.$cname.ini", 'rb');
		if ($fp) while (!feof($fp)) {
			$line = fgets($fp);
			if ($line AND '#' == $line[0]) continue;
			$assigns = explode('=',$line);
			if (2 == count($assigns)) $this->translations[trim($assigns[0])] = trim($assigns[1]);
		}			
	}
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}
}

class JText {
	public static function _($string) {
		$language = JALanguage::getInstance();
		return isset($language->translations[$string]) ? $language->translations[$string] : $string;
	}
}

class JToolBar {
	public function __construct ($name) {
	}
	public function appendButton ($mode, $image, $text, $task, $extended, $listprompt) {
		aliroAdminToolbar::getInstance()->addToToolBar ($task, $text, $image, $image, $extended, $listprompt);
	}
	public function render () {}
}

class JParameter extends aliroParameters {}

/*
class JDatabase extends aliroBasicDatabase {
	protected static $instance = null;
	
	public function __construct ($options) {
		parent::__construct ($options['host'], $options['user'], $options['password'], $options['database'], $options['prefix'], true);
	}
	public static function getInstance () {
		$options = func_get_arg(0);
		if (!is_object(self::$instance)) self::$instance = new self($options);
		if (self::$instance->getErrorNum()) {
			$result = new JException(T_('Database credentials invalid in JDatabasae'));
			return $result;
		}
		return self::$instance;
	}	
	public function loadObject (&$object=null) {
		$object = null;
		parent::loadObject($object);
		return $object;
	}
}
*/

class JException {
	public function __construct ($message) {
	}
}

class JTable {
	public static function addIncludePath () {
		trigger_error(T_('Please recode DB object classes to inherit from aliroDatabaseRow, and make accessible using XML.'));
	}
}