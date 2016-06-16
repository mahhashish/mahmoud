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
 * aliroPage404 should be developed to be much more user friendly.  At present
 * it simply provides some basic diagnostics when a page link does not work out.
 *
 */


class aliroPage404 {

	public function __construct () {
		if (aliroComponentHandler::getInstance()->componentCount() AND aliroMenuHandler::getInstance()->getMenuCount('mainmenu')) {
			header ('HTTP/1.1 404 Not Found');
			$this->record404();
			$searchtext = $this->searchuri();
			aliroRequest::getInstance()->setPageTitle(T_('404 Error - page not found'));
			echo <<<PAGE_404
			<h3>Sorry! Page not found</h3>
			<p>
			This may be a problem with our system, and the issue 
			has been logged for investigation. Or it could be that 
			you have an outdated link.
			</p>
			<p>
			This page is also presented for an item that exists 
			but is not available to you.  If you are not logged in, 
			you might like to log in and try again.
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
PAGE_404;

			echo $searchtext;
		}
		else echo T_('This Aliro based web site is not yet configured with user data, please call back later');
	}

	private function record404 () {
		$uri = $_SERVER['REQUEST_URI'];
		$timestamp = date ('Y-m-d H:i:s');
		$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$post = base64_encode(serialize($_POST));
		$trace = aliroRequest::trace();
		aliroCoreDatabase::getInstance()->doSQL("INSERT INTO #__error_404 (uri, timestamp, referer, post, trace) VALUES ('$uri', '$timestamp', '$referer', '$post', '$trace') ON DUPLICATE KEY UPDATE timestamp = '$timestamp', referer='$referer', post='$post', trace='$trace'");
	}

	private function searchuri () {
		$uri = $_SERVER['REQUEST_URI'];
		$bits = explode ('/', $uri);
		for ($i=count($bits); $i>0; $i--) {
			$bit = $bits[$i-1];
			if ($bit) break;
		}
		$bit = str_replace(array('!', '%21'), array('',''), $bit);
		$searchword = preg_replace('/[^A-Za-z]/', ' ', $bit);
		$results = aliroMambotHandler::getInstance()->trigger('onSearch', array($searchword, 'all', 'popular'));
		$lines = array();
		$purifier = new HTMLPurifier;
		foreach ($results as $result) {
			if ($result) foreach ($result as $item) {
				if (empty($item->text)) continue;
				$item->text = $purifier->purify($item->text);
				$item->text = strip_tags($item->text);
				if (strlen($item->text) > 200) $item->text = substr($item->text,0,200).'...';
				if (!isset($item->section)) $item->section = '';
				$lines[] = $item;
			}
		}
		$html = '';
		$sef = aliroSEF::getInstance();
		if (count($lines)) foreach ($lines as $line) {
			$section = isset($line->section) ? $line->section : '';
			$html .= <<<SEARCH_LINE

			<p>
			<a href="{$sef->sefRelToAbs($line->href)}">$line->title</a>
			$section
			$line->text
			</p>

SEARCH_LINE;

		}
		else $html = '<p>'.T_('Sorry, none found').'</p>';
		return $html;
	}

}