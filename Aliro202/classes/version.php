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
 * version is a very simple singleton class used to hold version information.
 *
 */

/** Version information */
class version {
	private static $instance = __CLASS__;

	/** @public string Product */
	public $PRODUCT = 'Aliro';
	/** @public int Main Release Level */
	public $RELEASE = '2.0';
	/** @public string Development Status */
	public $DEV_STATUS = 'Release';
	/** @public int Sub Release Level */
	public $DEV_LEVEL = '0';
	/** @public string Codename */
	public $CODENAME = 'Scarborough';
	/** @public string Date */
	public $RELDATE = '18 September 2012';
	/** @public string Time */
	public $RELTIME = '01:00';
	/** @public string Timezone */
	public $RELTZ = 'GMT';
	/** @public string Copyright Text */
	public $COPYRIGHT = 'Copyright 2006-12 Aliro Software Limited.  All rights reserved.';
	/** @public string URL */
	public $URL = '<a href="http://www.aliro.org">%s</a> is Free Software released under the GNU GPL/LGPL 2.0 License.';

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
	}

	function footer ($element='div') {
		return "<$element>".sprintf($this->URL,$this->PRODUCT.' '.$this->RELEASE.'/'.$this->DEV_STATUS.'/'.$this->DEV_LEVEL)."</$element>";
	}
}