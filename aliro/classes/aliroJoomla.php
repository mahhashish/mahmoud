<?php

/**
 * Aliro - Joomla 1.5 compatibility
 * Copyright Martin Brampton 2008
 */

define ('JPATH_ADMINISTRATOR', _ALIRO_ADMIN_PATH);
define ('_JEXEC', 1);
define( 'JREQUEST_NOTRIM', _MOS_NOTRIM );
define( 'JREQUEST_ALLOWRAW' , _MOS_ALLOWRAW );
define( 'JREQUEST_ALLOWHTML', _MOS_ALLOWHTML );

define ('_J_ALLOWHTML', _MOS_ALLOWHTML);

class aliroJoomla extends aliroFriendlyBase {
	public function getValue ($property) {
		$parts = explode('.', $property);
		$p = count($parts) > 1 ? $parts[1] : $parts[0];
		return $this->getCfg($p);
	}
}

function jimport () {}

class JApplicationHelper {
	public static function getPath ($name) {
		$mainframe = mosMainFrame::getInstance();
		return $mainframe->getPath($name);
	}
}

class JFactory {
	public static function getDBO () {
		return joomlaDatabase::getInstance();
	}
	public static function getconfig () {
		return new aliroJoomla();
	}
}

class JRequest {
	public static function getvar ($name, $default=null, $arr='default', $type='none', $flags=0) {
		$mapname = array ('POST' => '_POST', 'GET' => '_GET', 'REQUEST' => '_REQUEST');
		$arr = strtoupper($arr);
		eval('$source =& '."$$mapname[$arr];");
		$result = aliroRequest::getInstance()->getParam($source, $name, $default, $flags);
		if ('params' == $name) {
			$p = new aliroParameters($result);
			return $p->asPost();
		}
		else return $result;
	}
}

class JError {
	public static function raiseWarning ($number, $text) {
		aliroRequest::getInstance()->setErrorMessage($text, _ALIRO_ERROR_WARN);
	}
	public static function raiseNotice ($number, $text) {
		aliroRequest::getInstance()->setErrorMessage($text, _ALIRO_ERROR_INFORM);
	}
	public static function isError ($object) {
		return is_a($object, 'JException') OR is_a($object, 'JError') OR is_a($object, 'Exception');
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
	
class JDatabase extends aliroDatabase {
	protected static $instance = 'JDatabase';
	public function __construct ($options) {
		$this->database = new aliroDatabaseHandler ($options['host'], $options['user'], $options['password'], $options['database'], $options['prefix'], true);
	}
	public static function getInstance () {
		$options = func_get_arg(0);
		if (!is_object(self::$instance)) self::$instance = new self::$instance($options);
		if (self::$instance->getErrorNum()) {
			$result = new JException(T_('Database credentials invalid in JDatabasae'));
			return $result;
		}
		return self::$instance;
	}	
}

class JException {
	public function __construct ($message) {
	}
}