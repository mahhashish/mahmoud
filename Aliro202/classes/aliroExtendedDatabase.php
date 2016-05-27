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
 * Everything here is to do with database management.
 *
 * The aliroExtendedDatabase class utilises an aliroBasicDatabase object to 
 * implement extended methods beyond the basic.  Simpler SQL operations, and
 * extensive metadata handling methods.
 *
 */

abstract class aliroExtendedDatabase {
	protected $DBInfo = null;
	protected $cache = null;
	protected $database = null;

	protected function __construct( $host, $user, $pass, $db, $table_prefix, $return_on_error=false ) {
		$this->database = new aliroBasicDatabase ($host, $user, $pass, $db, $table_prefix, $return_on_error);
		if ($this->database->getErrorNum()) return;
		$this->cache = new aliroSimpleCache(get_class($this));
		$this->DBInfo = $this->cache->get($host.$db.$user.$table_prefix);
		if (!$this->DBInfo) $this->emptyCache();
	}

	public function __call ($method, $args) {
		return call_user_func_array(array($this->database, $method), $args);
	}
	
	public function loadObject (&$object=null) {
		$result = $this->database->loadObject($object);
		if (true === $result) return $result;
		if (is_object($result)) {
			if ($object instanceof aliroDBGeneralRow) $object->bind($result, '', false, false);
			else foreach (get_object_vars($object) as $k => $v) {
				if ($k[0] != '_' AND isset($result->$k)) $object->$k = $result->$k;
			}
			return true;
		}
		return false;
	}

	public function clearCache () {
		$this->cache->clean();
		$this->emptyCache();
	}

	protected function emptyCache () {
		$this->DBInfo = new stdClass();
		$this->DBInfo->DBTables = array();
		$this->DBInfo->DBFields = array();
		$this->DBInfo->DBFieldsByName = array();
		$this->getTableInfo();
	}

	// Combined operation - takes SQL and executes it
	public function doSQL ($sql) {
		$this->database->setQuery($sql);
		return $this->database->query();
	}

	// Combined operation - as above - and returns an array of objects of the specified class
	public function doSQLget ($sql, $classname='stdClass', $key='', $max=0) {
		$this->database->setQuery($sql);
		if ('stdClass' == $classname) return $this->retrieveResults ($key, $max, 'object');

		$sql_function = $this->database->getFetchFunc().'object';
		$cur = $this->query();
		if ($cur) {
			while ($row = $sql_function($cur)) {
				$next = new $classname();
				if (!isset($objfields)) $objfields = array_keys(get_object_vars($row));
				foreach ($objfields as $field) $next->$field = $row->$field;
				if (method_exists($next, 'onDataLoad')) $next->onDataLoad();
				if ($key) $result[$row->$key] = $next;
				else $result[] = $next;
				if ($max AND count($results) >= $max) break;
			}
			$this->database->freeResultSet($cur);
		}
		return isset($result) ? $result : array();
	}

	protected function retrieveResults ($key='', $max=0, $result_type='row') {
		return $this->database->retrieveResults($key, $max, $result_type);
	}

	protected function getTableInfo () {
		if (count($this->DBInfo->DBTables) == 0) {
			$this->database->setQuery ("SHOW TABLES");
            $results = $this->database->loadResultArray();
			if ($results) foreach ($results as $result) $this->DBInfo->DBTables[] = $this->restoreOnePrefix($result);
			$this->saveCache();
		}
	}

	protected function restoreOnePrefix ($tablename) {
		return $this->database->restoreOnePrefix($tablename);
	}

	protected function saveCache () {
		$this->cache->save($this->DBInfo);
	}

