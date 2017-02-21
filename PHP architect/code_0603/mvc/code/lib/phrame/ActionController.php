<?php
/**
 * The ActionController class represents the controller in the
 * Model-View-Controller (MVC) design pattern. The ActionController receives
 * and processes all requests that change the state of the application.
 *
 * Generally, a "Model2" application is architected as follows:
 * <ul>
 * <li>The user interface will generally be created with either PHP pages,
 * which will not themselves contain any business logic. These pages represent
 * the "view" component of an MVC architecture.</li>
 * <li>Forms and hyperlinks in the user interface that require business logic
 * to be executed will be submitted to a request URI that is mapped to the
 * ActionController. The ActionController receives and processes all requests
 * that change the state of a user's interaction with the application. This
 * component represents the "controller" component of an MVC architecture.</li>
 * <li>The ActionController will select and invoke an Action class to perform
 * the requested business logic.</li>
 * <li>The Action classes will manipulate the state of the application's
 * interaction with the user, typically by creating or modifying classes that
 * are stored as session attributes. Such classes represent the "model"
 * component of an MVC architecture.</li>
 * <li>Instead of producing the next page of the user interface directly,
 * Action classes will forward control to an appropriate PHP page to produce
 * the next page of the user interface.</li>
 * </ul>
 * The standard version of ActionController implements the following logic for
 * each incoming HTTP request. You can override some or all of this
 * functionality by subclassing this class and implementing your own version of
 * the processing.
 * <ul>
 * <li>Identify, from the incoming request URI, the substring that will be used
 * to select an Action procedure.</li>
 * <li>Use this substring to map to the class name of the corresponding Action
 * class (a subclass of the Action class).</li>
 * <li>If this is the first request for a particular Action class, instantiate
 * an instance of that class and cache it for future use.</li>
 * <li>Optionally populate the properties of an ActionForm class associated
 * with this ActionMapping and cache it for future use.</li>
 * <li>Call the perform() method of this Action class. Passing in the mapping
 * and the request that were passed to the ActionController by the bootstrap.
 * </li>
 * </ul>
 *
 * The standard version of ActionController is configured based on the
 * following initialization parameters, which you will specify in the options
 * for your application. Subclasses that specialize this ActionController are
 * free to define additional initialization parameters.
 * <ul>
 * <li><b>options</b> - This sets the ActionController options.</li>
 * </ul>
 *
 * @author	Arnold Cano
 * @version	$Id: ActionController.php,v 1.1.1.1 2002/11/19 16:46:54 arcano Exp $
 */
class ActionController extends Object
{
	/**
	 * @var	array
	 */
	var $_options;
	/**
	 * @var	HashMap
	 */
	var $_actionMappings;
	/**
	 * @var	HashMap
	 */
	var $_actionForms;
	/**
	 * @var	HashMap
	 */
	var $_actions;

