<?php
/*
 * Smarty plugin
 *
-------------------------------------------------------------
 * File:     modifier.mm.php
 * Type:     modifier
 * Name:     mm
 * Version:  1.0
 * Date:     May 1st, 2003
 * Purpose:  assume the input string is a number, decrement and return result
 * Install:  Drop into the plugin directory.
 * Author:   Jason E. Sweat <jsweat_php@yahoo.com>
 *
-------------------------------------------------------------
 */
function smarty_modifier_mm($string)
{
    return --$string;
}

/* vim: set expandtab: */

?>
