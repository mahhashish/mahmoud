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
 * aliroSpamHandler provides central services for anti-spam plugins and admin
 * side spam management.
 *
 */

final class aliroSpamRecord extends aliroDatabaseRow {
	protected static $spamrecords = array();
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__spam_log';
	protected $rowKey = 'id';

	public static function getByID ($id) {
		$id = intval($id);
		if (isset(self::$spamrecords[$id])) return self::$spamrecords[$id];
		$spamrecord = new self();
		if ($id) {
			$spamrecord->load($id);
			self::$spamrecords[$id] = $spamrecord;
		}
		return $spamrecord;
	}

	public static function countByIP ($ip) {
		$anyrecord = new self();
		$database = $anyrecord->getDatabase();
		$ip = $database->getEscaped($ip);
		$database->setQuery("SELECT COUNT(*) FROM $anyrecord->tableName WHERE authorip = '$ip'");
		return intval($database->loadResult());
	}
}

final class aliroSpamHandler extends cachedSingleton {
    protected static $instance = __CLASS__;

	protected $spamoptions = array();
	protected $identifiers = array();
	protected $checkers = 0;
	protected $spaminess = 0.0;
	protected $status = 'unknown';
	protected $blacklist = array();

	protected function __construct () {
		$this->spamoptions = array(
		'ham' => $this->T_('Ham'),
		'spam' => $this->T_('Spam'),
		'unknown' => $this->T_('Unknown'),
		'unsure' => $this->T_('Unsure'),
		'profanity' => $this->T_('Profanity'),
		'unwanted' => $this->T_('Unwanted'),
		'lowquality' => $this->T_('Low Quality')
		);
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL("DELETE FROM #__spam_blacklist WHERE stamp < SUBDATE(NOW(), INTERVAL 14 DAY)");
		$blacks = $database->doSQLget("SELECT * FROM #__spam_blacklist");
		foreach ($blacks as $black) $this->blacklist[$black->ip] = $this->makeResultArray($black->status, $black->spaminess, $black->identifier);
	}

	protected function T_ ($string) {
		return function_exists('T_') ? T_($string) : $string;
	}
	
