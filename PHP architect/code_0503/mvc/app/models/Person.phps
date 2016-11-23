<?php
/**
 * maximun character to accept for name
 */
define('PERSON_MAX', 20);
/**
 * session index to store class data
 */
define('PERSON_SESSION_INDEX', '_person');

/**
 * Personal data - allow for change and retrieval of name
 */
class Person
{
	/**
	 * @var	string	$_name	name of the person
	 */
	var $_name;
	
	/**
	 * constructor
	 *
	 * @param	string	$psName	optional - name to use by default\
	 * @return	void
	 */
	function Person($psName = null)
	{
		if (!array_key_exists(PERSON_SESSION_INDEX, $_SESSION)) {
			$_SESSION[PERSON_SESSION_INDEX] = $psName;
		}
		$this->_name =& $_SESSION[PERSON_SESSION_INDEX];
	}
	
	/**
	 * retrieve the name
	 *
	 * @return	string	the name
	 */
	function GetName()
	{
		return $this->_name;
	}
	
	/**
	 * set the name
	 *
	 * @param	string	$psName	the name to use
	 * @return	boolean		sucess
	 */
	function SetName($psName)
	{
		$b_valid = true;
		if (strlen($psName) > PERSON_MAX) {
			appl_error('Name > '.PERSON_MAX.' characters');
			$b_valid = false;
		} elseif ( 0 == strlen($psName) ) {
			appl_error('Please enter a name.');
			$b_valid = false;
		} else {
			$this->_name = $psName;
		}
		return $b_valid;
	}
}
?>
