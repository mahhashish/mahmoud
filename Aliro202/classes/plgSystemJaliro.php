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
 * plgSystemJaliro emulates the JaliroPlugin base class for Aliro
 *
 */

class plgSystemJaliro {
	public static $cmsgroups = 0;
	
	public static function setAmazonS3Params (&$accesskey, &$secretkey, &$bucket) {
		$coredata = aliroCore::getInstance();
		$accesskey = $coredata->getCfg('s3accesskey');
		$secretkey = $coredata->getCfg('s3secretkey');
		$bucket = $coredata->getCfg('s3bucket');
	}
}