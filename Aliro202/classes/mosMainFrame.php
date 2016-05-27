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
 * mosMainFrame is purely for backwards compatibility with Mambo 4.5+ and
 * Joomla! 1.0.x.
 *
 */

class mosMainFrame {
    private static $instance = null;
	private $request = null;
	private $option = '';
	private $config = null;

    private function __construct () {
        $this->request = aliroRequest::getInstance();
    }

    private function __clone () {
    	// Enforces singleton
    }

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}

	public function __call ($method, $args) {
		return call_user_func_array(array($this->request, $method), $args);
	}

    public function initSession() {
        $session = aliroSession::getSession();
        return $session;
    }

    public function getTemplate () {
    	return $this->request->getTemplate();
    }
    
    public function getCfg($property) {
    	if (empty($this->config)) $this->config = aliroComponentConfiguration::getInstance('com_content');
    	return isset($this->config->$property) ? $this->config->$property : $this->request->getCfg($property);
    }

    /**
	* Checks to see if an image exists in the current templates image directory
 	* if it does it loads this image.  Otherwise the default image is loaded.
	* Also can be used in conjunction with the menulist param to create the chosen image
	* load the default or use no image
	*/
    function ImageCheck( $file, $directory='/images/M_images/', $param=NULL, $param_directory='/images/M_images/', $alt=NULL, $name='image', $type=1, $align='middle' ) {
        $basepath = aliroCore::get('mosConfig_live_site');
        if ($param) $image = $basepath.$param_directory.$param;
        else {
            $endpath = '/templates/'.$this->request->getTemplate().'/images/'.$file;
            if (file_exists(aliroCore::get('mosConfig_absolute_path').$endpath)) $image = $basepath.$endpath;
            else $image = $basepath.$directory.$file;  // outputs only path to image
        }
        // outputs actual html <img> tag
        //if ($type) $image = '<img src="'. $image .'" alt="'. $alt .'" align="'. $align .'" name="'. $name .'" border="0" />';
        if ($type) $image = '<img src="'. $image .'" alt="'. $alt .'" />';
        return $image;
    }

    /**
	* Returns the first to be found of one or more files, or null
	*
	*/
    function tryFiles ($first_choice, $second_choice=null, $third_choice=null) {
        if (file_exists($first_choice)) return $first_choice;
        elseif ($second_choice AND file_exists($second_choice)) return $second_choice;
        elseif ($third_choice AND file_exists($third_choice)) return $third_choice;
        else return null;
    }

    /**
	* Returns a standard path variable
	*
	*/
    function getPath( $varname, $option='' ) {
        $base = $this->request->absolute_path;
        $origoption = $option;
        if (!$option) $option = $this->request->getParam($_REQUEST, 'option');
        $name = substr($option,4);
		$admindir = defined('_ALIRO_ADMIN_DIR') ? _ALIRO_ADMIN_DIR : '/administrator';
        $bac_admin = $base.$admindir.'/components/com_admin/';
        $baco = $base.$admindir."/components/$option/";
        $bacc = $base.$admindir."/core/$option/";
        $bttc = "$base/templates/".$this->request->getTemplate().'/components/';
        $bco = "$base/components/$option/";
        $bai = $base.$admindir.'/includes/';
        $bi = "$base/includes/";
        switch ($varname) {
            case 'front': return $this->tryFiles ($bco."$name.php");
            case 'front_html': return $this->tryFiles ($bttc."$name.html.php", $bco."$name.html.php");
            case 'admin': return $this->tryFiles ($baco."admin.$name.php", $bac_admin.'admin.admin.php');
            case 'core': return $this->tryFiles ($bacc."admin.$name.php");
            case 'admin_html': return $this->tryFiles ($baco."admin.$name.html.php", $bac_admin.'admin.admin.html.php');
            case 'toolbar': return $this->tryFiles ($baco."toolbar.$name.php", $bacc."toolbar.$name.php");
            case 'toolbar_html': return $this->tryFiles ($baco."toolbar.$name.html.php", $bacc."toolbar.$name.html.php");
            case 'toolbar_default': return $this->tryFiles ($bai.'toolbar.html.php');
            case 'class': return $this->tryFiles ($bco."$name.class.php", $baco."$name.class.php", $bi."$name.php");
            case 'com_xml': return $this->tryFiles ($baco."$name.xml", $bco."$name.xml");
            case 'mod0_xml':
            if ($origoption) $path = $base."/modules/$option.xml";
            else $path = $base.'/modules/custom.xml';
            return $this->tryFiles ($path);
            case 'mod1_xml':
            if ($origoption) $path = $base.$admindir."/modules/$option.xml";
            else $path = $base.$admindir.'/modules/custom.xml';
            return $this->tryFiles ($path);
            case 'bot_xml': return $this->tryFiles ($base."/mambots/$option.xml");
            case 'menu_xml': return $this->tryFiles ($base.$admindir."/components/com_menus/$option/$option.xml");
            case 'installer_html': return $this->tryFiles($base.$admindir."/components/com_installer/$option/$option.html.php");
            case 'installer_class': return $this->tryFiles($base.$admindir."/components/com_installer/$option/$option.class.php");
        }
    }

}