	private function makeResultArray ($status, $spaminess, $identifier) {
		return array(
		'status' => $status,
		'spaminess' => $spaminess,
		'identifier' => $identifier
		);
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	public function getSpamOptions () {
		return $this->spamoptions;
	}
	
	public function isBlackListed ($ip) {
		return isset($this->blacklist[$ip]);
	}

	public function checkContentForSpam ($identifier='', $title='', $body='', $authorName='', $authorURI='', $authorEmail='', $authorIP='', $authorOpenID='', $authorID=0, $articlePermalink='', $articleDate=0, $trusted=false) {
		if (isset($this->blacklist[$authorIP])) return $this->blacklist[$authorIP];
		$args = func_get_args();
		$results = aliroMambotHandler::getInstance()->trigger('checkContentForSpam', $args);
		$this->evaluate($results);
		$database = aliroCoreDatabase::getInstance();
		foreach ($args as $key=>$arg) $args[$key] = $database->getEscaped($arg);
		$logid = call_user_func_array(array($this, 'logSpamCheck'), $args);
		if (0 == $logid % 10) $this->pruneSpamLogs();
		foreach ($results as $result) {
			$database->doSQL("INSERT INTO #__spam_log_results (spamid, checker, status, spaminess, identifier) VALUES ($logid, '{$result['checker']}', '{$result['status']}', '{$result['spaminess']}', '{$result['identifier']}')");
		}
		$final =  $this->makeResultArray($this->status, $this->spaminess, $logid);
		if ('spam' == $this->status) {
			if (0.95 < $this->spaminess) $this->blockIP($authorIP, $final);
			elseif (10 < aliroSpamRecord::countByIP($authorIP)) $this->blockIP($authorIP, $final);
		}
		return $final;
	}

	private function blockIP ($ip, $final) {
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL("INSERT INTO #__spam_blacklist (ip, status, spaminess, identifier)"
			." VALUES ('$ip', '{$final['status']}', {$final['spaminess']}, {$final['identifier']})"
			." ON DUPLICATE KEY UPDATE status = '{$final['status']}', spaminess = {$final['spaminess']}, identifier ={$final['identifier']}"
		);
		$this->clearCache();
	}

	public function announceArticleForComments ($authorName='', $authorEmail='', $title='', $body='', $articlePermalink='') {
		$args = func_get_args();
		$results = aliroMambotHandler::getInstance()->trigger('announceArticleForComments', $args);
		return (bool) array_product($results);
	}

	public function changeSpamStatus ($identifier, $newStatus) {
		if (isset($this->spamoptions[$newStatus])) {
			$database = aliroCoreDatabase::getInstance();
			$identifier = intval($identifier);
			if ($identifier) {
				$spamdata = aliroSpamRecord::getByID($identifier);
				if (isset($this->spamoptions[$spamdata->status])) {
					aliroMambotHandler::getInstance()->trigger('changeSpamStatus', array($spamdata, $newStatus));
				}
				$spamdata->status = $newStatus;
				if ('ham' == $newStatus) $spamdata->spaminess = 0.0;
				elseif ('spam' == $newStatus) {
					$spamdata->spaminess = 0.99;
					$final = $this->makeResultArray('spam', 0.99, $identifier);
					$this->blockIP($spamdata->authorip, $final);
				}
				$spamdata->store();
			}
		}
	}

	public function getSpamImageCaptcha ($identifier) {
		$results = aliroMambotHandler::getInstance()->trigger('getSpamImageCaptcha', array($identifier));
		foreach ($results as $result) if ($result) return $result;
		return '';
	}

	public function getSpamAudioCaptcha ($identifier) {
		$results = aliroMambotHandler::getInstance()->trigger('getSpamAudioCaptcha', array($identifier));
		foreach ($results as $result) if ($result) return $result;
		return '';
	}

	public function checkSpamCaptcha ($identifier, $solution) {
		$args = func_get_args();
		$results = aliroMambotHandler::getInstance()->trigger('checkSpamCaptcha', $args);
		return (bool) array_product($results);
	}

	public function getCheckerIdentifier ($identifier, $checkername) {
		$identifier = intval($identifier);
		if ($identifier AND !isset($this->identifiers[$identifier])) {
			$database = aliroCoreDatabase::getInstance();
			$database->setQuery("SELECT checker, identifier FROM #__spam_log_results WHERE spamid = $identifier");
			$results = $database->loadObjectList();
			foreach ($results as $spamcheck) $this->identifiers[$identifier][$spamcheck->checker] = $spamcheck->identifier;
		}
		return isset($this->identifiers[$identifier][$checkername]) ? $this->identifiers[$identifier][$checkername] : null;
	}

	private function evaluate ($results) {
		$this->checkers = count($results);
		$spaminess = 0.0;
		$spamcount = $hamcount = $othercount = $nesscount = 0;
		foreach ($results as $result) {
			if ('spam' == $result['status'] OR 'ham' == $result['status']) {
				$spaminess += (float) $result['spaminess'];
				$nesscount++;
				if ('spam' == $result['status']) $spamcount++;
				else $hamcount++;
			}
			elseif ('unknown' != $result['status']) $othercount++;
		}
		if ($spamcount) $this->status = 'spam';
		elseif ($hamcount AND !$othercount) $this->status = 'ham';
		else $this->status = 'unknown';
		$this->spaminess = $nesscount ? (float) $spaminess/$nesscount : 0;
	}

	private function logSpamCheck ($identifier='', $title='', $body='', $authorName='', $authorURI='', $authorEmail='', $authorIP='', $authorOpenID='', $authorID=0, $articlePermalink='', $articleDate=0, $trusted=false) {
		$database = aliroCoreDatabase::getInstance();
		$trusted = ($trusted ? 1 : 0);
		$authorID = intval($authorID);
		$articleDate = date('Y-m-d H:i:s', (intval($articleDate) ? intval($articleDate) : time()));
		$database->doSQL("INSERT INTO #__spam_log (checkers, spaminess, status, title, body, authorname, authoruri, authoremail, authorip, authoropenid, authorid, permalink, articledate, trusted)"
			." VALUES ('$this->checkers', '$this->spaminess', '$this->status', '$title', '$body', '$authorName', '$authorURI', '$authorEmail', '$authorIP', '$authorOpenID', $authorID, '$articlePermalink', '$articleDate', $trusted)");
		return $database->insertid();
	}

	private function pruneSpamLogs () {
		$database = aliroCoreDatabase::getInstance();
		$days = _ALIRO_KEEP_DAYS_OF_SPAM;
		$database->doSQL("DELETE FROM #__spam_log WHERE articledate < SUBDATE(NOW(), INTERVAL $days DAY)");
		$database->doSQL("DELETE r FROM #__spam_log_results AS r LEFT JOIN #__spam_log AS l ON r.spamid = l.id WHERE l.id IS NULL");
	}

}
