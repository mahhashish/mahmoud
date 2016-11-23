<?php

require_once 'FormElement.php';

class FormTextbox extends FormElement
{  
    // allows checking types:
    //  * 'regex' - pattern matching
    //  * 'func' - user-defined function
    //  * '' - don't care

    // constructor
    function FormTextbox($name)
    {
        // call parent constructor
        $this->FormElement('text', $name); 
    }

    // check pattern
    function is_pattern_ok($index)
    {
        if (preg_match($this->check_extras[$index], $this->value))
        {       
            return true;
        }       
        return false;
    }
    
    // render the element and return it
    function get_element()
    {
        $element = "<input  type=\"text\" "
                         . "name=\"{$this->name}\" "
                         . "value=\"{$this->value}\" ";

        // add extra attributes                 
        foreach ($this->attributes as $attribute=>$value)
        {       
            $element .= "{$attribute}=\"{$value}\" ";
        }       

        $element .= ">"; 
        return $this->apply_format($element);
    }
    
    // check value
    function check($index)
    {
        switch ($this->check_methods[$index])
        {
            case '':
                return true;
                break;
            case 'regex':
                return $this->is_pattern_ok($index);
                break;
            case 'func':
                return $this->_run_check_function($index,
                                                 $this->type,
                                                 $this->name,
                                                 $this->value);
                break;
            default:
                // error
                die('unsupported checking method');
        }
    }
}

?>
