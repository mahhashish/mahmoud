<?php

// form elements
require_once 'FormTextbox.php';
require_once 'FormHidden.php';
require_once 'FormPassword.php';
require_once 'FormCheckbox.php';
require_once 'FormRadio.php';
require_once 'FormFile.php';
require_once 'FormSelect.php';
require_once 'FormTextarea.php';
require_once 'FormButton.php';
require_once 'FormSubmit.php';
require_once 'FormReset.php';

class Form
{
    // elements of the form
    var $elements = array();
    
    // invalid form messages
    var $messages = array();

    // used as a hidden field, so we know which form on a
    // page is being submitted
    var $identifier = null; 
    
    // attributes of the form
    var $name = 'form1';
    var $method = 'get';
    var $action = '';
    var $target = '_self';
    var $attributes = array();

    // constructor
    function Form($identifier)
    {
        $this->identifier = $identifier;
        $this->action = $_SERVER['PHP_SELF'];
    }

    // add an element to the form
    function add($element)
    {
        $this->elements[] = &$element;
    }

    // set the name of the form
    function set_name($name)
    {
        $this->name = $name;
    }
    
    // set the method of the form (get, post)
    function set_method($method)
    {
        $this->method = $method;
    }

    // set the action associated with the form (ie. where to submit to)
    function set_action($action)
    {
        $this->action= $action;
    }

    // set the target window of the form (ie. what window to display the result)
    function set_target($target)
    {
        $this->target = $target;
    }

    // specify other attributes for the tag (ie. class, enctype, etc)
    function set_attribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    // returns a true/false flag as to whether the elements of the form
    // were happy with their submitted values
    //
    function is_form_valid()
    {
        $valid = true;

        // form has been submitted
        if (! $this->_is_form_submitted())
        {
            return false;
        }

        foreach ($this->elements as $index=>$element)
        {
            // each element takes what it needs
            $this->elements[$index]->set_value();

            // if element doesn't check out, valid is false
            list ($valid_element, $msg) = $this->elements[$index]->validate();
            if ($valid_element == false)
            {
                $valid = false;
                $this->messages[] = $msg;
            }
        }
        return $valid;
    }

    // returns a true/false flag as to whether the form was submitted or not
    function _is_form_submitted()
    {
        if ((isset($_REQUEST['_form'])) && ($_REQUEST['_form'] == $this->identifier))
        {
            return true;
        }
        return false;
    }

    // get any error messages
    function get_messages()
    {
        return $this->messages;
    }

    function _get_form_tag()
    {
        $form_tag = "<form name=\"{$this->name}\" "
                        . "method=\"{$this->method}\" "
                        . "action=\"{$this->action}\" "
                        . "target=\"{$this->target}\" ";

        // add extra attributes                 
        foreach ($this->attributes as $attribute=>$value)
        {
            $form_tag .= "{$attribute}=\"{$value}\" ";
        }

        $form_tag .= ">";
        return $form_tag;
    }

    function _get_identifier_tag()
    {
        $ident_tag = "<input type=\"hidden\" "
                          . "name=\"_form\" "
                          . "value=\"{$this->identifier}\">";
        return $ident_tag;
    }

    // return the array of tags and elements making up the form
    function get_form()
    {
        $form['form_start'] = $this->_get_form_tag() . $this->_get_identifier_tag();
        foreach($this->elements as $element)
        {
            // get the value (if any) from the previous submission
            //  * to allow for form validation, etc.
            $element->set_value();

            $form[$element->get_name()] = $element->get_element();
        }
        $form['form_end'] = '</form>';

        // save form in session
        $_SESSION['_forms'][$this->identifier] = &$this;

        return $form;
    }

    // gets the value from one of the defined elements
    function get_form_value($name)
    {
        foreach($this->elements as $element)
        {
            if ($name == $element->get_name())
            {
                return $element->get_value();
            }
        }
    }
}

?>
