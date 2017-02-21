<?php
/**
 *	View class definition
 *
 *	@author		Jason E. Sweat
 *	@since		2003-03-23
 */

/**
 * View base class
 */
class View
{
	/**
	 *	@var	object	ActionForm
	 */
	var $_moForm;
	
	/**
	 *	@var	object	smarty template instance
	 */
	var $_moTpl;
	
	/**
	 *	@var	boolean	has the view been prepared for rendering
	 */
	var $_mbPrepared = false;
	
	/**
	 *	constructor method
	 *	@return	void
	 */
	function Init(&$poTpl, &$poForm)
	{
		$this->_moTpl  =& $poTpl;
		$this->_moForm =& $poForm;
	}

	/**
	 *
	 *	@return	void
	 */
	function Prepare()
	{
		$this->_mbPrepared = true;
	}

	/**
	 *
	 *	@return	void
	 */
	function Render()
	{		
		if (!$this->_mbPrepared) {
			$this->Prepare();
		}
		
		$this->ProcessErrors();
		$this->_moTpl->Display($this->_msTemplate);
	}

	/**
	 *	do this just before display to catch all errors for the page
	 *
	 *	@param	object	$poTpl	the smarty template object
	 *	@return	void
	 */
	function ProcessErrors()
	{
		$o_errors =& new Errors;
		
		$this->_moTpl->Assign('errors', $o_errors->ToArray());
		$o_errors->Clear();
	}
}

?>
