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
 * aliroMailer provides email services.
 *
 * aliroMailMessage provides a simple email sending interface
 *
 * mosMailer provides backwards compatibility
 *
 */

require_once _ALIRO_CLASS_BASE.'/extclasses/SwiftMailer/lib/swift_required.php';

class aliroMailer extends htmlMimeMail5 {
	protected $Mailer = 'mail';
	private $config = null;
	private $body = '';
	private $fromemail = '';

	public function __construct () {
		parent::__construct();
		$this->configureHtmlMimeMail5 ();
	}

	private function getCfg ($property) {
		static $config = null;
		if (null === $config) $config = aliroCore::getInstance();
		return $config->getCfg($property);
	}

	private function configureHtmlMimeMail5 () {
		$config = aliroCore::getInstance();
		$this->setTextCharset(defined('_ISO') ? substr(_ISO, 8) : 'utf-8');
		$this->Mailer = $config->getCfg('mailer');
		$sendmail = $config->getCfg('sendmail');
		// Add smtp values if needed
		if ($this->Mailer == 'smtp') {
			$auth = $config->getCfg('smtpauth');
			$user = $config->getCfg('smtpuser');
			$pass = $config->getCfg('smtppass');
			$host 	= $config->getCfg('smtphost');
			$port = null;
			$helo = null;
			$this->setSMTPParams($host, $port, $helo, $auth, $user, $pass);
		}
		// Set sendmail path
		elseif ('sendmail' == $this->Mailer AND $sendmail) $this->setSendmailPath($sendmail);
		else $this->Mailer = 'mail';
	}
	
	public function simpleSend ($recipient) {
		return $this->send($recipient, $this->Mailer);
	}

	protected function addAnyAttachment ($attachment) {
		if ($attachment instanceof stringAttachment OR $attachment instanceof fileAttachment) $this->addAttachment($attachment);
		elseif (is_string($attachment)) {
			if (file_exists($attachment)) {
				$attobject = new fileAttachment($attachment);
				$this->addAttachment($attobject);
			}
			else {
				$attobject = new stringAttachment($attachment);
				$this->addAttachment($attobject);
			}
		}
	}
	
	protected function setFromDetails ($from, $fromname) {	
		$config = aliroCore::getInstance();
		$this->fromemail = $from ? $from : $config->getCfg('mailfrom');
		$fromname = $fromname ? $fromname : $config->getCfg('fromname');
		$this->setFrom ($fromname ? "\"$fromname\" <$this->fromemail>" : "<$this->fromemail>");
	}
	
	public function getFrom () {
		return $this->fromemail;
	}
	
	protected function setManyDetails ($cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL) {
		foreach ((array) $cc as $to) $this->setCc($to);
		foreach ((array) $bcc as $to) $this->setBcc($to);
		if (is_object($attachment)) $attachment = array($attachment);
		else $attachment = (array) $attachment;
		foreach ($attachment as $fname) $this->addAnyAttachment($fname);
		$replynames = (array) $replytoname;
		foreach ((array) $replyto as $sub=>$to) {
			if (isset($replynames[$sub])) $to = "\"{$replynames[$sub]}\"<$to>";
			$this->setReplyTo($to);
		}
	}
	
	protected function sendAndLog ($recipient) {
		$result = $this->send((array) $recipient, $this->Mailer);
		$this->logMail(implode(' / ', (array) $recipient));
		return $result;
	}
	
	protected function logMail ($recipient) {
	    $database = aliroCoreDatabase::getInstance();
		$recipient = $database->getEscaped($recipient);
	    $ip = aliroRequest::getInstance()->getIP();
	    $posted = $database->getEscaped(serialize($_POST));
	    $userid = (string) aliroUser::getInstance()->id;
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	    $database->doSQL("INSERT INTO `#__mail_log` (userid, date, transport, recipient, ip, query, post) VALUES ($userid, NOW(), '$this->Mailer', '$recipient', '$ip', '$uri', '$posted')");
		$database->doSQL("DELETE LOW_PRIORITY FROM `#__mail_log` WHERE date < SUBDATE(NOW(), INTERVAL 7 DAY)");
	}

}

class mosMailer extends aliroMailer {
	private $body = '';

	/**
	* Function to create a mail object for futher use (uses phpMailer)
	* @param string From e-mail address
	* @param string From name
	* @param string E-mail subject
	* @param string Message body
	* @return object Mail object
	*/
	public function __construct ( $from, $fromname, $subject, $body ) {
		parent::__construct();
		$this->setFromDetails($from, $fromname);
		$this->setSubject($subject);
	    $this->body = $body;
	}

	/**
	* Mail function (uses htmlMimeMail5)
	*/
	public function mosMail($recipient, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
		// activate HTML formatted emails
		if ($mode) $this->setHTML($this->body);
		else $this->setText($this->body);
		$this->setManyDetails($cc, $bcc, $attachment, $replyto, $replytoname);
		return $this->sendAndLog($recipient);
	} // mosMail
	
}