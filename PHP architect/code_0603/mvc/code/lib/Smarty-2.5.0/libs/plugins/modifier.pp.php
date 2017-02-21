<?php
/*
 * Smarty plugin
 *
-------------------------------------------------------------
 * File:     modifier.pp.php
 * Type:     modifier
 * Name:     pp
 * Version:  1.0
 * Date:     May 1st, 2003
 * Purpose:  assume the input string is a number, increment and return result
 * Install:  Drop into the plugin directory.
 * Author:   Jason E. Sweat <jsweat_php@yahoo.com>
 *
-------------------------------------------------------------
 */
function smarty_modifier_pp($string)
{
    return ++$string;
}

/* vim: set expandtab: */

?>
