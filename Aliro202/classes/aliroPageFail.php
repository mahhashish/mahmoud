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
 * aliroPageFail is the base class for failure classes - currently aliroPage403
 * and aliroPage404.
 *
 */

abstract class aliroPageFail {

	protected function formatMessage ($message) {
		if ($message) return <<<FORMAT_MSG

			<h4>
				$message
			</h4>

FORMAT_MSG;

	}

	protected function T_ ($string) {
		return function_exists('T_') ? T_($string) : $string;
	}

	protected function recordPageFail ($errorcode) {
		$database = aliroCoreDatabase::getInstance();
		$uri = $database->getEscaped(@$_SERVER['REQUEST_URI']);
		$timestamp = date ('Y-m-d H:i:s');
		$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$referer = $database->getEscaped($referer);
		$ip = aliroRequest::getInstance()->getIP();
		$post = base64_encode(serialize($_POST));
		$trace = aliroBase::trace();
		$database->doSQL("INSERT INTO #__error_404 (uri, timestamp, referer, ip, errortype, post, trace) VALUES ('$uri', '$timestamp', '$referer', '$ip', '$errorcode', '$post', '$trace') ON DUPLICATE KEY UPDATE timestamp = '$timestamp', referer='$referer', post='$post', trace='$trace'");
		$database->doSQL("DELETE LOW_PRIORITY FROM #__error_404 WHERE SUBDATE(NOW(), INTERVAL 14 DAY) > timestamp");
	}

	protected function searchuri () {
		$uri = @$_SERVER['REQUEST_URI'];
		$bits = explode ('/', $uri);
		for ($i=count($bits); $i>0; $i--) {
			$bit = $bits[$i-1];
			if ($bit) break;
		}
		$bit = str_replace(array('!', '%21'), array('',''), $bit);
		$searchword = preg_replace('/[^A-Za-z]/', ' ', $bit);
		$results = aliroMambotHandler::getInstance()->trigger('onSearch', array($searchword, 'all', 'popular'));
		$lines = array();
		foreach ($results as $result) {
			if ($result) foreach ($result as $item) {
				if (empty($item->text)) continue;
				$item->text = aliroRequest::getInstance()->doPurify($item->text);
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
