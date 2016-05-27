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
 * aliroPlugin is the abstract base class that aids the creation of plugins.
 */

abstract class aliroPlugin {
	protected $params = null;
	protected $published = false;
	
	public function __construct ($handler=null, $config=array()) {
		if (!empty($config)) {
			$this->params = $config['params'];
			$this->published = $config['published'];
		}
	}
}

abstract class JPlugin extends aliroPlugin {
	protected $_name = '';
	protected $_type = 'unknown';
	
	public function __construct ($subject, $config=array()) {
		if (!empty($config)) $this->_name = $config['name'];
		parent::__construct($subject, $config);
	}
}