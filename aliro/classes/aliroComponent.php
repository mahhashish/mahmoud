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
		$results = $database->doSQLget("SELECT c.*, e.xmlfile FROM #__components AS c INNER JOIN #__extensions AS e ON c.option = e.formalname", 'aliroComponent');
		foreach ($results as $result) {
			$this->components[$result->option] = $result;
			$this->links[$result->id] = $result->option;
		}
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	// Mainly for the installer - overrides default method
	public function getXMLRelativePath ($formalname, $admin) {
		return $this->getRelativePath ($formalname, 2);
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

	public function mosMainBody() {
		return $this->_buffer;
	}

	public function startBuffer () {
		$this->_buffer = '';
		ob_start();
	}

	public function endBuffer () {
		$this->_buffer = ob_get_contents();
		ob_end_clean();
	}

}

class aliroComponent extends aliroCommonExtBase {
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__components';
	protected $rowKey = 'id';
	protected $handler = 'aliroComponentHandler';
	protected $formalfield = 'extformalname';

}