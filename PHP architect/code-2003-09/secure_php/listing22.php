<?php

//xsssearchpatched.php
$q=str_replace('<', '&lt;', $_GET['query']);
$q=str_replace('>', '&gt;', $_GET['query']);
echo "You searched for " . $q;

?>
