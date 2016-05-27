<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more
 * information on this, please see the index.php file or visit
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
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
			if (_ALIRO_IS_ADMIN) self::$instance = aliroAdminRequest::getInstance();
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
	    return aliroBase::trace();
	}

}

class mosRequest extends aliroRequest {

}