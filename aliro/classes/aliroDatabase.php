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
 * Everything here is to do with database management.
 *
 * aliroDataCache is not yet used - it exists ready for development into a cache
 * for database queries.  This has low priority or may even be abandonded, since
 * it is usually more effective to cache complete output, or more structured
 * data derived from the database, as happens in cached singletons.
 *
 * databaseException uses PHP5 exception handling for database errors, rather
 * than expecting other applications to handle them.  This is combined with the
 * introduction of an error logging table, since detailed diagnostic information
 * is useful to developers, but not much use to users.  Only basic messages are
 * shown to users.
 *
 * aliroAbstractDatabase contains all the basic functions to make it easier to
 * code database functions.  It uses the PHP5 mysqli interface.
 *
 * database is a class provided for backwards compatibility with Mambo 4.x and
 * Joomla! 1.0.x.  aliroDatabaseHandler is simply the preferred name for a class
 * with the same functions as "database".
 *
 * aliroDatabase is a singleton extension of the abstract database class.  It is
 * created from the stored credentials for the general database driving Aliro.
 *
 * aliroCoreDatabase is another singleton extension of the abstract database class.
 * It is the optionally separate database holding critical tables relating only to
 * the core of Aliro, such as information about menus, components, etc.  It is also
 * the only place where user passwords are stored, thus reducing the impact of
 * SQL injection attacks that penetrate only the general database.  If it is not
 * possible to have two databases, Aliro will run with both being the same.
 *
 * Other names are purely for compatibility and are deprecated.
 *
 */

class aliroDataCache {
	public $records = array();
}

class databaseException extends Exception {
	public $dbname = '';
	public $sql = '';
	public $number = 0;

	public function __construct ($dbname, $message, $sql, $number, $dbtrace) {
		parent::__construct($message, $number);
		$this->dbname = $dbname;
		$this->sql = $sql;
		$this->dbtrace = $dbtrace;
	}

}

class databaseInterfaceFactory {

	public static function getInterface () {
		if (function_exists( 'mysqli_connect' )) return new mysqliInterface;
		if (function_exists( 'mysql_connect' )) return new mysqlInterface;
		return  null;
	}

}

abstract class aliroAbstractDatabase {
	protected static $stats = array();
	protected $_sql='';
	protected $_cached=false;
	protected $_errorNum=0;
	protected $_errorMsg='';
	protected $_table_prefix='';
	protected $_resource='';
	protected $_cursor=null;
	protected $_log=array();
	protected $DBname = '';
	protected $DBInfo = null;
	protected $cache = null;
	protected $interface = null;

	public function __construct( $host='localhost', $user, $pass, $db, $table_prefix, $return_on_error=false ) {
		// perform a number of fatality checks, then die gracefully if necessary
		$this->DBname = $db;
		if (!$this->interface = databaseInterfaceFactory::getInterface()) {
			if ($return_on_error) {
				$this->_errorNum = _ALIRO_DB_NO_INTERFACE;
				return;
			}
			$this->forceOffline(_ALIRO_DB_NO_INTERFACE);
		}
		if (!($this->_resource = $this->interface->connect($host, $user, $pass, $db))) {
			$this->_errorMsg = $this->interface->connectError();
			if ($return_on_error) {
				$this->_errorNum = _ALIRO_DB_CONNECT_FAILED;
				return;
			}
			$this->forceOffline(_ALIRO_DB_CONNECT_FAILED);
		}
		$this->interface->setCharset('utf8');
		$this->cache = new aliroSimpleCache('aliroAbstractDatabase', true);
		$this->DBInfo = $this->cache->get($host.$db.$user.$table_prefix, _ALIRO_DATABASE_CACHE_TIME);
		if (!$this->DBInfo) {
			$this->DBinfo = new stdClass();
			$this->DBInfo->DBTables = array();
			$this->DBInfo->DBFields = array();
			$this->DBInfo->DBFieldsByName = array();
		}
		$this->_table_prefix = $table_prefix;
		$this->getTableInfo();
	}

	public function __destruct () {
		try {
			@session_write_close();
			if (aliro::getInstance()->installed) $this->saveStats();
    	} catch (databaseException $exception) {
    		exit('DB Error during shutdown');
    	}
	}

	private function clearCache () {
		$this->cache->clean();
	}
	
	private function getTableInfo () {
		if (count($this->DBInfo->DBTables) == 0) {
			$this->setBareQuery ("SHOW TABLES");
			if ($results = $this->loadResultArray()) foreach ($results as $result) $this->DBInfo->DBTables[] = $this->restoreOnePrefix($result);
			$this->cache->save($this->DBInfo);
		}
	}

