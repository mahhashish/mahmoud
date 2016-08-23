<?php

function smarty_prefilter_literal_script($source, &$smarty){
    $result=&$source;
    $result=preg_replace('~<script\b(?![^>]*smarty)~iU', '<!--{literal} --><script', $result);
    $result=preg_replace('~</script>~iU', '</script><!--{literal} {/literal}-->', $result);
    return $result;
}

?>