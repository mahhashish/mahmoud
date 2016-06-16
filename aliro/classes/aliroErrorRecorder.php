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
 * aliroErrorRecorder provides a simple way to log errors to the database.  It
 * will accept a short message, a long message, and optionally an exception as
 * parameters.  It derives for itself the POST and GET data, and also a trace
 * of execution.  The whole is stored as a database record, in a table which
 * is pruned to keep it to a maximum of 7 days so it will not grow too large.
 *
 */

class aliroErrorRecorder extends aliroDatabaseRow  {
    protected static $instance = null;
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__error_log';
	protected $rowKey = 'id';

	public static function getInstance ($request=null) {
	    return (null == self::$instance) ? (self::$instance = new self()) : self::$instance;
	}

	public function PHPerror ($errno, $errstr, $errfile, $errline, $errcontext) {
		if (!($errno & error_reporting())) return;
	    $rawmessage = function_exists('T_') ? T_('PHP Error %s: %s in %s at line %s') : 'PHP Error %s: %s in %s at line %s';
	    $message = sprintf($rawmessage, $errno, $errstr, $errfile, $errline);
        $lmessage = $message;
        if (is_array($errcontext)) {
            foreach ($errcontext as $key=>$value) if (!is_object($value) AND !(is_array($value))) $lmessage .= "; $key=$value";
        }
        $errorkey = "PHP/$errno/$errfile/$errline/$errstr";
	    $this->recordError($message, $errorkey, $lmessage);
	    aliroRequest::getInstance()->setErrorMessage(T_('A PHP error has been recorded in the log'), _ALIRO_ERROR_WARN);
	    if ($errno & (E_USER_ERROR|E_COMPILE_ERROR|E_CORE_ERROR|E_ERROR)) die (T_('Serious PHP error - processing halted - see error log for details'));
	}

	public function recordError ($smessage, $errorkey, $lmessage='', $exception=null) {
	    $this->id = 0;
		$this->timestamp = date ('Y-m-d H:i:s');
		$this->smessage = $smessage;
		$this->lmessage = $lmessage ? $lmessage : $smessage;
		$this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$database = aliroCoreDatabase::getInstance();
		$this->errorkey = $database->getEscaped($errorkey);
		$this->get = $_SERVER['REQUEST_URI'];
		$this->post = base64_encode(serialize($_POST));
		$this->trace = aliroRequest::trace();
		if ($exception instanceof databaseException) {
			$this->dbname = $exception->dbname;
			$this->sql = $exception->sql;
			$this->dberror = $exception->getCode();
			$this->dbmessage = $exception->getMessage();
			$this->dbtrace = $exception->dbtrace;
		}
		else $this->dbname = $this->sql = $this->dberror = $this->dbmessage = null;
		$database->setQuery("SELECT id FROM #__error_log WHERE errorkey = '$this->errorkey'");
		$id = $database->loadResult();
		if (!$id) $this->store();
		else $database->doSQL("UPDATE #__error_log SET timestamp = NOW() WHERE id = $id");
		// code to prune error log - limit to max items, max days
		$database = call_user_func(array($this->DBclass, 'getInstance'));
		$database->doSQL("DELETE LOW_PRIORITY FROM $this->tableName WHERE timestamp < SUBDATE(NOW(), INTERVAL 7 DAY)");
	}
}