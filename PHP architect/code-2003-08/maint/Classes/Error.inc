<?php

require_once 'Constants.inc';

class cSevereError
{
   function alert ($msg)
   {
      $syslog = false;
	  $email = false;
      $details  = "Severe Error---\n";
	  $details .= $msg;
      if (ADD_TRACE)
      {
         $details .= "Back Trace---\n";
         $details .= var_export(debug_backtrace(),true);
         $details .= "---Back Trace\n";
	  }
      $details  .= "---Severe Error\n";
      if (WRITE_TO_SYSLOG)
	  {
	     $syslog = @error_log($details,0);
	  }
      if (SEND_EMAIL)
	  {
	     $email = @error_log($details,1,EMAIL_ADDRESS);
	  }
	  if (!$email and ECHO_ON_FAILURE)
	  {
	  	echo "<b>A Severe Error has occurred.</b> The server is not feeling well. Please contact the technical support and send them a copy of this page. Thank you, and sorry for any inconvenience that was caused.";
		echo "<pre>" . $details . "</pre>";
	  }
	  exit(-1);
   }
}

?>