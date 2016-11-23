<?php
require 'XML/Transformer/Driver/OutputBuffer.php';
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
        return 'Hello ' . $this->_name;
    }
}

$myTransformer = new XML_Transformer_Driver_OutputBuffer(array('overloadedNamespaces' => array('&MAIN' => new HelloWorld)));
?>
<example>
    <hello name="Bruno"/>
</example>
