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
 * aliroComponentHandler is a singleton class that keeps details of all the
 * components in the system.  It extends cachedObject, which allows it to have
 * all its data cached easily.  The constructor is therefore only invoked
 * relatively infrequently and the code is optimised to do work in the constructor
 * so as to save time in the regular methods.  A number of the methods (to do with
 * paths, directories, etc) are standard methods used by the installer for the
 * different kinds of things that can be (un)installed.  Other methods provide
 * for retrieving components by ID, by name or as a complete collection.  Buffer
 * handling assists the management of components at run time.
 *
 * aliroComponent is the class that corresponds to the component table entries
 * and thus supports the creation of objects that describe actual components.
 *
 */

class aliroComponentHandler extends aliroCommonExtHandler  {
	protected static $instance = __CLASS__;

	private $components = array();
	private $links = array();
	private $_buffer = '';

	protected $extensiondir = '/components/';

	protected function __construct () {
		// Making private enforces singleton
		$database = aliroCoreDatabase::getInstance();
		$results = $database->doSQLget("SELECT * FROM #__components", 'aliroComponent');
		foreach ($results as $result) {
			$this->components[$result->option] = $result;
			$this->links[$result->id] = $result->option;
		}
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	public function componentCount () {
	    return count($this->components);
	}

	public function getComponentByFormalName ($formalname) {
		return isset($this->components[$formalname]) ? $this->components[$formalname] : null;
	}
	
	public function getComponentByID ($id) {
		return (isset($this->links[$id]) AND isset($this->components[$this->links[$id]])) ? $this->components[$this->links[$id]] : null;
	}

	public function getAllComponents () {
		return $this->components;
	}

	public function getBuffer() {
		return $this->_buffer;
	}

	public function mosMainBody() {
		return $this->getBuffer();
	}

	public function startBuffer () {
		$this->_buffer = '';
		ob_start();
	}

	public function endBuffer () {
		$this->_buffer .= ob_get_contents();
		ob_end_clean();
	}

}

final class aliroApplicationHandler extends aliroComponentHandler {
	// Currently has no additional methods or properties
}

final class aliroComponent extends aliroCommonExtBase {
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__components';
	protected $rowKey = 'id';
	protected $handler = 'aliroComponentHandler';
	protected $formalfield = 'extformalname';

}