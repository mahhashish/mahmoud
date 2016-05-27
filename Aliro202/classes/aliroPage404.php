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
 * aliroPage404 displays messages and information when the requested page
 * cannot be found.
 *
 */

class aliroPage404 extends aliroPageFail {

	public function __construct ($message='') {
		if (aliroCore::getInstance()->getCfg('debug')) echo aliroBase::trace();
		if (aliroComponentHandler::getInstance()->componentCount() AND aliroMenuHandler::getInstance()->getMenuCount()) {
			header ($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			$this->recordPageFail('404');
			$searchtext = $this->searchuri();
			$request = aliroRequest::getInstance();
			$request->noRedirectHere();
			$request->setSpecialItemid(_ALIRO_SPECIAL_ID_PAGE404);
			
			$request->setPageTitle($this->T_('404 Error - page not found'));
			echo <<<PAGE_404
			
			<div id="aliropage404">
				<div id="aliropage404intro">
					<h3 id="aliropage404header">{$this->T_('Sorry! Page not found')}</h3>
					<p>
					{$this->T_('This may be a problem with our system, and the issue has been logged for investigation. Or it could be that you have an outdated link.')}
					</p>
					<p>
					{$this->T_('If you have any query you would like us to deal with, please contact us')}
					</p>
					<p>
					{$this->T_('The following items have some connection with the URI you used to come here, so maybe they are what you were looking for?')}
					</p>
				</div>
				
PAGE_404;

			echo <<<LAST_PARA
			
				<div id="aliropage404search">
					$searchtext
				</div>
				<div id="aliropage404techinfo">
					<p>
						{$this->T_('Technical support staff may be helped by the following message:')}
						$message
					</p>
				</div>
			</div>
				
LAST_PARA;
				
		}
		else echo $this->T_('This Aliro based web site is not yet configured with user data, please call back later');
	}
}