	private function storeFields ($tablename) {
		if (!isset($this->DBInfo->DBFields[$tablename])) {
			$this->DBInfo->DBFields[$tablename] = $this->doSQLget("SHOW FIELDS FROM `$tablename`");
			$this->DBInfo->DBFieldsByName[$tablename] = array();
			foreach ($this->DBInfo->DBFields[$tablename] as $field) $this->DBInfo->DBFieldsByName[$tablename][$field->Field] = $field;
			$this->cache->save($this->DBInfo);
		}
	}
	
	public function getName () {
		return $this->DBname;
	}

	public function getAllFieldInfo ($tablename) {
		$this->storeFields($tablename);
		return $this->DBInfo->DBFields[$tablename];
	}

	public function getAllFieldNames ($tablename) {
		$this->storeFields($tablename);
		return array_keys($this->DBInfo->DBFieldsByName[$tablename]);
	}

	public function addFieldIfMissing ($tablename, $fieldname, $fieldspec, $alterIfPresent=false) {
		if (in_array($fieldname, $this->getAllFieldNames($tablename))) {
			if ($alterIfPresent) return $this->alterField($tablename, $fieldname, $fieldspec);
			return false;
		}
		$this->doSQL("ALTER TABLE $tablename ADD `$fieldname` ".$fieldspec);
		$this->clearCache();
		return true;
	}

	public function alterField ($tablename, $fieldname, $fieldspec) {
		if (!in_array($fieldname, $this->getAllFieldNames($tablename))) return false;
		$this->doSQL("ALTER TABLE $tablename CHANGE COLUMN `$fieldname` ".$fieldspec);
		$this->clearCache();
		return true;
	}

	public function getFieldInfo ($tablename, $fieldname) {
		$this->storeFields($tablename);
		return isset($this->DBInfo->DBFieldsByName[$tablename][$fieldname]) ? $this->DBInfo->DBFieldsByName[$tablename][$fieldname] : null;
	}

	public function setFieldValue ($value) {
		if (is_numeric($value)) {
			if ((string) $value == (string) (int) $value) return (string) $value;
			else return "'".(string) $value."'";
		}
		$value = $this->getEscaped($value);
		return "'".$value."'";
	}

	// Expects parameter to be of the form #__name_of_table, so no need to look for DB prefix
	public function tableExists ($tablename) {
		return in_array($tablename, $this->DBInfo->DBTables);
	}

	protected function forceOffline ($error_number) {
			new aliroOffline ($error_number);
			// Uncomment this for more diagnostics
			// echo aliroRequest::trace();
			exit();
	}

	// Deprecated in favour of leaving all error handling to the system
	public function getErrorNum() {
		return $this->_errorNum;
	}

	// Deprecated as above
	public function getErrorMsg() {
		return str_replace( array( "\n", "'" ), array( '\n', "'" ), $this->_errorMsg );
	}

	// Takes a string and escapes any characters needing it
	public function getEscaped($text) {
		return $this->interface->getEscaped($text);
	}

	// Deprecated - does not add enough value
	public function Quote( $text ) {
		return '\''.$this->getEscaped($text).'\'';
	}

	// No conversion of prefix marker - use only internally
	protected function setBareQuery($sql) {
		$this->_sql = $sql;
	}

	// Replaces #_ by the chosen database prefix and saves the query
	public function setQuery( $sql, $cached=false, $prefix='#__' ) {
		$this->_sql = $this->replacePrefix($sql, $prefix);
		$this->_cached = $cached;
	}

	// Carries out prefix marker replacement
	public function replacePrefix ($sql, $prefix='#__') {
		$text = $sql;
		$result = '';
		while ($text) {
			$firstquote = $this->nonzeromin(strpos($text, "'"), strpos($text, '"'));
			if ($firstquote) {
				$result .= str_replace($prefix, $this->_table_prefix, substr($text,0,$firstquote));
				$text = substr($text, $firstquote);
				$endquote = $this->findMatchingQuote($text, $text[0]);
				$result .= substr($text, 0, $endquote+1);
				$text = substr($text, $endquote+1);
			}
			else {
				$result .= str_replace($prefix, $this->_table_prefix, $text);
				break;
			}
		}
		return $result;
	}

	private function nonzeromin ($x, $y) {
		if (false === $x) return $y;
		if (false === $y) return $x;
		return min($x, $y);
	}

