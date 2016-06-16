<?php

class aliroMailer extends htmlMimeMail5 {
	protected $Mailer = '';
	private $config = null;
	private $body = '';

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
		$this->setTextCharset(substr_replace(_ISO, '', 0, 8));
		$this->Mailer = $this->getCfg('mailer');
		$sendmail = $this->getCfg('sendmail');
		// Add smtp values if needed
		if ($this->Mailer == 'smtp') {
			$config = aliroCore::getInstance();
			$auth = $config->getCfg('smtpauth');
			$user = $config->getCfg('smtpuser');
			$pass = $config->getCfg('smtppass');
			$host 	= $config->getCfg('smtphost');
			$port = null;
			$helo = null;
			$this->setSMTPParams($host, $port, $helo, $auth, $user, $pass);
		}
		// Set sendmail path
		elseif ($this->Mailer == 'sendmail' AND $sendmail) $this->setSendmailPath($sendmail);
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
	public function __construct ( $from='', $fromname='', $subject, $body ) {
		parent::__construct();
		$config = aliroCore::getInstance();
		$fromemail = $from ? $from : $config->getCfg('mailfrom');
		$fromname = $fromname ? $fromname : $config->getCfg('fromname');
		$from = "$fromname <$fromemail>";
		$this->setFrom ($from);
	    $this->setSubject($subject);
	    $this->body = $body;

	}

	/**
	* Mail function (uses htmlMimeMail5)
	* @param string From e-mail address
	* @param string From name
	* @param string/array Recipient e-mail address(es)
	* @param string E-mail subject
	* @param string Message body
	* @param boolean false = plain text, true = HTML
	* @param string/array CC e-mail address(es)
	* @param string/array BCC e-mail address(es)
	* @param string/array Attachment file name(s)
	* @param string/array Reply-to e-mail address
	* @param string/array Reply-to name
	*/
	function mosMail($recipient, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
		// activate HTML formatted emails
		if ($mode) $this->setHTML($this->body);
		else $this->setText($this->body);
		if(!is_array($recipient)) $recipient = array($recipient);
		if (isset($cc)) {
		    if (is_array($cc)) foreach ($cc as $to) $this->setCC($to);
		    else $this->setCC($cc);
		}
		if (isset($bcc)) {
		    if(is_array($bcc)) foreach ($bcc as $to) $this->setBCC($to);
		    else $this->setBCC($bcc);
		}
	    if ($attachment) {
	        if (is_array($attachment)) foreach ($attachment as $fname) $this->addAttachment($fname);
	        else $this->addAttachment($attachment);
	    } // if
	    if ($replyto) {
	        if (is_array($replyto)) {
	            foreach ($replyto as $to) {
	            	$toname = ((list($key, $value) = each($replytoname))
					? $value : "");
	            	// $this->setReplyTo($to, $toname);
	            }
	        } // else $this->setReplyTo($replyto, $replytoname);
	    }
		$result = $this->Send($recipient, $this->Mailer);
	    $my = aliroUser::getInstance();
	    $database = aliroCoreDatabase::getInstance();
	    if (is_array($recipient)) {
	    	foreach ($recipient as &$rec) $rec= $database->getEscaped($rec);
	    	$recipient = implode (' / ', $recipient);
	    }
	    else $recipient = $database->getEscaped($recipient);
	    $posted = $database->getEscaped(serialize($_POST));
	    $userid = (string) $my->id;
	    $database->doSQL("INSERT INTO `#__mail_log` VALUES (0, $userid, NOW(), '$this->Mailer', '$recipient', '{$_SERVER['REQUEST_URI']}', '$posted')");
		$database->doSQL("DELETE LOW_PRIORITY FROM `#__mail_log` WHERE date < SUBDATE(NOW(), INTERVAL 7 DAY)");
		return $result;
	} // mosMail

}