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
	public $DEV_STATUS = 'External-Alpha5';
	/** @public int Sub Release Level */
	public $DEV_LEVEL = '0';
	/** @public string Codename */
	public $CODENAME = 'Christoph';
	/** @public string Date */
	public $RELDATE = '18-Nov-2007';
	/** @public string Time */
	public $RELTIME = '23:00';
	/** @public string Timezone */
	public $RELTZ = 'GMT';
	/** @public string Copyright Text */
	public $COPYRIGHT = 'Copyright 2006-7 Martin Brampton.  All rights reserved.';
	/** @public string URL */
	public $URL = '<a href="http://www.aliro.org">%s</a> is Free Software released under the GNU/GPL License.';

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
	}

	function footer () {
		return "<div>".sprintf($this->URL,$this->PRODUCT.' '.$this->RELEASE.'/'.$this->DEV_STATUS.'/'.$this->DEV_LEVEL).'</div>';
	}
}