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
 * aliroPage403 should be developed to be much more user friendly.  At present
 * it simply provides some basic diagnostics when a page is refused for any reason.
 *
 */


class aliroPage403 extends aliroPageFail {

	public function __construct ($message='') {
		if (aliroCore::getInstance()->getCfg('debug')) echo aliroBase::trace();
		if (aliroComponentHandler::getInstance()->componentCount() AND aliroMenuHandler::getInstance()->getMenuCount()) {
			header ($_SERVER['SERVER_PROTOCOL'].' 403 Not Authorised');
			$this->recordPageFail('403');
			$searchtext = $this->searchuri();
			$request = aliroRequest::getInstance();
			$request->setSpecialItemid(_ALIRO_SPECIAL_ID_PAGE403);
			$request->noRedirectHere();
			
			$request->setPageTitle(T_('403 Error - request not authorised'));
			echo <<<PAGE_403
			<h3>Sorry! Request not authorised</h3>
			{$this->formatMessage($message)}
			<p>
			This may be a problem with our system, and the issue 
			has been recorded for investigation. Or it could be that 
			you need to be logged in to do what you wanted to do.
			</p>
			<p>
			If you are not logged in, you might like to log in and 
			try again.  Or maybe you need us to help you to access
			what you want.
			</p>
			<p>
			If you have any query you would like us to deal with, 
			please use the CONTACT US facility from the main menu.  
			</p>
			<p>
			The following items have some connection with the URI 
			you used to come here, so maybe they are what you were 
			looking for?
			</p>
PAGE_403;

			echo $searchtext;
		}
		else echo T_('This Aliro based web site is not yet configured with user data, please call back later');
	}
}