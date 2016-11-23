<?php

/**
 * application error handling
 */
class HelloErrors
{
	/**
	 * @var	array	the errors
	 */
	var $_errors;
	
	/**
	 * constructor
	 *
	 * assosiate the $_errors variable with the session index for errors
	 * @return	void
	 */
	function HelloErrors()
	{
		if(!array_key_exists(_ERRORS, $_SESSION)) {
			$_SESSION[_ERRORS] = array();
		}
		$this->_errors =& $_SESSION[_ERRORS];
	}
	
	/**
	 * add an application error
	 *
	 * @param	string	$psErrorMsg	the error message to track
	 * @return	void
	 */
	function AddError($psErrorMsg) 
	{
		$this->_errors[] = $psErrorMsg;
	}
	
	/** 
	 * determine if the application has errors
	 *
	 * @return	boolean
	 */
	function HasErrors()
	{
		return (count($this->_errors)) ? true : false;
	}
	
	/**
	 * retrieve the application errors
	 *
	 * @return	array	application errors
	 */
	function GetErrors($pbClear=true)
	{
		$a_ret = $this->_errors;
		if ($pbClear) {
			$this->_errors = array();
		}
		return $a_ret;
	}
}

?>
