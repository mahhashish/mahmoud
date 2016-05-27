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
 * aliroPDF is not yet developed but is planned to create PDF instead of HTML pages.
 */

class aliroPDF {
	private static $instance = null;

    private function __construct () {
		// Do whatever is needed
	}

	private function __clone () {
	    // Enforce singleton
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}

	public function createPDF () {
		// Do whatever is needed
	}
	
}