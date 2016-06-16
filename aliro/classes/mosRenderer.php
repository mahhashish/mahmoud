<?php

/**
 * A controller class modelled on the front controller and
 * application controller design patterns.
 * The Command and View classess are an integral part of this pattern.
 *
 */
class mosController
{
    /**
     * the name of the $_REQUEST variable that contains the command name
     *
     * @var string
     */
    private $commandname;
    /**
     * the current command object
     *
     * @var object
     */
    private $command;
    /**
     * the current view object. This property is only used for debugging purposes.
     *
     * @var object
     */
    private $view;
    /**
     * a reference to the request object
     *
     * @var object
     */
    private $request;
    /**
     * a reference to a renderer object to pass on to the view
     *
     * @var unknown_type
     */
    private $renderer;

    /**
     * Constructor.
     *
     * @param object $request - A reference to a request object
     * @param object $renderer - A reference to a renderer object
     * @param string $commandname - the name of the $_REQUEST variable containing the command name
     * @return Controller
     */
    function __construct (mosRequest &$request, mosRenderer &$renderer, $commandname='command')
    {
        $this->request     =& $request;
        $this->renderer    =& $renderer;
        $this->commandname =  $commandname;
    }

    /**
     * Forwards the request to the correct command from the request or the default command,
     * Then forwards to a view.
     *
     */
    function run()
    {
        $command = $this->request->getParam($_REQUEST, $this->commandname, 'default');
        $this->forward($command);
        // get the view from the command
        $this->setView($this->command->getView());
        $this->view->render($this->request, $this->renderer);
    }

    /**
     * sets the current command object and calls its execute method
     *
     * @param unknown_type $command
     */
    function forward($command)
    {
        $this->setCommand($command);
        $this->command->execute($this, $this->request);
    }

    /**
     * Instantiates the command class and sets the current command property.
     * The command classes must extend the Command class and must follow a naming convention.
     * eg. the 'addUser' command class must be named 'addUserCommand'
     *
     * @param string $command
     * @return bool - triggers an error if the command class isn't found.
     */
    function setCommand($command)
    {
        $commandclass = $command.'Command';
        // instantiate the command class
        if (class_exists($commandclass)) {
            $this->command = new $commandclass();
        } else {
            return trigger_error("Command class '$commandclass' not found.", E_USER_ERROR);
        }
    }
    /**
     * Instantiates the view class and sets the current view property
     * The view classes must extend the View class and must follow a naming convention.
     * The view is also associated to the command that sets it.
     * eg. the form view for the 'addUser' command class must be named 'form_addUserView'
     *
     * @param string $viewname - the view name st by the command
     * @return bool - triggers an error if the view class isn't found.
     */
    function setView($viewname)
    {
        $commandname = substr(get_class($this->command), 0, -7);
        $viewclass = $viewname.'View';
        if (class_exists($viewclass)) {
            $this->view = new $viewclass();
        } else {
            return trigger_error("View class '$viewclass' not found.", E_USER_ERROR);
        }
    }
    /**
     * Helper function to redirect the user request.
     * useful for cases where we don't want the user to resend a form
     *
     * @param unknown_type $url
     */
    function redirect($url)
    {
        if (headers_sent()) {
            echo "<script>document.location.href='$url';</script>";
        } else {
            if (ob_get_contents()) while (@ob_end_clean()); // clear output buffer if one exists
            header( "Location: $url" );
        }
        exit;
    }

}

/**
 * The base command class. This could probably be an interface in PHP5
 *
 */
class mosCommand
{
    /**
     * The name of the view to be redrected to
     *
     * @var unknown_type
     */
    private $viewname;

    /**
     * constructor does nothing
     *
     * @return Command
     */
    function __construct(){}

    /**
     * executes the command
     *
     * @param object $controller
     * @param object $request
     * @return bool - triggers an error if called directly
     */
    function execute(mosController &$controller, mosRequest &$request)
    {
        return trigger_error('Command::execute() must be overridden.', E_USER_ERROR);
    }
    /**
     * Set the view to be redirected to if any
     *
     * @param string $viewname - the name of the view
     */
    function setView($viewname)
    {
        $this->viewname = $viewname;
    }
    /**
     * return the viewname. This is called by the controller class
     *
     * @return string
     */
    function getView()
    {
        return $this->viewname;
    }
}

/**
 * Base class for views. This could probably be an interface in PHP5
 *
 */
class mosView
{

    /**
     * Constructor. Does nothing.
     *
     * @return View
     */
    function __construct(){}
    /**
     * renders the view
     *
     * @param object $request - a reference to a Request object
     * @param unknown_type $renderer - a reference to a Renderer object
     * @return bool - triggers an error if called directly
     */
    function render(mosRequest &$request, mosRenderer &$renderer)
    {
        return trigger_error('View::render() must be overridden');
    }
}


class mosRenderer
{

    private $dir;
    private $vars = array();
    private $engine = 'php';
    private $template = '';

    function __construct(){}

    static function &getInstance($type = 'php') {
        static $renderer;
        if (is_null($renderer[$type])) {
            if ($type == 'php') {
                $renderer[$type] = new mosRenderer();
            } else {
                $classname = $type . 'Renderer';
                if (class_exists($classname))
                $renderer[$type] = new $classname();
            }
        }
        return $renderer[$type];
    }

    function display($template, $return = false){
        if ($template == NULL){
            return trigger_error('A template has not been specified', E_USER_ERROR);
        }
        $this->template = $this->dir . $template;

        if (is_readable($this->template)) {
            extract($this->getvars());
            if ($return) {
                ob_start();
                include_once($this->template);
                $ret = ob_get_contents();
                ob_end_clean();
                return $ret;
            } else {
                include_once($this->template);
            }
        } else {
            return trigger_error("Template file $template does not exist or is not readable", E_USER_ERROR);
        }
        return false;
    }

    function fetch($template){
        return $this->display($template, true);
    }

    function &getengine(){
        return $this->engine;
    }

    function addvar($key, $value){
        $this->vars[$key] = $value;
    }

    function addbyref ($key, &$value) {
        $this->vars[$key] = $value;
    }

    function getvars($name = false){
        return (isset($this->vars[$name])) ? $this->vars[$name] : $this->vars;
    }

    function setdir($dir){
        $this->dir = (substr($dir, -1) == DIRECTORY_SEPARATOR) ? $dir : $dir.DIRECTORY_SEPARATOR;
    }

    function getdir(){
        return $this->dir;
    }

    function settemplate($template){
        $this->template = $template;
    }
}