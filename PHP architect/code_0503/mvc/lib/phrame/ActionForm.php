<?php
/**
 * An ActionForm is optionally associated with one or more ActionMappings. The
 * properties will be initialized from the corresponding request parameters
 * before the corresponding Action's perform() method is called. When the
 * properties have been populated, but before the perform() method of the
 * Action is called, the validate() method will be called, which gives a chance
 * to verify that the properties submitted by the user are correct and valid.
 *
 * @author	Arnold Cano
 * @version	$Id: ActionForm.php,v 1.1 2003/05/02 03:46:11 brian Exp $
 */
class ActionForm extends HashMap
{
	/**
	 * Validate the properties that have been set for this request and
	 * return ActionError objects representing any validation errors that have
	 * been found. Subclasses must override this method to provide any
	 * validation they wish to perform.
	 *
	 * @access	public
	 * @return	boolean
	 */
	function validate() {}
	/**
	 * Reset all properties to their default state. This method is called
	 * before the properties are repopulated by the ActionController. The
	 * default implementation does nothing. Subclasses should override this
	 * method to reset all bean properties to default values.
	 *
	 * @access	public
	 */
	function reset() {}
}
?>
