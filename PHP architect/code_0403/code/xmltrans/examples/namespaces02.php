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
$myTransformer = new XML_Transformer(array('overloadedNamespaces' => array('my' => new My)));
echo $myTransformer->transform($xmlFile);
?>
