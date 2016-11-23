<?php
 
function smarty_modifier_substr($string, $start, $length = 'dummy')
{
    if ($length==='dummy') return substr($string, $start);
    return substr($string, $start, $length);
}

?> 