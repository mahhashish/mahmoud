<?php
/**
 * Class Object is the root of the class hierarchy. Every class has Object as a
 * superclass. All objects, including arrays, implement the methods of this
 * class.
 *
 * @author	Arnold Cano
 * @version	$Id: Object.php,v 1.1.1.1 2002/11/19 16:46:54 arcano Exp $
 */
class Object
{
	/**
	 * Returns a copy of this object instance.
	 *
	 * @access	public
	 * @return	mixed
	 */
	function clone()
	{
		return $this;
	}
	/**
	 * Compares the specified object with this object for equality.
	 *
	 * @access	public
	 * @param	mixed	$object
	 * @return	boolean
	 */
	function equals($object)
	{
		return ($this === $object);
	}
	/**
	 * Returns a string representation of this object.
	 *
	 * @access	public
	 * @return	string
	 */
	function toString()
	{
		return var_export($this, TRUE);
	}
}
?>
