<?php /* $Id: mailer.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

/* Include phpmailer */
include_once( dirname(__FILE__) . '/class.phpmailer.php');

class mailer extends phpmailer
{
	function mailer()
	{
		$this->phpmailer();
		$this->Mailer 	= 'smtp';
		$this->Helo		= gethostbyaddr('127.0.0.1');
	}
}
?>
