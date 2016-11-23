<?php

// a simple breadcrumb class that works off of the directory
// names in the current script
class SimpleBreadcrumb
{
    var $_separator = ' > ';
    var $_label_map = array();

    function SimpleBreadcrumb($label_map=null, $separator=null)
    {
        if (isset($separator))
        {
            $this->set_separator($separator);
        }
        if (isset($label_map))
        {
            $this->set_label_map($label_map);
        }
    }
    
    function set_separator($separator)
    {
        $this->_separator = $separator;
    }
    
    function set_label_map($label_map)
    {
        $this->_label_map = $label_map;
    }

    function get_output()
    {	
	    // get script path, strip any trailing slashes, and 
        // split into constituents
	    // ex. /images/icons/gif
        $dir = $_SERVER['PHP_SELF'];
        $current = basename(dirname($dir));
	    $dirs = split('/', $dir);
    
	    // base is built as we go through the dirs
	    $base = '/';
        $crumbs = array();
	    foreach($dirs as $dir)
	    {
		    $base .= "{$dir}/";
		    if (isset($this->_crumb_label[$dir]))
		    {
                if ($dir == $current)
                {
			        $crumbs[] = $this->_crumb_label[$dir];
                }
                else
                {
			        $crumbs[] = "<a href='{$base}'>{$this->_crumb_label[$dir]}</a>";
                }
		    }
	    }

        return join($this->_separator, $crumbs);
    }
}

?>

<?php

// simple example of using SimpleBreadcrumb class
$crumb_label_map = $array(  'images'  => 'Images', 
                            'buttons' => 'Buttons', 
                            'clipart' => 'Clip Art', 	
                            'icons'   => 'Icons', 
                            'photos'  => 'Photographs',
                            'jpg'     => 'JPEG',
                            'gif'     => 'GIF',
                            'png'     => 'PNG',
                            'code'    => 'Code',
                            // etc...
                            );

$sbc = &new SimpleBreadcrumb($crumb_label_map);

print $sbc->get_output();
			
?>

