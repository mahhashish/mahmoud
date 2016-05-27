<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class aliroDatabaseInterface {
	protected $dbname = '';
	protected $dbserver = '';
	protected $interface_type = ''; // Must be overriden with a value in the subclass
	protected $database_engine = 'mysql';
	
	public function __construct ($dbname) {
		$this->dbname = $dbname;
	}
	
	public function getType () {
		return $this->interface_type;
	}
	
	protected function T_ ($string) {
		return function_exists('T_') ? T_($string) : $string;
	}
}
