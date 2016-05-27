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
 * aliroPurifier extends HTMLPurifier to make it Aliro specific.
 * HTMLPurifier is a filter for HTML.
 *
 */

class aliroPurifier extends HTMLPurifier {
	
	public function __construct () {
  		$config = HTMLPurifier_Config::createDefault();
		$config->set('Cache.SerializerPath', _ALIRO_SITE_BASE.'/cache/HTMLPurifier');
   		if (_ALIRO_IS_ADMIN) {
			$config->set('HTML.Trusted', true);
			$config->set('Attr.EnableID', true);
		}
  		parent::__construct($config);
	}
}