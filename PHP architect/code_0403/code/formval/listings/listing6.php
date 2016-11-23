<?php
 
    require_once "Form.php";

    session_start();

    // check the forms validity
    if (isset($_REQUEST['_form']))
    {
        if ($_SESSION['_forms'][$_REQUEST['_form']]->is_form_valid())
        {       
            print 'form is valid<br>';
            exit;
        }       
        else    
        {
            print 'form is not valid<br>';
            print join('<br>', 
                     $_SESSION['_forms'][$_REQUEST['_form']]->get_messages()) . "<br>\n";
        }       
    }

    // set up the form and validation tests
    $f = &new Form('foo');
    $tb = &new FormTextbox('foo_textbox');
    $tb->add_check_method('regex', 'Invalid entry by regex', '/^\d+$/');
    $tb->add_check_method('func', 'Invalid entry by func', 'foo_textbox_check');
    $sb = &new FormSubmit('foo_submit');
    $f->add($tb);
    $f->add($sb);
    $form_arr = $f->get_form();

    // output the form
    print <<<EOD
{$form_arr['form_start']}
Some text: {$form_arr['foo_textbox']}<br>
{$form_arr['foo_submit']}<br>
{$form_arr['form_end']}
EOD;

    // special checking function (checks if the value is an even number)
    function foo_textbox_check($type, $name, $value) 
    {
        if (($value % 2) == 0)
        {       
            return true;
        }       
        return false;
    }

?>
