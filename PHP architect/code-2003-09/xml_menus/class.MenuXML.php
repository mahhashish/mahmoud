<?php

class MenuXML 
{
	var $data;
    var $menu;
    var $heading;
    var $state;

	// Constructor
    function MenuXML($data) 
    {
		if ( file_exists($data) ) 
			$this->data = implode('', file($data));
		else 
			$this->data = $data;
        $this->loadXML();
    }

	// Load the XML file
    function loadXML() 
    {
        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $this->data, $d_ar, $i_ar);
        xml_parser_free($xml_parser);

        $i = 0;
        foreach ( $d_ar as $element ) {
            switch ( $tag = strtolower($element['tag']) ) {
                case 'heading':
                    $this->heading = $element['value'];
                break;
                case 'state':
                    $this->state = $element['value'];
                break;
                case 'section':
                    if ( isset($element['attributes']['ID']) ) 
                        $key = $element['attributes']['ID'];
                    $i++;
                break;
                case 'name':
                    $this->menu[$key][$i]['name'] = $element['value'];
                break;
                case 'href':
                    $this->menu[$key][$i]['href'] = str_replace('##', '&', $element['value']);
                break;
                case 'image':
                    $this->menu[$key][$i]['image'] = $element['value'];
                break;
                case 'target':
                    $this->menu[$key][$i]['target'] = $element['value'];
                break;
            }
        }
    }
}

?>
