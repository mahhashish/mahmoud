<?php

// For backwards compatibility - deprecated
// Instead, add indextype=2 to the URI

$_REQUEST['indextype'] = 2;
require('index.php');