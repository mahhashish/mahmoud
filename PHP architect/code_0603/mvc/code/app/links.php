<?php
/**
 * links application bootstrap file
 *
 * @author		Jason E. Sweat
 * @since		2003-04-26
 * @package		PHP_Architect_MVC_links_example
 * @subpackage	bootstrap
 */
error_reporting(E_ALL);

/**
 * application setup
 */
require_once 'links_setup.php';

//set default action if none specified
if (!array_key_exists(_ACTION, $_REQUEST)) {
        $_REQUEST[_ACTION] = 'ShowView';
}

//create Phrame controller
$go_controller = new ActionController($go_map->GetOptions());

//release control to controller for further processing
$go_controller->Process($go_map->GetMappings(), $_REQUEST);

//debugging code
trigger_error('Action with no redirect?');
if ($gb_debug) {
        print "<pre>\n";
        var_dump($_SESSION);
}

?>
