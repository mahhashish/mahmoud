<?php
require 'XML/Transformer.php';
require 'XML/Transformer/Namespace.php';

class My extends XML_Transformer_Namespace
{
    function start_example($attrs)
    {
        return 'Just a ';
    }

    function end_example($cdata)
    {
        return $cdata . ' example';
    }
}

$xmlFile = implode('', file('namespaces.xml'));
$myTransformer = new XML_Transformer();
$myTransformer->overloadNamespace('my', new My, false);
echo $myTransformer->transform($xmlFile);
?>
