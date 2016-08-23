<?php

function smarty_prefilter_dreamweaver_template(&$source, &$smarty){
    $pattern[]='~<!-- TemplateBeginEditable name="(.*)" -->.*<!-- TemplateEndEditable -->~Us';
    $pattern[]='~<!-- TemplateBeginIf cond="(.*)" -->(.*)<!-- TemplateEndIf -->~Us';
    $pattern[]='~<!-- TemplateBeginRepeat name="(.*)" -->(.*)<!-- TemplateEndRepeat -->~Us';
    $replace[]='{$$1}';
    $replace[]='{if $$1}$2{/if}';
    $replace[]='{foreach from=$$1 item=$1}$2{/foreach}';
    return preg_replace($pattern, $replace, $source);
}

?>