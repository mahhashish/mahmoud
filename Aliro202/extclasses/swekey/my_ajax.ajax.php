<?php

// include your comon files here

class my_swekey_ajax {

	// Establish the environment, as required
	public function __construct () {
		if (!defined('_ALIRO_ABSOLUTE_PATH')) {
			$swekeydir = dirname(__FILE__);
			$extclassesdir = dirname($swekeydir);
			$basedir = dirname($extclassesdir);
			require_once($basedir.'/aliro.php');
			aliro::getInstance()->startup(false);
		}
	}

	// REQUIRED: In this function you should set the 'swekey_id' field of the logged
	// user row in the database with the $swekey_id value
	// return true if the update is successfull
	public function AttachSwekeyToLoggedUser($swekey_id)
	{
		// put your code here
		$user = aliroUser::getInstance();
		aliroDatabase::getInstance()->doSQL("UPDATE #__users SET swekey_id = '$swekey_id' WHERE id = $user->id");
		return true;
	}

	// OPTIONAL: Here you should return the name of the user that is attached to the
	// swekey $swekey_id
	function NameOfUserAttachedToSwekey($swekey_id)
	{
		// put your code here
		$database = aliroDatabase::getInstance();
		$database->setQuery("SELECT username FROM #__users WHERE swekey_id = '$swekey_id'");
		return $database->loadResult();
	}
}

$swekey = new my_swekey_ajax();

if (@$_GET['swekey_action'] == 'resolve' && strlen(@$_GET['swekey_id']) == 32)
{
	echo $swekey->NameOfUserAttachedToSwekey(@$_GET['swekey_id']);
    exit;
}

if (@$_GET['swekey_action'] == 'attach' && strlen(@$_GET['swekey_id']) == 32)
{
	echo $swekey->AttachSwekeyToLoggedUser(@$_GET['swekey_id']) ? 'OK': 'FAILED';
    exit;
}