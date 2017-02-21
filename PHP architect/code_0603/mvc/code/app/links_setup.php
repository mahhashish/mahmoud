<?php
/**
 * links application setup
 *
 * @author	Jason E. Sweat
 * @since	2003-04-27
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	bootstrap
 */

/**
 * main lib path
 */
define('LINKS_LIB', '../lib/');
/**
 * Phrame library path
 */
define('LIB_PHRAME', LINKS_LIB.'phrame/');
/**
 * Smarty library path
 */
define('LIB_SMARTY', LINKS_LIB.'Smarty-2.5.0/libs/');
/**
 * ADOdb library path
 */
define('LIB_ADODB', LINKS_LIB.'adodb/');
/**
 * Eclipse library path
 */
define('LIB_ECLIPSE', LINKS_LIB.'eclipse/');

/**
 *	phrame library
 *	
 *	{@link http://phrame.itsd.ttu.edu/ Phrame homepage}
 *	{@link http://sourceforge.net/projects/phrame/ sourceforge project}
 */
require_once '../lib/phrame/include.jes.php';
/**
 * define this only when debugging to disable the phrame error handling
 */
//define('DISABLE_PHRAME_ERROR_HANDLING', true);
/**
 *	links Mappings
 */
require_once 'LinksMap.php';
/**
 *	Smarty Template System
 *
 *	{@link http://smarty.php.net/ smarty.php.net}
 *	{@link http://smarty.incutio.com/ smarty wiki}
 *	{@link http://marc.theaimsgroup.com/?l=smarty-general&r=1&w=2 mail list archive}
 */
require_once LIB_SMARTY.'Smarty.class.php';
/**
 * include ADOdb database access class
 *
 * {@link http://php.weblogs.com/ADOdb/ php.weblogs.com/ADOdb}
 */
require_once LIB_ADODB.'adodb.inc.php';
/**
 *    array iterator from eclipse library
 * {@link }
 */
require_once LIB_ECLIPSE.'ArrayIterator.php';
/**
 * applicaiton error class
 */
require_once 'models/Errors.php';
/**
 * User Model
 */
require_once 'models/User.php';
//all classes defined, we can now load the session
session_start();


if(defined('DISABLE_PHRAME_ERROR_HANDLING') && DISABLE_PHRAME_ERROR_HANDLING !== false) {
	error_reporting(E_ALL);
	print "DISABLE_PHRAME_ERROR_HANDLING debugging mode enabled<p>\n";
}

/**
 *	application code
 */
define('APPL', 'LINKS');
/**
 *	applicaiton base url
 */
define('APPL_BASE', 'links.php?'._VIEW.'=');
/**
 *	applicaiton base action url
 */
define('APPL_ACTN', 'links.php');
/**
 *	database error message
 */
define('DB_OOPS', APPL.'A database related error has occured, if the problem persists, please contact Jason Sweat for assistance with this application.');
/**
 *	redirect for errors
 */
define('ERROR_VIEW', 'Location: '.APPL_BASE.'index');

//initialize error object
$go_errors =& new Errors;

//develoment mode?
$gb_debug = false;//(strpos($_SERVER['SCRIPT_FILENAME'],'public_html')>0) ? true : false;

//global mapping object
$go_map =& new LinksMap;

//global connection object
$go_conn = &ADONewConnection('postgres');
$go_conn->PConnect('', 'linkuser', 'linkpass', 'links');
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

/**
 *    log an application error message using the applicaiton error handler
 *
 *    @param    string    $psMsg         the message to log    
 *    @return void
 */
function appl_error($psMsg)
{    
    set_error_handler('handle_error');
    trigger_error(APPL.$psMsg);
    restore_error_handler();
}

/**
 * This is the application error handler.
 *
 * @access    public
 * @global	boolean	$gb_debug 
 * @param    string    $number
 * @param    string    $message
 * @param    string    $file
 * @param    string    $line
 * @param    string    $context
 */
function handle_error($piNumber, $psMessage, $psFile, $piLine, $psContext)
{
	global $gb_debug;
	
    $s_debug_okay = '<br>DEBUG MODE: The user will not be notified of this error.';
    $s_debug_warn = '<br>If this problems persists, contact Jason Sweat';
    $a_ignore = array(
         'Undefined index:  show_script'
        ,'Undefined index:  title_extra'
        ,'Undefined offset:  0'
        ,'Undefined offset:  1'
        ,'Undefined offset:  2'
        ,'Undefined offset:  3'
        ,'Undefined offset:  4'
        ,'Undefined offset:  5'
        ,'Undefined offset:  6'
        ,'Undefined offset:  7'
        ,'Undefined offset:  8'
        ,'Undefined offset:  9'
        );
	
	$o_errors =& new Errors;
    
    if (in_array(trim($psMessage), $a_ignore)) {
        // exit, because we don't care
        return false;
    }
    
    if (APPL == substr($psMessage, 0, strlen(APPL))) {
        $s_msg = substr($psMessage, strlen(APPL));
        $o_errors->Push($s_msg);
    } else {
		if ($gb_debug) {
			print "<b>PHP Error:</b> $piNumber: $psMessage<br>($psFile: line $piLine)<p>";
			//print "<pre>\n"; var_dump($psContext); print"</pre>\n";
		}
        $s_msg = "$psMessage ($psFile: $piLine)";
        if (strpos($psFile, '.tpl')>0 || 
            strpos($psFile, 'Smarty')>0
            ) {
            if ($gb_debug) {
                $o_errors->Push($s_msg.$s_debug_okay);
            }
        } else { //not a known issue we can skip
            $o_errors->Push($s_msg.$s_debug_warn);
        }
    }
}

?>
