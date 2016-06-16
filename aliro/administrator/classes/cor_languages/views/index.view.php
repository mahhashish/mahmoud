<?php
/**
* @package Mambo
* @subpackage Languages
* @copyright  Refer to copyright.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Mambo Foundation Inc see README.php
*/

class indexView extends aliroView {
	
    function render ($renderer) {
        $rows           = array();
        $languageDir    = mamboCore::get('mosConfig_absolute_path')."/language/";
        $xmlFilesInDir  = mosReadDirectory($languageDir,'.xml$');
        $rowid = 0;

        foreach($xmlFilesInDir as $xmlfile) {
            // Read the file to see if it's a valid template XML file
            $parser =& new mosXMLDescription($languageDir.$xmlfile);
            if ($parser->getType() != 'language') continue;
            $row                = new StdClass();
            $row->id            = $rowid;
            $row->language      = substr($xmlfile,0,-4);
            $row->name          = $parser->getName('language');
            $row->creationdate  = $parser->getCreationDate('language');
            $row->author        = $parser->getAuthor('language');
            $row->copyright     = $parser->getCopyright('language');
            $row->authorEmail   = $parser->getAuthorEmail('language');
            $row->authorUrl     = $parser->getAuthorUrl('language');
            $row->version       = $parser->getVersion('language');
            $row->checked_out = 0;
            $row->mosname = strtolower( str_replace( " ", "_", $row->name ) );
            $row->published = (mamboCore::get('mosConfig_locale') == $row->language) ? 1 : 0;
            $rows[] = $row;
            $rowid++;


        }

        $renderer->addvar('rows', $rows);
        $renderer->addvar('content', $renderer->fetch('table.tpl.php'));
        $renderer->display('form.tpl.php');
    }
}