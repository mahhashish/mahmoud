<?php /* $Id: applog.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

/* Global to store stuff */
$_LOG = array(	
		'debug' 	=> 5,
		'SCRIPT'	=> basename($_SERVER['PHP_SELF'])
		);

/**
 * Writes log entries
 */
function logWrite($cc, $message)
{
	global $_LOG;

	$msg 	= '';
	foreach( explode("\n", trim($message)) AS $line )
	{
		$msg .= $_LOG['SCRIPT'] . "\t{$cc}\t{$line}\n";
	}

	@error_log($msg, 3, APPLOG );
}

/**
 * A fatal error has occurred, we're done, no way to recover.
 */
function logFatal($message, $redirect = false, $errno = '')
{
	logWrite('FT', $message);
	
	if ( $redirect )
	{
		echo '<meta http-equiv="Refresh" contents="0; URL=' . APPLOG_ERROR_URL . "?{$errno}\">";
	}
	exit( 1 );
}

/**
 * An error has occurred but we can go on/cleanup
 */
function logError($message)
{
	logWrite('ER', $message);
}

/**
 * A warning has occurred, we're going on!
 */
function logWarning($message)
{
	logWrite('WR', $message);
}

/**
 * A general info message has occurred, such as CREATED PROJECT[546]
 */
function logInfo($message)
{
	logWrite('IN', $message);
}
?>
