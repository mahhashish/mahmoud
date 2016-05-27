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
 * Aliro wrapper for PHP Grid
 *
 */

require_once (_ALIRO_CLASS_BASE.'/extclasses/phpgrid/conf.php');

final class aliroDataGrid {
	private $datagrid = null;
	
	public function __construct  ($sql, $keyname='id', $tablename='', $dbname='aliroDatabase') {
		$database = call_user_func(array($dbname, 'getInstance'));
		$credentials = $database->getCredentials();
		if ('mysqli' == $credentials['dbtype']) $credentials['dbtype'] = 'mysql';
		$this->datagrid = new C_DataGrid($sql, $keyname='id', $tablename='', $credentials);
		$this->datagrid->setCallbackString(_ALIRO_CURRENT_PATH);
	}

	public function __call ($method, $args) {
		return call_user_func_array (array($this->datagrid,$method), $args);
	}
}
