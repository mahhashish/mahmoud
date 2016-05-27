<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class aliroMailMessage extends aliroMailer {
	
	public function __construct ($from='', $fromname='') {
		parent::__construct();
		$this->setFromDetails($from, $fromname);
	}
	
	public function sendMail ($recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL) {
		$this->setSubject($subject);
		// activate HTML formatted emails
		if ($mode) $this->setHTML($body);
		else $this->setText($body);
		$this->setManyDetails($cc, $bcc, $attachment, $replyto, $replytoname);
		return $this->sendAndLog($recipient);
	}
	
	public function mailSuperAdmins ($subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL) {
		$admins = aliroAuthoriser::getInstance()->listAccessors('aUser', 'Super Administrator');
		$adminlist = implode(',', $admins);
		$details = aliroDatabase::getInstance()->doSQLget("SELECT email, sendEmail FROM #__users WHERE id IN ($adminlist)");
		foreach ($details as $detail) if ($detail->sendEmail) $recipient[] = $detail->email;
		if (isset($recipient)) $this->sendMail($recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
	}
}
