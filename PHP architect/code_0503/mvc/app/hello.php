<?php
/**
 * Phrame "Hello world" example for PHP|Architect Intro to PHP MVC article
 *
 * @author	Jason E. Sweat
 * @since	2003-04-14
 */
error_reporting(E_ALL);

/**
 * path to Phrame library
 *
 * adjust this to where you have phrame installed
 */
define('PHRAME_LIB_PATH', '../lib/phrame/');

/**
 * Phrame library include
 */
require_once PHRAME_LIB_PATH.'include.jes.php';
/**
 * Smarty library include
 */
require_once 'Smarty.class.php';
/**
 * MappingManager class definition
 */
require_once 'MappingManager.php';
/**
 * HelloAction class definition
 */
require_once 'actions/HelloAction.php';
/**
 * Person class definition
 */
require_once 'models/Person.php';
/**
 * HelloErrors class definition
 */
require_once 'models/HelloErrors.php';

/**
 * URL to use when displaying a view
 */
define('APPL_VIEW', 'hello.php?'._VIEW.'=');
/**
 * URL to use when posting actions
 */
define('APPL_ACTN', 'hello.php');

/**
 * HelloMap defines the Phrame mappings for this application
 */
class HelloMap extends MappingManager
{
	/**
	 * constructor method
	 * @return	void
	 */
	function HelloMap()
	{
		//set options
		$this->_SetOptions('handle_error');
		
		//add application forms
		$this->_AddForm('helloForm', 'ActionForm');
		
		//add application actions and forwards
		$this->_AddMapping('sayHello', 'HelloAction', APPL_VIEW.'index',  'helloForm');
		$this->_AddForward('sayHello', 'index');
		$this->_AddForward('sayHello', 'hello', APPL_VIEW.'hello');
	}
}

session_start();

$smarty =& new Smarty;
$map =& new HelloMap;
$controller =& new ActionController($map->GetOptions());
$errors =& new HelloErrors;

if (array_key_exists(_ACTION,$_REQUEST)) {
	//release control to controller for further processing
	$controller->Process($map->GetMappings(), $_REQUEST);
} else {
	//determine and display view
	$requested_view = (array_key_exists(_VIEW, $_REQUEST)) ? strtolower($_GET[_VIEW]) : 'index';
	switch ($requested_view) {
	case 'hello':
		$template = $requested_view.'.tpl';
		//assign view specific data
		$person =& new Person;
		$smarty->Assign('name', $person->GetName());
		break;
	case 'index':
	default:
		$template = 'index.tpl';
	}
	//assign common data
	$smarty->Assign(array(
		 'appl_link'	=> APPL_ACTN
		,'appl_view'	=> APPL_VIEW
		,'action'	=> _ACTION
		));
	//assign and clear errors
	$smarty->Assign('errors', $errors->GetErrors());
	$smarty->Display($template);
	exit;
}

/**
 * error handler function for this application
 *
 * takes standard PHP error handler parameters
 */
function handle_error($number, $message, $file, $line, $context)
{
	appl_error($message);
}

/**
 * handle an application error
 *
 * @param	string	$psErrorMsg	the error message to handle
 * @return	void
 */
function appl_error($psErrorMsg)
{
	$errors =& new HelloErrors;
	$errors->AddError($psErrorMsg);
}

?>
