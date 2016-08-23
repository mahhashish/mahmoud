<?php

class ComplexBreadcrumb
{
    var $_separator = ' > ';
    var $_current_state;

    // constructor
    function ComplexBreadcrumb($separator=null)
    {
        if (isset($separator))
        {       
            $this->set_separator($separator);
        }       
    }

    function set_separator($separator)
    {
        $this->separator = $separator;
    }

    // set a state/crumb
    // - 'parent' is the parent label (allows chaining)
    // - 'label' is the state/crumb label you are adding
    // - 'vars' is an array of name-value pairs (usually from $_GET) that
    //   identify the state/crumb
    function set_state($label, $parent='home', $vars=array())
    {
        $_SESSION['_crumbs'][$label] = array(   'label'  => $label,
                                                'path'   => $_SERVER['PHP_SELF'],
                                                'vars'   => $vars,
                                                'parent' => $parent,
                                               );
        $this->current_state = $label;
    }

    function get_output()
    {
        // no current crumb means no crumb display on the page
        if (! isset($this->current_state))
        {
            return '';
        }
        
        $crumbs = array();
        
        // don't make a link from the current state
        $crumbs[] = $this->current_state;

        // get the current state's parent and set the new current
        $parent = $_SESSION['_crumbs'][$this->current_state]['parent'];
        $current = $_SESSION['_crumbs'][$parent];

        // do all parents
        do
        {
            foreach ($current['vars'] as $name=>$value)
            {
                $values[] =  urlencode($name) . '=' . urlencode($value);
            }
            $crumbs[] = "<a href='{$current['path']}?" . join('&', $values) . "'>"
                        . "{$current['label']}</a>";
            // get the current state's parent and set the new current
            $parent = $_SESSION['_crumbs'][$this->current_state]['parent'];
            $current = $_SESSION['_crumbs'][$parent];
        }
        while ($current['label'] != 'home'))

        // reverse the array (we built it in reverse)
        krsort($crumbs);

        return 'back to ' . join(" {$this->separator} ", $crumbs);
    }
}

?>

<?php
    
// simple example of using ComplexBreadcrumb class
$cbc = &new ComplexBreadcrumb(' / ');

// get product name
$category = $_GET['category'];

$cbc->set_state('football categories', 
                'product categories', 
                array('category'=>$_GET['category']));

print $cbc->get_output();

?>
