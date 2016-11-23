<?php
class eventlog {

	// Properties

	// Build an array of the log type constants and their corresponding values

	var $log_types=array(array("EVENTLOG_SUCCESS", 0), array("EVENTLOG_ERROR_TYPE", 1),
	array("EVENTLOG_WARNING_TYPE", 2), array("EVENTLOG_INFORMATION_TYPE", 4),
	array("EVENTLOG_AUDIT_SUCCESS", 8), array("EVENTLOG_AUDIT_FAILURE", 10));
	var $logtype;

	var $hEventLog;


	// Constructor

	function eventlog() {

		// Register w32api functions

		w32api_register_function("advapi32.dll", "RegisterEventSourceA", "long");
		w32api_register_function("advapi32.dll", "ReportEventA", "long");
		w32api_register_function("advapi32.dll", "DeregisterEventSource", "long");

		// Register event source

		$this->hEventLog=RegisterEventSourceA(NULL, "WebApp");
	}

	// Report Event Method

	function reportevent($eventid, $logtype) {
		$logtypefound=false;

		// Match the string log type passed to a value from the array of constants

		for ($i=0; $i<sizeof($this->log_types); $i++) {
			if ($this->log_types[$i][0] == $logtype) {
				$this->logtype=$this->log_types[$i][1];
				$logtypefound=true;
			}
		}
		if (!$logtypefound)
			return false;

		// Report the event

		if (!ReportEventA($this->hEventLog, $this->logtype, 0, $eventid, NULL, 0, 0, NULL, NULL))				return false;
		return true;
	}

	// Destructor
	
	function destructor() {

		// De register the event source

		DeregisterEventSource($this->hEventLog);
	}
}
?>
<!-- HTML Form --!>
<html>
<head>
	<title>Authentication Form</title>
</head>
<body>
<!-- Begin dynamic content --!>
<?php
$u='testuser';
$p='testpass';
// Instantiate an eventlog object



if (isset($u) && isset($p)) {
	if (auth($u, $p)) {
		echo "<h2>Authentication Successful!</h2>\n</body></html>\n";

		// Instatiate an eventlog object, report the event and destroy the object

		$el_obj = New eventlog();
		$el_obj->reportevent(2, "EVENTLOG_INFORMATION_TYPE");
		$el_obj->destructor();
		unset($el_obj);
		exit;
	} else {
		echo "<font color=\"#FF0000\"><h2>Authentication Failed</h2></font>\n";

		// Instatiate an eventlog object, report the event and destroy the object

		$el_obj = New eventlog();
		$el_obj->reportevent(1, "EVENTLOG_WARNING_TYPE");
		$el_obj->destructor();
		unset($el_obj);

	}
	

}

function auth($u, $p) {
	if ($u == 'testuser' && $p == 'testpass')
		return true;
	return false;
}
 
class eventlog {

	// Properties

	// Build an array of the log type constants and their corresponding values

	var $log_types=array(array("EVENTLOG_SUCCESS", 0), array("EVENTLOG_ERROR_TYPE", 1),
	array("EVENTLOG_WARNING_TYPE", 2), array("EVENTLOG_INFORMATION_TYPE", 4),
	array("EVENTLOG_AUDIT_SUCCESS", 8), array("EVENTLOG_AUDIT_FAILURE", 10));
	var $logtype;

	var $hEventLog;


	// Constructor

	function eventlog() {

		// Register w32api functions

		w32api_register_function("advapi32.dll", "RegisterEventSourceA", "long");
		w32api_register_function("advapi32.dll", "ReportEventA", "long");
		w32api_register_function("advapi32.dll", "DeregisterEventSource", "long");

		// Register event source

		$this->hEventLog=RegisterEventSourceA(NULL, "WebApp");
	}

	// Report Event Method

	function reportevent($eventid, $logtype) {
		$logtypefound=false;

		// Match the string log type passed to a value from the array of constants

		for ($i=0; $i<sizeof($this->log_types); $i++) {
			if ($this->log_types[$i][0] == $logtype) {
				$this->logtype=$this->log_types[$i][1];
				$logtypefound=true;
			}
		}
		if (!$logtypefound)
			return false;

		// Report the event

		if (!ReportEventA($this->hEventLog, $this->logtype, 0, $eventid, NULL, 0, 0, NULL, NULL))				
			return false;

		return true;
	}

	// Destructor
	
	function destructor() {

		// De register the event source

		DeregisterEventSource($this->hEventLog);
	}
}

?>
<!-- End dynamic content --!>
<form action="eventlog.php" method="post">
<table border="0" cellspacing="3" cellpadding="3">
<tr>
<td><b>Username:</b></td>
<td><input type="text" name="u"></td>
</tr>
<tr>
<td><b>Password:</b></td>
<td><input type="text" name="p"></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Authenticate"></td>
</tr>
</table>
</form>
</body>
</html>