	private function findMatchingQuote ($text, $quote) {
		$skip = 1;
		do {
			$endquote = $quote ? strpos($text, $quote, $skip) : strlen($text) - 1;
			if ($endquote) $skip = $endquote+1;
		}
		while ($endquote AND '\\' == $text[$endquote-1]);
		if ($endquote) return $endquote;
		else return strlen($text)-1;
	}

	public function restoreOnePrefix ($tablename) {
		if (substr($tablename, 0, strlen($this->_table_prefix)) === $this->_table_prefix) return '#__'.substr($tablename, strlen($this->_table_prefix));
		else return $tablename;
	}

	// Returns stored SQL with replacements, ready to display
	public function getQuery ($sql='') {
		if ($sql == '') $sql = $this->_sql;
		return "<pre>" . htmlspecialchars( $sql ) . "</pre>";
	}

	public function query ($sql='') {
		if (empty($sql)) $sql = $this->_sql;
		$timer = new aliroProfiler('Database timer');
		if ($this->_cursor = $this->interface->query($sql)) {
			$this->_errorNum = 0;
			$this->_errorMsg = '';
			$stats = new stdClass;
			$stats->timer = $timer->getElapsed();
			$stats->trace = aliroRequest::trace(false);
			$stats->sql = $sql;
			self::$stats[] = $stats;
			$this->_log[] = htmlspecialchars($sql).'<br />'.$timer->mark('secs for query').'<br />'.$stats->trace;
			return $this->_cursor;
		}
		else {
			$this->_errorNum = $this->interface->errno();
			$this->_errorMsg = $this->interface->error()." SQL=$sql";
			throw new databaseException ($this->DBname, $this->_errorMsg, $this->_sql, $this->_errorNum, aliroRequest::trace());
		}
	}

	// Combined operation - takes SQL and executes it
	public function doSQL ($sql) {
		$this->setQuery($sql);
		return $this->query();
	}

	// Combined operation - as above - and returns an array of objects of the specified class
	public function doSQLget ($sql, $classname='stdClass', $key='', $max=0) {
		$this->setQuery($sql);
		$rows = $this->retrieveResults ($key, 0, 'object');
		if ('stdClass' == $classname) return $max ? array_slice($rows, 0, $max) : $rows;
	    $result = array();
		foreach ($rows as $sub=>$row) {
			$next = new $classname();
			foreach (get_object_vars($row) as $field=>$value) $next->$field = $value;
			$result[$sub] = $next;
			if ($max AND count($result) >= $max) return $result;
		}
		return $result;
	}

	public function query_batch() {
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($this->interface->multiQuery($this->_sql)) {
		    do $result = $this->interface->storeResult();
		    while ($this->interface->nextResult());
		}
	}

	public function getNumRows ($cur=null) {
		return $this->interface->getNumRows($cur);
	}

	public function getAffectedRows () {
		return $this->interface->getAffectedRows();
	}

	protected function retrieveResults ($key='', $max=0, $result_type='row') {
		$results = array();
		if (!in_array($result_type, array ('row', 'object', 'assoc'))) die ('unexpected result type='.$result_type);
		$sql_function = $this->interface->getFetchFunc().$result_type;
		if ($cur = $this->query()) {
			while ($row = $sql_function($cur)) {
				if ($key != '') $results[(is_array($row) ? $row[$key] : $row->$key)] = $row;
				else $results[] = $row;
				if ($max AND count($results) >= $max) break;
			}
			$this->interface->freeResultSet($cur);
		}
		return $results;
	}

	public function loadResult() {
		$results = $this->retrieveResults('', 1, 'row');
		if (count($results)) return $results[0][0];
		else return null;
	}

	public function loadResultArray($numinarray = 0) {
		$results = $this->retrieveResults('', 0, 'row');
		foreach ($results as $result) $values[] = $result[$numinarray];
		return isset($values) ? $values : null;
	}

	public function loadAssocList( $key='' ) {
		$results = $this->retrieveResults($key, 0, 'assoc');
		if (count($results)) return $results;
		else return null;
	}

	// Of questionable value - not used in Aliro except for compatibility in mambofunc
	public function mosBindArrayToObject( $array, $obj, $ignore='', $prefix=NULL, $checkSlashes=true ) {
		if (!is_array($array) OR !is_object($obj)) return false;
		if ($prefix == null) $prefix = '';
		foreach (get_object_vars($obj) as $k => $v) {
			if( substr( $k, 0, 1 ) != '_' AND strpos($ignore, $k) === false) {
				if (isset($array[$prefix.$k])) {
					$obj->$k = ($checkSlashes AND get_magic_quotes_gpc()) ? $this->mosStripslashes( $array[$prefix.$k] ) : $array[$prefix.$k];
				}
			}
		}
		return true;
	}

