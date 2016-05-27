<?php
/**
* @package Mambo
* @subpackage Languages
* @copyright  Refer to copyright.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Mambo Foundation Inc see README.php
*/

class languageView extends aliroView {
	
    function render ($renderer) {
        $renderer->addvar('content', $renderer->fetch('languages.tpl.php'));
        $renderer->display('form.tpl.php');
    }
}