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
    	$info = criticalInfo::getInstance();
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
		$info = criticalInfo::getInstance();
		if ($info->isAdmin) return $this->getDefaultAdminTemplateClass();
		else return $this->getDefaultUserTemplateClass();
	}

	public function getDefaultUserTemplateClass () {
		if (isset($this->defaultTemplate) AND isset($this->defaultTemplate->class)) return $this->defaultTemplate->class;
		else return 'defaultTemplate';
	}
	
	public function getDefaultUserCSS () {
		if (isset($this->defaultTemplate)) {
			criticalInfo::getInstance()->absolute_path.'/templates/'.$this->defaultTemplate->formalname.'/css/template_css.css';
		}
		else return criticalInfo::getInstance()->absolute_path.'/templates/default.css';
	}
	
	public function getDefaultAdminTemplateClass () {
		if (isset($this->defaultAdminTemplate) AND isset($this->defaultAdminTemplate->adminclass)) return $this->defaultAdminTemplate->adminclass;
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
		$info = criticalInfo::getInstance();
		if (2 == $admin) $dirpath = $info->admin_absolute_path.'/templates/'.$formalname;
		else $dirpath = $info->absolute_path.'/templates/'.$formalname;
		$dir = new aliroDirectory ($dirpath);
		$dir->deleteAll();
		$this->clearCache();
    }

   	public function getDefaultTemplateProperty ($property, $isAdmin=null) {
		if (is_null($isAdmin)) $isAdmin = $info = criticalInfo::getInstance()->isAdmin;
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