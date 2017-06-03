<?php
   // Error flags
   define ("NO_ERROR", 0);
   // File Error flags
   define ("FILE_DOES_NOT_EXIST",   -4001);
   define ("COULD_NOT_CREATE_FILE", -4002);
   define ("COULD_NOT_OPEN_FILE",   -4003);
   // Severe Error Actions
   define ("WRITE_TO_SYSLOG", true);
   define ("SEND_EMAIL", true);
   define ("EMAIL_ADDRESS", "graeme@druknet.bt, graeme@acm.org");
   define ("ADD_TRACE", true);
   define ("ECHO_ON_FAILURE", true);  // normally only set to true for intranets
   // Valid responses to whichfile (cErrorLog)
   define ("CONSOLIDATED",1);
   define ("INDIVIDUAL",2);
   define ("BOTH_FILES",3);
   // Error Levels
   define ("AUTH","Authorisation");
   define ("VALID","Validation");
   define ("USER","User");
   define ("SQL","SQL");
   define ("SEVERE","Severe - ouch");

?>