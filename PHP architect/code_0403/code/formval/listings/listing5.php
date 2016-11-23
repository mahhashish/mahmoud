<?php

require_once 'FormElement.php';

class FormSubmit extends FormElement
{  
    // allows checking types:
    //  * '' - don't care

    // button label
    var $desc;

    // constructor
    function FormSubmit($name, $desc='Submit')
    {
        // call parent constructor
        $this->FormElement('submit', $name); 
        $this->desc = $desc;
    }

    // render the element and return it
    function get_element()
    {
        $element = "<input  type=\"submit\" "
                         . "name=\"{$this->name}\" "
                         . "value=\"{$this->desc}\" ";

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
            default:
                // error
                die('unsupported checking method');
        }     
    }
}

?>
