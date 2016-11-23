<?php
require 'XML/Transformer.php';
require 'XML/Transformer/Namespace.php';

class HelloWorld extends XML_Transformer_Namespace
{
    var $_name;
    function start_hello($attrs)
    {
        if (!empty($attrs['name'])) {
            $this->_name = $attrs['name'];
        }
    }

    function end_hello($cdata)
    {
        return 'Hello ' . $this->_name;;
    }
}
$xmlFile = implode('', file('helloWorld.xml'));

$myTransformer = new XML_Transformer();
$myTransformer->overloadNamespace('&MAIN', new HelloWorld, false);
echo $myTransformer->transform($xmlFile);
?>
