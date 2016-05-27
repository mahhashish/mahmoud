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
 * aliroTemplateHandler keeps track of all the templates known to the system and
 * provides useful methods for other parts of Aliro and for extensions
 *
 */

class aliroTemplateHandler extends aliroCommonExtHandler {
    protected static $instance = __CLASS__;
	private $defaultTemplate = null;
	private $defaultAdminTemplate = null;
	private $adminTemplateClasses = array();
	private $userTemplateClasses = array();
	private $innerUserTemplateClasses = array();
	private $innerAdminTemplateClasses = array();
	private $allTemplateClasses = array();

	protected $extensiondir = '/templates/';

    protected function __construct () {
    	foreach (aliroExtensionHandler::getInstance()->getTemplateExtensions() as $extension) {
			if ($extension->inner) {
				if (2 & $extension->admin) $this->innerAdminTemplateClasses[$extension->formalname] = $extension->adminclass;
				else $this->innerUserTemplateClasses[$extension->formalname] = $extension->class;
			}
	    	elseif (2 & $extension->admin) {
   				$this->adminTemplateClasses[$extension->formalname] = $extension->adminclass;
   				if ($extension->default_template) $this->defaultAdminTemplate = $extension;
   			}
    		else {
   				$this->userTemplateClasses[$extension->formalname] = $extension->class;
    			if ($extension->default_template) $this->defaultTemplate = $extension;
   			}
			$this->allTemplateClasses[$extension->formalname] = (2 & $extension->admin) ? $extension->adminclass : $extension->class;
    	}
    }

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	public function getDefaultTemplateName () {
		return $this->getDefaultTemplateProperty('formalname');
	}
	
	public function getDefaultTemplateClass () {
		if (_ALIRO_IS_ADMIN) return $this->getDefaultAdminTemplateClass();
		else return $this->getDefaultUserTemplateClass();
	}

	public function getDefaultUserTemplateClass () {
		if (isset($this->defaultTemplate) AND isset($this->defaultTemplate->class) AND class_exists($this->defaultTemplate->class)) return $this->defaultTemplate->class;
		else return 'defaultTemplate';
	}
	
	public function getDefaultUserCSS () {
		if (isset($this->defaultTemplate)) {
			_ALIRO_ABSOLUTE_PATH.'/templates/'.$this->defaultTemplate->formalname.'/css/template_css.css';
		}
		else return _ALIRO_ABSOLUTE_PATH.'/templates/default.css';
	}
	
	public function getDefaultAdminTemplateClass () {
		if (isset($this->defaultAdminTemplate) AND isset($this->defaultAdminTemplate->adminclass) AND class_exists($this->defaultAdminTemplate->adminclass)) return $this->defaultAdminTemplate->adminclass;
		else return 'defaultAdminTemplate';
	}
	
	public function getInnerTemplates ($isadmin=false) {
		return $isadmin ? array_keys($this->innerAdminTemplateClasses) : array_keys($this->innerUserTemplateClasses);
	}
	
	public function getTemplateObjectByFormalName ($name) {
		$tclass = isset($this->allTemplateClasses[$name]) ? $this->allTemplateClasses[$name] : '';
		return $tclass ? new $tclass() : null;
	}
	
    public function removeTemplate ($formalname, $admin) {
		if (2 == $admin) $dirpath = _ALIRO_ASBOLUTE_PATH.'/templates/'.$formalname;
		else $dirpath = _ALIRO_ABSOLUTE_PATH.'/templates/'.$formalname;
		$dir = new aliroDirectory ($dirpath);
		$dir->deleteAll();
		$this->clearCache();
    }

   	public function getDefaultTemplateProperty ($property, $isAdmin=null) {
		if (is_null($isAdmin)) $isAdmin = _ALIRO_IS_ADMIN;
		$template = $isAdmin ? 'defaultAdminTemplate' : 'defaultTemplate';
		return (isset($this->$template) AND isset($this->$template->$property)) ? $this->$template->$property : '';
	}

	public function getAllUserPositions () {
		return $this->getTemplatePositions(array_merge($this->userTemplateClasses,$this->innerUserTemplateClasses), 'defaultTemplate');
	}

	public function getAllAdminPositions () {
		return $this->getTemplatePositions(array_merge($this->adminTemplateClasses,$this->innerAdminTemplateClasses), 'defaultAdminTemplate');
	}

	private function getTemplatePositions ($tclasses, $tdefault) {
		$xhandler = aliroExtensionHandler::getInstance();
		$raw = $result = array();
		$tobject = new $tdefault();
		foreach (array_keys($tobject->positions()) as $position) $raw[$position][] = 'default';
		foreach ($tclasses as $formalname=>$tclass) {
			$tobject = new $tclass;
			foreach (array_keys($tobject->positions()) as $position) $raw[$position][] = $formalname;
		}
		foreach ($raw as $position=>$names) $result[$position] = implode(', ', $names);
		return $result;
	}

}