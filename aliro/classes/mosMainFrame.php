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
 * mosMainFrame is purely for backwards compatibility with Mambo 4.5+ and
 * Joomla! 1.0.x.
 *
 */

class mosMainFrame {
    private static $instance = __CLASS__;
	private $request = null;

    private function __construct () {
        $this->request = aliroRequest::getInstance();
    }

    private function __clone () {
    	// Enforces singleton
    }

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	public function __call ($method, $args) {
		return call_user_func_array(array($this->request, $method), $args);
	}

    function initSession() {
        $session = aliroSessionFactory::getSession();
        return $session;
    }

    function getTemplate () {
    	return $this->request->getTemplate();
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
        $bac_admin = "$base/administrator/components/com_admin/";
        $baco = "$base/administrator/components/$option/";
        $bacc = "$base/administrator/core/$option/";
        $bttc = "$base/templates/".$this->request->getTemplate()."/components/";
        $bco = "$base/components/$option/";
        $bai = "$base/administrator/includes/";
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
            if ($origoption) $path = $base."/administrator/modules/$option.xml";
            else $path = $base.'/administrator/modules/custom.xml';
            return $this->tryFiles ($path);
            case 'bot_xml': return $this->tryFiles ($base."/mambots/$option.xml");
            case 'menu_xml': return $this->tryFiles ($base."/administrator/components/com_menus/$option/$option.xml");
            case 'installer_html': return $this->tryFiles($base."/administrator/components/com_installer/$option/$option.html.php");
            case 'installer_class': return $this->tryFiles($base."/administrator/components/com_installer/$option/$option.class.php");
        }
    }

}