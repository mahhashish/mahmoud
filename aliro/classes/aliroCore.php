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
 * aliroCore is the singleton class that holds the basic configuration information
 * for the CMS.  It dynamically determines the live site and absolute path, so as
 * to be able to adapt to any change in the environment.  However, the overhead is
 * reduced by the fact that aliroCore is a cached singleton.  The methods for
 * retrieving the configuration data from the file system are also used by database
 * classes to obtain their credentials.  The decoding is also used by the aliroParameters
 * class.  A variety of retrieval methods is provided, and also a globalization method
 * for use with old components that expect configuration variables to be global.
 *
 */

class aliroCore {

	private static $instance = __CLASS__;
    private $config = array();
    private $subdirlength = 0;

    protected function __construct () {
    	if (aliro::getInstance()->installed) $database = aliroDatabase::getInstance();
    	$this->config = aliroCore::getConfigData('configuration.php');
		error_reporting(E_ALL|E_STRICT);
		$subdirectory = dirname($_SERVER['PHP_SELF']);
		if (criticalInfo::getInstance()->isAdmin) $subdirectory = dirname($subdirectory);
		if ('/' == $subdirectory) $subdirectory = '';
		$this->subdirlength = strlen($subdirectory);
        $this->findLiveSite($subdirectory);
	}

	private function findLiveSite ($subdirectory) {
	    $_SERVER['HTTP_HOST'] = str_replace('joomla.', '', $_SERVER['HTTP_HOST']);
	    $_SERVER['SERVER_NAME'] = str_replace('joomla.', '', $_SERVER['SERVER_NAME']);
		$scheme = isset($_SERVER['HTTP_SCHEME']) ? $_SERVER['HTTP_SCHEME'] : ((isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS'] != 'off')) ? 'https' : 'http');
		if (isset($_SERVER['HTTP_HOST'])) {
			$withport = explode(':', $_SERVER['HTTP_HOST']);
			$servername = $withport[0];
			if (isset($withport[1])) $port = ':'.$withport[1];
		}
		elseif (isset($_SERVER['SERVER_NAME'])) $servername = $_SERVER['SERVER_NAME'];
		else trigger_error(T_('Impossible to determine the name of this server'), E_USER_ERROR);
		if (!isset($port) AND !empty($_SERVER['SERVER_PORT'])) $port = ':'.$_SERVER['SERVER_PORT'];
		if (isset($port)) {
			if (($scheme == 'http' AND $port == ':80') OR ($scheme == 'https' AND $port == ':443')) $port = '';
		}
		else $port = '';
		$afterscheme = '://'.$servername.$port.$subdirectory;
		$this->config['live_site'] = $this->config['secure_site'] = $_SESSION['aliro_live_site'] = $scheme.$afterscheme;
		$this->config['unsecure_site'] = $_SESSION['aliro_unsecure_site'] = 'http'.$afterscheme;
	}

	public function setRedirectHere () {
	    $_SESSION['aliro_redirect_here'] = substr($_SERVER['REQUEST_URI'], $this->subdirlength);
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	public function fixLanguage () {
        $language = aliroLanguage::getInstance();
        if (empty($this->config['lang'])) $this->set('mosConfig_lang', 'english');
        $language_file = _ALIRO_ABSOLUTE_PATH."/language/$this->mosConfig_lang.php";
        if (file_exists($language_file)) require_once ($language_file);
        else require_once (criticalInfo::getInstance()->absolute_path.'/language/english.php');
	}

	public static function getConfigData ($configname) {
		$info = criticalInfo::getInstance();
		$filename = md5($info->absolute_path.'/'.$configname).'.php';
		if (!file_exists($info->class_base.'/configs/'.$filename)) {
            @session_write_close();
			$installer = new aliroInstall();
			$installer->tellUserNotInstalled();
			exit;
        }
		require ($info->class_base.'/configs/'.$filename);
		$results = unserialize ($packed);
		foreach ($results as &$item) $item = base64_decode($item);
		$results['configfilename'] = $filename; // basename($filepath);
		return $results;
	}

	public function decodeParams ($rawtext) {
		$info = unserialize ($rawtext);
		foreach ($info as &$item) $item = base64_decode($item);
		return $info;
	}

    public function getCfg ($property) {
   		if (aliro::getInstance()->installed) $info = criticalInfo::getInstance();
		else return null;
    	if ('admin_site' == $property) return $this->config['live_site'].$info->admin_dir;
    	// For backwards compatibility
    	if ('allowUserRegistration' == $property) {
    		$registration = aliroComponentHandler::getInstance()->getComponentByFormalName('com_registration');
    		return $registration ? 1 : 0;
    	}
    	if ('cachepath' == $property) return $this->getCfg('absolute_path').'/cache';
    	if ('locale' == $property) return isset($this->config['locale']) ? $this->config['locale'] : 'en';
    	if (in_array($property, array ('absolute_path', 'admin_absolute_path', 'admin_dir'))) {
    		if (isset($info->$property)) return $info->$property;
    	}
    	elseif (isset($this->config[$property])) return $this->config[$property];
    	$this->propertyError($property);
        return null;
    }

    public function __get ($property) {
    	aliroCore::get($property);
    }

    private function propertyError ($property) {
        $message = sprintf(T_('Invalid property %s requested from aliroCore'), $property);
        trigger_error($message, E_USER_WARNING);
    }

    public static function get ($property) {
		if (aliro::getInstance()->installed) $config = aliroCore::getInstance();
		else return null;
        if ('mosConfig_' == substr($property, 0, 10)) return $config->getCfg(substr($property,10));
        elseif ('Itemid' == $property) return aliroRequest::getInstance()->getItemid();
        $config->propertyError($property);
        return null;
    }

    public static function is_set ($property) {
        $config = aliroCore::getInstance();
        return ('mosConfig_' == substr($property, 0, 10) AND isset($config->config[substr($property,10)]));
    }

    public static function set ($property, $value) {
        $config = aliroCore::getInstance();
        $config->config[$property] = $value;
        return $value;
    }

    public function globalizeConfig () {
    	foreach ($this->config as $key=>$value) $GLOBALS['mosConfig_'.$key] = $value;
    	$GLOBALS['mosConfig_absolute_path'] = $this->getCfg('absolute_path');
    }

    public function getSubLen () {
    	return $this->subdirlength;
    }

}

class mamboCore extends aliroCore {

}