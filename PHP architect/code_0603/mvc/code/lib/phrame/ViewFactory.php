<?php
/**
 *	view class directory
 */
define('PHRAME_VIEW_CLASS_DIR', 'views/');
/**
 *	class to create Phrame View Objects
 *
 *	@author		Jason E. Sweat	
 *	@since		2003-01-13
 */
class ViewFactory extends Object
{
	/**
	 *	constructor
	 *
	 *	@return void
	 */
	function ViewFactory()
	{
		trigger_error("ViewFactory is a virtual class, please extend for your application");
		return false;
	}

	/**
	 *	abstract function to return class based on view
	 *
	 *	Must be overridden in the application, should always return a valid class file.
	 *
	 *	@return void
	 */
	function _GetViewClass($psView)
	{
		$s_error = 'ViewFactory::_GetViewClass is a virtual method, please extend for your application';
		trigger_error($s_error);
		die($s_error);
	}
	
	/**
	 *	factory function
	 *
	 *	@return object	the view object
	 */
	function &Build($psView)
	{
		$s_view_class = $this->_GetViewClass($psView);
		require_once PHRAME_VIEW_CLASS_DIR.$s_view_class.'.php';
		return new $s_view_class;
	}
	
}
