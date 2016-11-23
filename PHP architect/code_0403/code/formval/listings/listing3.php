<?php

class FormElement
{
    // text, radio, etc
    var $type = '';
    
    // the name of the element
    var $name = '';
    
    // other attributes like 'size', 'class', etc.
    var $attributes = array();

    // checking method (differnt for different elements)
    // defaults to none
    var $check_methods = array();
    
    // points to user-defined function for checking data in the form
    var $check_extras = array();

    // value of form element
    var $value = null; 

    // invalid value message
    var $messages = array(); 

    // printf format for the output of the element
    var $format = '%s'; 

    // allow getting _REQUEST value for display
    var $use_request = true; 

    // base constructor
    function FormElement($type, $name)
    {
        $this->type = $type;
        $this->name = $name;
    }
    
    // set extra attributes
    function set_attribute($name, $value) 
    {
        $this->attributes[$name] = $value; 
    }

    // set format for output of element
    function set_format($format)
    {
        $this->format = $format;
    }

    // get format string for the element
    function get_format()
    {
        return $this->format;
    }

    // use submitted value?
    function set_use_request($use)
    {
        $this->use_request = $use;
    }

    // get the formatted representation
    function apply_format($element)
    {
        return sprintf($this->format, $element);
    }

    // set checking method
    //   examples:
    //    * 'func' - use user-defined function
    //    * 'regex' - use pattern matching using 'format'
    //    * '' - none (don't care)
    function add_check_method($method, $message, $extra=null, $file=null)
    {
        $index = count($this->check_methods);
        $this->check_methods[$index] = $method;
        $this->messages[$index] = $message;
        $this->check_extras[$index] = $extra;
        $this->check_files[$index] = $file;
    }

    // formulates and executes the call to the check function
    function _run_check_function($index, $type, $name, $value)
    {
        return call_user_func($this->check_extras[$index], $type, $name, $value);
    }

    // set the value for the element
    function set_value($value=null)
    {
        if (isset($value))
        {
            $this->value = $value;
        }
        else
        {
            // if _REQUEST value is allowed
            if ($this->use_request)
            {
                if (isset($_REQUEST[$this->name]))
                {
                    $this->value = $_REQUEST[$this->name];
                }
                // if no request var set, leave as is
            }
        }
    }

    // return the value of the form element
    function get_value()
    {
        return $this->value;
    }

    // return the name of the form element
    function get_name()
    {
        return $this->name;
    }

    // render the element and return it
    function get_element()
    {
        // Abstract method
        die('This method must be implemented in a subclass.');
    }

    // runs through the element's check methods and outputs invalid messages
    // if necessary
    function validate()
    {
        foreach($this->check_methods as $index=>$method)
        {
            if ('func' == $method)
            {
                if (! empty($this->check_files[$index]))
                {
                    require_once $this->check_files[$index];
                }
            }

            if (! $this->check($index))
            {
                return array(false, $this->messages[$index]);
            }
        }
        return array(true, '');
    }           
                
    function check($method)
    {           
        // Abstract method
        die('This method must be implemented in a subclass.');
    }   
}   

?>  