	// Of questionable value - not used in Aliro except for compatibility in mambofunc
	public function mosStripslashes($value) {
	    if (is_string($value)) $ret = stripslashes($value);
		elseif (is_array($value)) {
	        $ret = array();
	        foreach ($value as $key=>$val) $ret[$key] = $this->mosStripslashes($val);
	    }
		else $ret = $value;
	    return $ret;
	} // mosStripSlashes

	// May be obscure to users how this will behave depending on prior setting of parameter
	public function loadObject(&$object) {
		if (!is_object($object)) $results = $this->retrieveResults('', 1, 'object');
		else $results = $this->retrieveResults('', 1, 'assoc');
		if (0 == count($results)) return false;
		if (!is_object($object)) $object = $results[0];
		else {
			if (is_subclass_of($object, 'aliroDBGeneralRow')) $object->bind($results[0], '', false);
			else foreach (get_object_vars($object) as $k => $v) {
				if ($k[0] != '_' AND isset($results[0][$k])) $object->$k = $results[0][$k];
			}
		}
		return true;
	}

	public function loadObjectList( $key='' ) {
		$results = $this->retrieveResults($key, 0, 'object');
		if (count($results)) return $results;
		else return null;
	}

	public function loadRow() {
		$results = $this->retrieveResults('', 1, 'row');
		if (count($results)) return $results[0];
		else return null;
	}

	public function loadRowList( $key='' ) {
		$results = $this->retrieveResults($key, 0, 'row');
		if (count($results)) return $results;
		else return null;
	}

	public function insertObject ($table, $object, $keyName=NULL) {
		$dbfields = $this->getAllFieldNames($table);
		foreach ($dbfields as $name) {
			if (!isset($object->$name) OR is_array($object->$name) OR is_object($object->$name)) continue;
			$fields[] = "`$name`";
			$values[] = $this->setFieldValue($object->$name);
		}
		if (isset($fields)) {
			$result = $this->doInsertion ($table, implode( ",", $fields ), implode( ",", $values ));
			// insertid() is only meaningful if non-zero
			$autoinc = $this->insertid();
			if ($autoinc AND $keyName AND !is_array($keyName)) $object->$keyName = $autoinc;
			return $result;
		}
		else {
			trigger_error (sprintf(T_('Insert into table %s but no fields'), $this->tableName));
			echo aliroRequest::trace();
			return false;
		}
	}

	private function doInsertion ($table, $fields, $values) {
		return $this->doSQL("INSERT INTO $table ($fields) VALUES ($values)");
	}

	public function updateObject ($table, $object, $keyName, $updateNulls=true) {
		$dbfields = $this->getAllFieldNames($table);
		foreach ($dbfields as $name) {
			if (!isset($object->$name) OR is_array($object->$name) OR is_object($object->$name)) {
				if ($updateNulls) $value = "''";
				else continue;
			}
			else $value = $this->setFieldValue($object->$name);
			$setter = "`$name` = $value";
			if (is_array($keyName) AND in_array($name, $keyName)) $where[] = $setter;
			elseif ($name == $keyName) $where[] = $setter;
			else $setters[] = $setter;
		}
		if (!isset($where)) {
			trigger_error (sprintf(T_('Update table %s but no key fields'), $table));
			return false;
		}
		if (isset($setters)) return $this->doUpdate ($table, implode (', ', $setters), implode (' AND ' , $where));
		return true;
	}

	private function doUpdate ($table, $setters, $conditions) {
		return $this->doSQL("UPDATE $table SET $setters WHERE $conditions");
	}

	// Deprecated in favour of allowing the system to handle errors
	public function stderr( $showSQL = false ) {
		return "DB function failed with error number $this->_errorNum"
		."<br /><font color=\"red\">$this->_errorMsg</font>"
		.($showSQL ? "<br />SQL = <pre>$this->_sql</pre>" : '');
	}

	public function insertid() {
		return $this->interface->insertid();
	}

	public function getVersion()
	{
		return $this->interface->getVersion();
	}

	/**
	* Fudge method for ADOdb compatibility???? Not used in Aliro
	*/
	public function GenID () {
		return '0';
	}