	protected function storeFields ($tablename) {
		if ($this->tableExists($tablename) AND !isset($this->DBInfo->DBFields[$tablename])) {
			$this->DBInfo->DBFields[$tablename] = $this->doSQLget("DESCRIBE `$tablename`", 'stdClass', 'Field');
			$this->DBInfo->DBFieldsByName[$tablename] = array();
			foreach ($this->DBInfo->DBFields[$tablename] as $field) $this->DBInfo->DBFieldsByName[$tablename][$field->Field] = $field;
			$this->saveCache();
		}
	}

	public function getAllFieldInfo ($tablename) {
		$this->storeFields($tablename);
		return isset($this->DBInfo->DBFields[$tablename]) ? $this->DBInfo->DBFields[$tablename] : array();
	}

	public function getAllFieldNames ($tablename) {
		$this->storeFields($tablename);
		return isset($this->DBInfo->DBFieldsByName[$tablename]) ? array_keys($this->DBInfo->DBFieldsByName[$tablename]) : array();
	}
	
	public function getAllTableNames () {
		$this->getTableInfo();
		return $this->DBInfo->DBTables;
	}

	public function getShortFieldNames ($tablename) {
		$fieldinfo = $this->getAllFieldInfo($tablename);
		foreach ($fieldinfo as $info) {
			if (false === strpos($info->Type, 'blob') AND false === strpos($info->Type, 'text')) {
				$short[] = $info->Field;
			}
		}
		return isset($short) ? $short : array();
	}

	public function getIndexNames ($tablename) {
		if ($this->tableExists($tablename)) {
			$indexes = $this->doSQLget("SHOW INDEXES FROM `$tablename`");
			foreach ($indexes as $index) $result[] = $index->Key_name;
		}
		return isset($result) ? $result : array();
	}

	public function getShortRecords ($tablename, $condition) {
		$fields = $this->getShortFieldNames($tablename);
		if (empty($fields)) return null;
		$fieldlist = implode(',', $fields);
		return $this->doSQLget("SELECT $fieldlist FROM $tablename $condition");
	}

	public function addFieldIfMissing ($tablename, $fieldname, $fieldspec, $alterIfPresent=false) {
		if (in_array($fieldname, $this->getAllFieldNames($tablename))) {
			if ($alterIfPresent) return $this->alterField($tablename, $fieldname, $fieldspec);
			return false;
		}
		if ($this->tableExists($tablename)) {
			$this->doSQL("ALTER TABLE `$tablename` ADD `$fieldname` ".$fieldspec);
			$this->clearCache();
		}
		return true;
	}

	public function dropFieldIfPresent ($tablename, $fieldname) {
		if (!in_array($fieldname, $this->getAllFieldNames($tablename))) return false;
		$this->doSQL("ALTER TABLE $tablename DROP COLUMN `$fieldname`");
		$this->clearCache();
		return true;
	}

	public function alterField ($tablename, $fieldname, $fieldspec, $newfieldname='') {
		if (!in_array($fieldname, $this->getAllFieldNames($tablename))) return false;
		if (!$newfieldname) $newfieldname = $fieldname;
		$this->doSQL("ALTER TABLE $tablename CHANGE COLUMN `$fieldname` `$newfieldname` ".$fieldspec);
		$this->clearCache();
		return true;
	}

	public function getFieldInfo ($tablename, $fieldname) {
		return $this->fieldExists($tablename, $fieldname) ? $this->DBInfo->DBFieldsByName[$tablename][$fieldname] : null;
	}

	public function fieldExists ($tablename, $fieldname) {
		$this->storeFields($tablename);
		return isset($this->DBInfo->DBFieldsByName[$tablename][$fieldname]);
	}

	// Expects parameter to be of the form #__name_of_table, so no need to look for DB prefix
	public function tableExists ($tablename) {
		return in_array($tablename, $this->DBInfo->DBTables);
	}

	public function insertObject ($table, $object, $keyName=NULL) {
		$query = $this->buildInsertFields($table, $object);
		$result = $query ? $this->doSQL($query) : false;
		if ($result) {
			// insertid() is only meaningful if non-zero
			$autoinc = $this->insertid();
			if ($autoinc AND $keyName AND !is_array($keyName)) $object->$keyName = $autoinc;
		}
		return $result;
	}

