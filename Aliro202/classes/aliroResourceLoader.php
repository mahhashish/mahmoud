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
 * Author: Chad Auld
 * cauld@aliro.org
 *
 * aliroResourceLoader is an implementation of the YUI PHP Loader for Aliro.  It
 * is used to load YUI components for use in Aliro and also serves as a generic
 * CSS and JavaScript resource loader.  More information on the YUI PHP Loader can 
 * be found here - http://developer.yahoo.com/yui/phploader/.
 */

class aliroResourceLoader extends YAHOO_util_Loader {
    
    private $yuiVersion       = "3.0.0";
    private static $instance  = null;
    protected $debug          = false;
    protected $tmpDir         = null;
    protected $minifyBasePath = null;
    protected $modulesToLoad  = array();
    protected $customModules  = array();
    
    protected function __construct () {
    	//We'll call the parents constructor in the loadOptimized method once we have all the required modules
    	
    	//We'll notify minify of debug status and behave accordingly (i.e.) clear cache as needed to test
    	$this->debug = aliroCore::getInstance()->getCfg('debug');

    	//Setup out minify lib base path 
        $this->minifyBasePath = aliroCore::getInstance()->getCfg('live_site') . "/extclasses/minify/min/index.php?";
		$docroot = '/' == substr(@$_SERVER['DOCUMENT_ROOT'],-1) ? substr(@$_SERVER['DOCUMENT_ROOT'],0,-1) : @$_SERVER['DOCUMENT_ROOT'];
		$minifyBase = str_replace($docroot, "", _ALIRO_ABSOLUTE_PATH);
        if ($minifyBase AND $minifyBase[0] == "/") {
            $minifyBase = substr($minifyBase, 1);
            $this->minifyBasePath .= "b=" . $minifyBase;
        }
        
        $this->minifyBasePath .= "&f=";
	}
	
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}
	
	public function addCustomModules ($customModules) {
	    try {
			if (is_array($customModules)) {
				$this->customModules = array_merge_recursive($this->customModules, $customModules);
			}
		}
		catch (Exception $exception) {
			trigger_error('Unable to add custom modules! They must be provided as an array.');
			exit;
		}
	}
	
	public function addYUIModule ($module) {
	    if (is_array($module)) {
			$this->modulesToLoad = array_merge_recursive($this->modulesToLoad, $module);
		} else {
		    if (!in_array($module, $this->modulesToLoad)) {
		       $this->modulesToLoad[] = $module;
		    }
		}
	}
	
	protected function buildComboUrl ($dependencyData, $type) {
	    $comboUrl = '';
	    
	    if (!empty($dependencyData)) {
	        $comboUrl = $this->minifyBasePath;
	        
	        $processedDeps = array(); //watch out for dups
            foreach($dependencyData[$type] as $depData) {
                foreach($depData as $key=>$value) {        
                    if (!in_array($key, $processedDeps)) {
                        $processedDeps[] = $key;
                        $comboUrl .= $key . ',';
                    }
                }
            }
            
            $comboUrl = substr($comboUrl, 0, strlen($comboUrl) - 1); //Trim the trailing comma
            if ($this->debug) {
                 $comboUrl .= "&debug=1";
             }
        }
        
        return $comboUrl;
	}
	
	protected function loadAllModules () {
	    //Collect all requested modules and load them
        foreach ($this->modulesToLoad as $module=>$moduleName) {
            $this->loadSingle($moduleName);
        }
        
        //Collect all requested custom modules and load them
		foreach($this->customModules as $customModule) {
		    $this->loadSingle($customModule["name"]);
		}
	}
	
	public function loadOptimized() {
	    //Blow away the minify cache for debug requests
	    if ($this->debug) {
	        $tmpPath = _ALIRO_ABSOLUTE_PATH . '/tmp/';
	        $tmpDir = new aliroDirectory($tmpPath);
	        $minifyCacheFiles = $tmpDir->listFiles($pattern='minify_');
	        foreach($minifyCacheFiles as $file) {
	            aliroFileManager::getInstance()->deleteFile($tmpPath . $file);
	        }
	    }
	    
	    if (!empty($this->modulesToLoad) || !empty($this->customModules)) {
            parent::__construct($this->yuiVersion, null, $this->customModules);
            $this->base = "/extclasses/yui/lib/".$this->yuiVersion."/build/";
	        $this->loadAllModules();
            
            //Build the combo urls
            $cssPath = $this->buildComboUrl($this->css_data(), 'css');
    	    $jsPath  = $this->buildComboUrl($this->script_data(), 'js');
            
            //Use the appropriate Aliro methods to add our final set of resources
            if ($cssPath != '') {
               aliroRequest::getInstance()->addCSS($cssPath, 'screen', true);
            }

            if ($jsPath != '') {
               aliroRequest::getInstance()->addScript($jsPath, 'early', true);
            }
	    }
	}
    
}