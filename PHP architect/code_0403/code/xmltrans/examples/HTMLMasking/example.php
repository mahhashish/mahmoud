<?php
require 'XML/Transformer.php';
require 'XML/Transformer/Namespace.php';

class Greeting extends XML_Transformer_Namespace
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
        return 'Hello ' . $this->_name . '.';
    }

    function start_goodbye($attrs)
    {
        if (!empty($attrs['name'])) {
            $this->_name = $attrs['name'];
        }
    }

    function end_goodbye($cdata)
    {
        return 'Goodbye ' . $this->_name . '.';
    }
}

function genXML($html, $namespaces, $tagName, $xmlHeader = '<?xml version="1.0"?>')
{
    if (!empty($html)) {
        $xml = htmlentities($html);
        foreach ($namespaces as $namespace) {
            $xml = preg_replace('/&lt;' . $namespace . ':(.*?)&gt;/e',
                                'strtr("<' . $namespace . ':$1>",
                                 array_flip(get_html_translation_table(HTML_ENTITIES)))',
                                $xml);
        }
        $xml = $xmlHeader . '<' . $tagName . '>' . $xml . '</' . $tagName . '>';
        return $xml;
    }
    return null;
}

$transformer = new XML_Transformer();
$transformer->overloadNamespace('greeting', new Greeting, false);

$usedNamespaces = array_keys($transformer->_callbackRegistry->overloadedNamespaces);
$html = implode('', file('example.html'));
$xml = genXML($html, $usedNamespaces, 'htmlMask');

$result = $transformer->transform($xml);

if (!empty($result)) {
    echo preg_replace('/<\/*' . 'htmlMask' . '>/', '', $result);
}
?>