	/**
	 * Create a ActionController specifying the options.
	 *
	 * @access	public
	 * @param	array	$options
	 */
	function ActionController($options)
	{
		if (!is_array($options)) {
			trigger_error('Invalid options file');
			return;
		}
		$this->_options = $options;
		//initialize cache
		$this->_actionMappings = new HashMap();
		$this->_actionForms = new HashMap();
		$this->_actions = new HashMap();
	}
	/**
	 * Process the request.
	 *
	 * @access	public
	 * @param	array	$mappings
	 * @param	array	$request
	 */
	function process($mappings, $request)
	{
		if (!is_array($mappings)) {
			trigger_error('Invalid mappings file');
			return;
		}
		if (!is_array($request)) {
			trigger_error('Invalid request');
			return;
		}
		error_reporting($this->_options[_ERROR_REPORTING]);
		$actionMapping = $this->_processMapping($mappings, $request);
		$actionForm = $this->_processForm($mappings, $request);
		if (is_object($actionForm)) {
			$_SESSION[_FORM] = $actionForm;
			if ($actionMapping->getValidate()) {
				if (!$this->_processValidate($actionMapping, $actionForm)) {
					return;
				}
			}
		}
		$actionForward = $this->_processAction($actionMapping, $actionForm);
		if (is_object($actionForward)) {
			$this->_processForward($actionForward);
		}
	}
	/**
	 * Identify and return an appropriate ActionMapping.
	 *
	 * @access	private
	 * @param	array			$mappings
	 * @param	array			$request
	 * @return	ActionMapping
	 */
	function _processMapping($mappings, $request)
	{
		$name = $request[_ACTION];
		$mapping = $mappings[_ACTION_MAPPINGS][$name];
		$actionMapping = $this->_actionMappings->get($name);
		if (!is_object($actionMapping)) {
			$actionMapping = new ActionMapping($name, $mapping);
			if ($this->_options[_CACHE]) {
				$this->_actionMappings->put($name, $actionMapping);
			}
		}
		return $actionMapping;
	}
	/**
	 * Identify and optionally return an appropriate populated ActionForm.
	 *
	 * @access	private
	 * @param	array			$mappings
	 * @param	array			$request
	 * @return	ActionForm
	 */
	function _processForm($mappings, $request)
	{
		$name = $request[_ACTION];
		$mapping = $mappings[_ACTION_MAPPINGS][$name];
		//verify that a form has been mapped
		if (isset($mapping[_NAME])) {
			$form = $mappings[_ACTION_FORMS][$mapping[_NAME]];
			$type = $form[_TYPE];
			$actionForm = $this->_actionForms->get($name);
			if (!is_object($actionForm)) {
				if (!class_exists($type)) {
					trigger_error("Invalid ActionForm '$name' type '$type'");
					return;
				}
				$actionForm = new $type();
				if ($this->_options[_CACHE]) {
					$this->_actionForms->put($name, $actionForm);
				}
			}
			//reset all properties to their default state
			$actionForm->reset();
			//populate the properties from the request
			$actionForm->putAll($request);
		}
		return $actionForm;
	}
	/**
	 * Call the validate() method of the specified ActionForm.
	 *
	 * @access	private
	 * @param	ActionMapping	$actionMapping
	 * @param	ActionForm		$actionForm
	 * @return	boolean
	 */
	function _processValidate($actionMapping, $actionForm)
	{
		$isValid = TRUE;
		set_error_handler($this->_options[_ERROR_HANDLER]);
		if (!$actionForm->validate()) {
			$input = $actionMapping->getInput();
			//forward errors back to view
			header("Location: $input&".SID);
			$isValid = FALSE;
		}
		restore_error_handler();
		return $isValid;
	}
	/**
	 * Ask the specified Action instance to handle this request.
	 *
	 * @access	private
	 * @param	ActionMapping	$actionMapping
	 * @param	ActionForm		$actionForm
	 * @return	ActionForward
	 */
	function _processAction($actionMapping, $actionForm)
	{
		$name = $actionMapping->getName();
		$type = $actionMapping->getType();
		$action = $this->_actions->get($name);
		if (!is_object($action)) {
			if (!class_exists($type)) {
				trigger_error("Invalid Action '$name' type '$type'");
				return;
			}
			$action = new $type();
			if ($this->_options[_CACHE]) {
				$this->_actions->put($name, $action);
			}
		}
		set_error_handler($this->_options[_ERROR_HANDLER]);
		$actionForward = $action->perform($actionMapping, $actionForm);
		restore_error_handler();
		return $actionForward;
	}
	/**
	 * Forward to the specified destination.
	 *
	 * @access	private
	 * @param	ActionForward	$actionForward
	 */
	function _processForward($actionForward)
	{
		$redirect = $actionForward->getRedirect();
		$path = $actionForward->getPath();
		if (!$redirect) {
			header("Location: $path&".SID);
		} else {
			$_SESSION = array();
			session_destroy();
			header("Location: $path");
		}
	}
}
?>