	protected function buildInsertFields ($table, $object, $ignore=false) {
		$dbfields = $this->getAllFieldInfo($table);
		foreach ($dbfields as $field) {
			$name = $field->Field;
			$unsuitable = (!isset($object->$name) OR is_array($object->$name) OR is_object($object->$name)) ? true : false;
			$isverylong = (false !== strpos($field->Type, 'text') OR false !== strpos($field->Type, 'blob')) ? true : false;
			if (!$isverylong AND $unsuitable) continue;
			$fields[] = "`$name`";
			$values[] = $unsuitable ? "''" : $this->setFieldValue($object->$name, $field->Type);
		}
		if (isset($fields)) {
			return $this->makeInsertSQL ($table, implode( ",", $fields ), implode( ",", $values ), $ignore);
		}
		else {
			trigger_error (sprintf($this->T_('Insert into table %s but no fields'), $table));
			$this->trace();
			return false;
		}
	}

	protected function makeInsertSQL ($table, $fields, $values, $ignore=false) {
		$sqlstart = $ignore ? 'INSERT IGNORE INTO' : 'INSERT INTO';
		return "$sqlstart $table ($fields) VALUES ($values)";
	}

	public function updateObject ($table, $object, $keyName, $updateNulls=true) {
		$dbfields = $this->getAllFieldInfo($table);
		foreach ($dbfields as $field) {
			$name = $field->Field;
			if (!isset($object->$name) OR is_array($object->$name) OR is_object($object->$name)) {
				if ($updateNulls) $value = "''";
				else continue;
			}
			else $value = $this->setFieldValue($object->$name, $field->Type);
			$setter = "`$name` = $value";
			if (is_array($keyName) AND in_array($name, $keyName)) $where[] = $setter;
			elseif (!is_array($keyName) AND $name == $keyName) $where[] = $setter;
			else $setters[] = $setter;
		}
		if (!isset($where)) {
			trigger_error (sprintf($this->T_('Update table %s but no key fields'), $table));
			return false;
		}
		if (isset($setters)) return $this->doUpdate ($table, implode (', ', $setters), implode (' AND ' , $where));
		return true;
	}

	// Note that this will not work when aliroExtendedDatabase is used with MiaCMS/Mambo/Joomla
	public function setFieldValue ($data, $type='varchar') {
		return $this->database->setFieldValue($data, $type);
	}

	protected function doUpdate ($table, $setters, $conditions) {
		return $this->doSQL("UPDATE $table SET $setters WHERE $conditions");
	}

	public function insertOrUpdateObject ($table, $object, $keyName, $updateNulls=true) {
		$query = $this->buildInsertFields($table, $object).' ON DUPLICATE KEY UPDATE ';
		$dbfields = $this->getAllFieldInfo($table);
		foreach ($dbfields as $field) {
			$name = $field->Field;
			if (is_array($keyName)) {
				if (in_array($name, $keyName)) $setters[] = "`$name` = `$name`";
			}
			elseif ($name == $keyName) $setters[] = "`$name` = `$name`";
			if (!isset($object->$name) OR is_array($object->$name) OR is_object($object->$name)) {
				if ($updateNulls) $value = "''";
				else continue;
			}
			else $value = $this->setFieldValue($object->$name, $field->Type);
			$setters[] = "`$name` = $value";
		}
		$query .= implode(', ', $setters);
		$this->doSQL($query);
	}

	// If the insert fails, the problem is ignored - use affected rows to find what happened
	public function insertObjectSafely ($table, $object) {
		$this->doSQL($this->buildInsertFields($table, $object, true));
	}

	protected function trace () {
		echo aliroBase::trace();
	}

	protected function T_($string) {
		return function_exists('T_') ? T_($string) : $string;
	}
}
