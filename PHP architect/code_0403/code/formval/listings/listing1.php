<?php

    // start action code
    if (isset($_REQUEST['foo']))
    {
        // peform validation and processing
    }

    // start screen code

?> 

<form action="<?php print $_SERVER['PHP_SELF']; ?>">
    <input type="text" name="foo" value="" />
</form>
