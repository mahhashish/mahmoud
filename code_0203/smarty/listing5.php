<?php

function smarty_outputfilter_literal_cleanup($source, &$smarty){
    $result=&$source;
    $result=preg_replace('~<!--({literal})? -->~iU', '', $result);
    return $result;
}

?>