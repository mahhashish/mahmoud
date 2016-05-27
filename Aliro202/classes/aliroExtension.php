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
 * aliroExtension is the data class for an extension, corresponding to a row
 * in the extensions table.
 *
 */

final class aliroExtension extends aliroDatabaseRow  {
	private static $legalTypes = array('application', 'component', 'module', 'mambot', 'plugin', 'template', 'language', 'patch');
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__extensions';
	protected $rowKey = 'id';
	private $purifier = null;

	public function __construct ($package='') {
		$this->package = $package;
	}
	
	protected function doPurify ($string) {
		if (null == $this->purifier) $this->purifier = new aliroPurifier();
		return $this->purifier->purify($string);
	}

	public function populateFromXML ($xmlobject, $application=null, $type=null) {
		$this->name = $this->doPurify((string) $xmlobject->getXML('name'));
		$this->type = $type ? $type : $xmlobject->baseAttribute('type');
		if (!in_array($this->type, self::$legalTypes)) return T_('has no valid type');
		if ('plugin' == $this->type) $extension->type = 'mambot';
		$this->formalname = $this->doPurify((string) $xmlobject->getXML('formalname'));
		$this->application = $application ? $application->formalname : $this->formalname;
		if (!$this->formalname AND 'component' == strtolower($this->type)) $this->formalname = 'com_'.str_replace(' ', '', strtolower($this->name));
		if (!$this->formalname) return T_('has no formal name');
		$this->admin = ('administrator' == $xmlobject->baseAttribute('client')) ? 2 : 1;
		$this->inner = ('yes' == $xmlobject->baseAttribute('inner')) ? 1 : 0;
		if ('template' == $this->type) {
			$currentDefault = aliroTemplateHandler::getInstance()->getDefaultTemplateProperty('formalname', (2 == $this->admin));
			if (!$currentDefault OR $currentDefault == $this->formalname) $this->default_template = '1';
		}
		foreach (array('author', 'version', 'authoremail', 'authorurl') as $field) {
			$this->$field = $application ? $application->$field : $this->doPurify((string) $xmlobject->getXML($field));
		}
		$this->date = $application ? $application->date : $this->doPurify((string) $xmlobject->getXML('creationdate'));
		$this->description = $this->doPurify((string) $xmlobject->getXML('description'));
		unset($this->creationdate);
		foreach (array('adminclass', 'menuclass', 'exportclass', 'triggers') as $field) {
			$this->$field = $xmlobject->baseAttribute($field);
		}
		$this->class = $xmlobject->baseAttribute('userclass');
		$this->published = ('yes' == $xmlobject->baseAttribute('published')) ? 1 : 0;
		$this->timestamp = date('Y-m-d');
		return false;
	}
}
