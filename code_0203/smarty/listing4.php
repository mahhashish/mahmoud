<?php

function smarty_prefilter_literal_style($source, &$smarty){
    $result=&$source;
    $result=preg_replace('~<style\b(?![^>]*smarty)~iU', '<!--{literal} --><style', $result);
    $result=preg_replace('~</style>~iU', '</style><!--{literal} {/literal}-->', $result);
    return $result;
}

?>