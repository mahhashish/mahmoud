<?php
/**
 * HelloAction sets the current name of the person model
 */
class HelloAction extends Action
{
	/**
	 * Performs the HelloAction class purpose
	 *
	 * @param	object	$poActionMapping	Phrame mappings
	 * @param	object	$poActionForm		Phrame form
	 * @return	object				Phrame forward
	 */
	function &Perform(&$poActionMapping, &$poActionForm) {
		$person =& new Person();
		$errors =& new HelloErrors;
		$name = $poActionForm->Get('name');
		
		//get ActionForward depending on if errors were generated
		if ((!$person->SetName($name)) || ($errors->HasErrors())) {
			$actionForward = $poActionMapping->Get('index');
		} else {
			$actionForward = $poActionMapping->Get('hello');
			}
		return $actionForward;
	}
}

?>
