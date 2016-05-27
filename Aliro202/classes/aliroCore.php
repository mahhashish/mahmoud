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

// Would be a final class if it were not for backwards compatibility
class aliroCore {

	private static $instance = null;
    private $config = array();
    private $subdirlength = 0;

    protected function __construct () {
    	// Maximum error reporting
		error_reporting(E_ALL|E_STRICT);
		// Set time zone to UTC in case we have not yet installed
		if (function_exists('date_default_timezone_set')) date_default_timezone_set('UTC');
    	$this->config = aliroCore::getConfigData('configuration.php');
		if (function_exists('date_default_timezone_set') AND !empty($this->config['timezone'])) {
			date_default_timezone_set($this->config['timezone']);
		}
	}

	public function makeOffline ($exception) {
		// $this->config['offline'] = 1;
		// $freshinstall = new aliroInstall();
		// $freshinstall->storeConfig($this->config, 'configuration.php', true);
		$subject = function_exists('T_') ? T_('Error in context where logging is unavailable at ') : 'Error in context where logging is unavailable at ';
		$subject .= $this->config['sitename'];
		$mailer = new aliroMailer ();
		$mailer->setFrom ($this->config['fromname'] ? "\"{$this->config['fromname']}\" <{$this->config['mailfrom']}>" : "<{$this->config['mailfrom']}>");
	    $mailer->setSubject($subject);
		$mailer->setText($exception->getMessage());
		$recipient = 'martin@black-sheep-research.com';
		$mailer->simpleSend(array($recipient));
		$offline = new aliroOffline();
		$offline->show(T_('Error in context where logging is unavailable at ').$this->config['sitename'], ($this->config['debug'] ? $exception : null));
		exit;
	}

	public function setRedirectHere () {
	    $here = substr($_SERVER['REQUEST_URI'], $this->config['subdirlength']);
            if (aliroSEF::getInstance()->isValidURI($here)) return;
		if (isset($_SESSION['aliro_redirect_here']) AND is_array($_SESSION['aliro_redirect_here'])) {
			array_unshift($_SESSION['aliro_redirect_here'], $here);
		}
		else $_SESSION['aliro_redirect_here'] = array($here);
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}

	public function fixLanguage () {
        $language = aliroLanguage::getInstance();
        if (empty($this->config['lang'])) $this->set('mosConfig_lang', 'english');
        $language_file = _ALIRO_ABSOLUTE_PATH."/language/$this->mosConfig_lang.php";
        if (file_exists($language_file)) require_once ($language_file);
        else require_once (_ALIRO_ABSOLUTE_PATH.'/language/english.php');
	}

	public static function getConfigData ($configname) {
		$filename = md5(_ALIRO_ABSOLUTE_PATH.'/'.$configname).'.php';
		clearstatcache();
		if (!file_exists(_ALIRO_SITE_BASE.'/configs/'.$filename)) {
            @session_write_close();
			$installer = aliroInstallerFactory::getInstaller();
			$installer->tellUserNotInstalled();
			exit;
        }
		require (_ALIRO_SITE_BASE.'/configs/'.$filename);
		$results = unserialize ($packed);
		if (is_array($results)) foreach ($results as &$item) $item = base64_decode($item);
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
    	if ('admin_site' == $property) return isset($this->config['live_site']) ? $this->config['live_site'].$info->admin_dir : $info->admin_dir;
    	// For backwards compatibility
    	if ('allowUserRegistration' == $property) {
    		$registration = aliroComponentHandler::getInstance()->getComponentByFormalName('com_registration');
    		return $registration ? 1 : 0;
    	}
    	if ('cachepath' == $property) return _ALIRO_SITE_BASE.'/cache';
    	if ('locale' == $property) return isset($this->config['locale']) ? $this->config['locale'] : 'en';
    	if (in_array($property, array ('absolute_path', 'admin_absolute_path', 'admin_dir'))) {
    		if (isset($info->$property)) return $info->$property;
    	}
    	elseif (isset($this->config[$property])) return $this->config[$property];
		$installer = new aliroInstall();
		$value = $installer->getDefaultProperty($property, $isfound);
		if ($isfound) {
			$this->config[$property] = $value;
			return $value;
		}
    	$this->propertyError($property);
        return null;
    }

    public function __get ($property) {
    	aliroCore::get($property);
    }

    private function propertyError ($property) {
        $message = function_exists('T_') ? sprintf(T_('Invalid property %s requested from aliroCore'), $property) : sprintf('Invalid property %s requested from aliroCore', $property);
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
    	return $this->config['subdirlength'];
    }

}

// Provided for backwards compatibility with Mambo 4.6+
class mamboCore extends aliroCore {

}