<?php
require 'XML/Transformer.php';
require 'XML/Transformer/Namespace.php';

class Date extends XML_Transformer_Namespace
{
    function end_today($cdata)
    {
        return strftime('%c');
    }
}

class Utils extends XML_Transformer_Namespace
{
    function start_ucase($attrs)
    {
    }

    function end_ucase($cdata)
    {
        return strtoupper($cdata);
    }
}

$xmlFile = implode('', file('namespaces03.xml'));
$myTransformer = new XML_Transformer();
$myTransformer->overloadNamespace('date', new Date);
$myTransformer->overloadNamespace('utils', new Utils);
echo $myTransformer->transform($xmlFile);
?>