	// Usual use is to check for existence of a table - easier to use tableExists which expects #_ type table name
	// Also more efficient as tableExists uses cache
	public function getTableList() {
		$this->setQuery('SHOW tables');
		return $this->loadResultArray();
	}

	// This is probably useful - what exactly does it do?
	public function getTableCreate( $tables ) {
		$result = array();

		foreach ($tables as $tblval) {
			$this->setQuery( 'SHOW CREATE table ' . $tblval );
			$this->query();
			$result[$tblval] = $this->loadResultArray( 1 );
		}

		return $result;
	}

	// This is also potentially useful, but requires translated prefix.
	// Easier to use getAllFieldNames repeatedly - also more efficient, as uses cache
	public function getTableFields( $tables ) {
		$result = array();

		foreach ($tables as $tblval) {
			$fields = $this->doSQLget ( 'SHOW FIELDS FROM ' . $tblval );
			foreach ($fields as $field) {
				$result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
			}
		}

		return $result;
	}
	
	public function getCount () {
		return count($this->_log);
	}

	public function getLogged () {
		$text = '<h4>'.$this->getCount().' queries executed</h4>';
	 	foreach ($this->_log as $k=>$sql) $text .= "\n".($k+1)."<br />".$sql.'<hr />';
		if (count($this->_log)) return $text;
		else return '';
	}

	private function saveStats () {
		new aliroObjectSorter(self::$stats, 'timer');
		$n = count(self::$stats);
		if ($n > 0) {
			$median = self::$stats[intval($n/2)]->timer;
			$total = 0.0;
			foreach (self::$stats as $stat) $total += $stat->timer;
			$mean = $total/$n;
			$var = 0.0;
			foreach (self::$stats as $stat) $var += ($stat->timer - $mean) * ($stat->timer - $mean);
			$stdev = sqrt($var);
			$best = self::$stats[0]->timer;
			$worst = self::$stats[$n-1]->timer;
			$elapsed = aliro::getInstance()->getElapsed();
			$memory = memory_get_usage();
			$uri = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
			aliroCoreDatabase::getInstance()->doSQL("INSERT INTO #__query_stats (count, mean, median, stdev, best, worst, total, elapsed, memory, uri) VALUES ($n, '$mean', '$median', '$stdev', '$best', '$worst', '$total', '$elapsed', '$memory', '$uri')");
			$queryid = $this->insertid();
			$stats = self::$stats;
			foreach ($stats as $stat) {
				if (0.50 < $stat->timer) {
					$querytext = htmlspecialchars($stat->sql, ENT_QUOTES);
					$tracetext = $this->getEscaped($stat->trace, ENT_QUOTES);
					$sql = "INSERT INTO {$this->_table_prefix}query_slow (queryid, time, trace, querytext) VALUES ($queryid, '$stat->timer', '$tracetext', '$querytext')";
					$this->interface->query($sql);
				}
			}
		}
		self::$stats = array();
	}

}

class database extends aliroAbstractDatabase {

}

class aliroDatabaseHandler extends aliroAbstractDatabase {

}

class aliroDatabase {

	protected static $instance = __CLASS__;
	protected $database;

	protected function __construct () {
		$credentials = aliroCore::getConfigData('credentials.php');
		$this->database = new aliroDatabaseHandler ($credentials['dbhost'], $credentials['dbusername'], $credentials['dbpassword'], $credentials['dbname'], $credentials['dbprefix']);
	}

	protected function __clone () {
		// Enforce singleton
	}

	public function __call ($method, $args) {
		return call_user_func_array(array($this->database, $method), $args);
	}
	
	// Required because parameter must be passed by reference, not by value
	public function loadObject (&$object) {
		return $this->database->loadObject($object);
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
	}

}

class mamboDatabase extends aliroDatabase {
	// Just an alias really
}

// Similar to aliroDatabase but with any conflicting methods overriden
class joomlaDatabase extends aliroDatabase {
	protected static $instance = __CLASS__;
	
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
	}
	
	public function loadObject () {
		$object = null;
		$this->database->loadObject($object);
		return $object;
	}
}

class aliroCoreDatabase extends aliroDatabase {

	protected static $instance = __CLASS__;

	protected function __construct () {
		$credentials = aliroCore::getConfigData('corecredentials.php');
		$this->database = new aliroDatabaseHandler ($credentials['dbhost'], $credentials['dbusername'], $credentials['dbpassword'], $credentials['dbname'], $credentials['dbprefix']);
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
	}
}