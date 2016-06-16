<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Aliro is open source software, free to use, and licensed under GPL.
 * You can find the full licence at http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * The author freely draws attention to the fact that Aliro derives from Mambo,
 * software that is controlled by the Mambo Foundation.  However, this section
 * of code is totally new.  If it should contain any fragments that are similar
 * to Mambo, please bear in mind (1) there are only so many ways to do things
 * and (2) the author of Aliro is also the author and copyright owner for large
 * parts of Mambo 4.6.
 *
 * Tribute should be paid to all the developers who took Mambo to the stage
 * it had reached at the time Aliro was created.  It is a feature rich system
 * that contains a good deal of innovation.
 *
 * Your attention is also drawn to the fact that Aliro relies on other items of
 * open source software, which is very much in the spirit of open source.  Aliro
 * wishes to give credit to those items of code.  Please refer to
 * http://aliro.org/credits for details.  The credits are not included within
 * the Aliro package simply to avoid providing a marker that allows hackers to
 * identify the system.
 *
 * Copyright in this code is strictly reserved by its author, Martin Brampton.
 * If it seems appropriate, the copyright will be vested in the Aliro Organisation
 * at a suitable time.
 *
 * Copyright (c) 2007 Martin Brampton
 *
 * http://aliro.org
 *
 * counterpoint@aliro.org
 *
 * aliroAbstractRequest is the abstract class that contains the central control logic for
 * Aliro.  There are two classes that extend aliroAbstractRequest, aliroUserRequest and
 * aliroAdminRequest.  They are instantiated very quickly by whichever index.php
 * is selected.  The admin side index.php will invoke aliroAdminRequest and the
 * user side (the root of the site) index.php will invoke aliroUserRequest.
 *
 * mosRequest is kept to provide backwards compatibility.
 *
 * aliroRequest is a factory class that provides the appropriate kind of
 * aliroAbstractRequest object, and also houses a very few static methods.
 *
 */

function T_($message) {
    return defined('_ALIRO_LANGUAGE') ? PHPGettext::getInstance()->gettext($message) : $message;
}
function Tn_($msg1, $msg2, $count) {
    return defined('_ALIRO_LANGUAGE') ? PHPGettext::getInstance()->ngettext($msg1, $msg2, $count) : $msg1;
}
function Td_($domain, $message) {
    return defined('_ALIRO_LANGUAGE') ? PHPGettext::getInstance()->dgettext($domain, $message) : $message;
}
function Tdn_($domain, $msg1, $msg2, $count) {
    return defined('_ALIRO_LANGUAGE') ? PHPGettext::getInstance()->dngettext($domain, $msg1, $msg2, $count) : $msg1;
}


class aliroRequest {
	private static $instance = null;

	public static function getInstance () {
		if (null == self::$instance) {
			$info = criticalInfo::getInstance();
			if ($info->isAdmin) self::$instance = aliroAdminRequest::getInstance();
			else self::$instance = aliroUserRequest::getInstance();
		}
		return self::$instance;
	}

	public static function mosMakeHtmlSafe( &$mixed, $quote_style=ENT_QUOTES, $exclude_keys='' ) {
		if (is_object($mixed)) foreach (get_object_vars( $mixed ) as $k => $v) {
			if (is_array($v) OR is_object($v) OR $v == NULL OR substr($k, 1, 1) == '_' OR (is_string($exclude_keys) AND $k == $exclude_keys) OR (is_array( $exclude_keys ) AND in_array( $k, $exclude_keys )));
			else $mixed->$k = htmlspecialchars($v, $quote_style);
		}
	}

	public static function trace ($error=true) {
	    static $counter = 0;
		$html = '';
		foreach(debug_backtrace() as $back) {
		    if (isset($back['file']) AND $back['file']) {
			    $html .= '<br />'.$back['file'].':'.$back['line'];
			}
		}
		if ($error) $counter++;
		if (1000 < $counter) {
		    echo $html;
		    die (T_('Program killed - Probably looping'));
        }
		return $html;
	}

}

class mosRequest extends aliroRequest {

}