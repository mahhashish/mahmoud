<?php

    setcookie('monster', "I love cookies", time()+5);

    foreach ($_FILES as $key=>$value)
    {
        print "- You uploaded a file called {$_FILES[$key]['name']}"
				. " in the {$key} variable.  It was {$_FILES[$key]['size']}"
				. " bytes in length.\n";
    }

    foreach ($_GET as $key=>$value)
    {
        $output = print_r($value, true);
        print "- GET variable submitted: {$key}={$output}\n";
    }

    foreach ($_POST as $key=>$value)
    {
        $output = print_r($value, true);
        print "- POST variable submitted: {$key}={$output}\n";
    }

    foreach ($_COOKIE as $key=>$value)
    {
        $output = print_r($value, true);
        print "- COOKIE variable retrieved: {$key}={$output}\n";
    }

?>
