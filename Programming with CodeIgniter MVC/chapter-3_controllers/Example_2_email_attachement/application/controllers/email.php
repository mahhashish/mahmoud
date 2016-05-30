<?php

/***********************************************************************
* Use the CI Build-in Email Library to send emails with attachements 
  and enable UTF-8 Subject and contet as well as HTML body content.
************************************************************************/
class Email extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index() 
	{	
	    // Loading the CI build in library for Email services 
		$this->load->library('email');
		
	    // Config the library to work with UTF-8 for multi language suport 
		// As well as enable HTML content Body 
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
	    
		// Loads the Config Settings:
		$this->email->initialize($config);
		// Replaces CR/LF with HTML <BR/>
		$this->email->set_newline("<BR/>");
		
		// Define The From Email 
		$this->email->from('eliorr@phpmyqsl.com', 'Eli Orr');
		
		// Define the To Email one or more: 				
		$this->email->to  (array(    '"Name 1" <name1@name.com>' 
						    ,'"Name 2" <name2@name.com>'
						   ));	
		// Set the Subject 
		$this->email->subject( "Non latin UTF-8 langue Subject are ok as well" );		
		
        // Define the Email Body in HTML as we enabled its : 
		$this->email->message
(
'<H1>Hello there!<H1/>
<p> 
This Email is sent to you from CI via its cool Email library :-)<BR/>
<font color=green size=3><b>See Attached Files...</b></font><BR/>
There are very intersting services such as <BR/>
 
Attached:<BR/>

<ul>
<li> 1 - File One.</li>
<li> 2 - Second File </li>
</p>
'
);

	    // Load the attachements     
		$path = $this->doc_root_path (); 
		
		// Doc root e.g:  /home/yourdomain.com/public_html
		// Let say attachement under public_html as /attachments
		$attachment_path1 =  $path."/attachments/file1.jpg"; 
		$attachment_path2 =  $path."/attachments/file2.php"; 
		
		// Set the two attachements to be in the email library instance: 
		$this->email->attach($attachment_path1);
		$this->email->attach($attachment_path2);
		
		// We have all ready ! Walla! let's send it!  
		if($this->email->send())
		{   // We are ok the email was sent.
			echo 'Your email was sent. See the steps executd:';
		    
		}
		else
		{   // We had some problems lets show what went wrong:
		}
        // Any case just show the debugging of the process to learn:
		echo $this->email->print_debugger();
		
	}
	
	//----------------------------------------------------------
	function doc_root_path () {
	 return $doc_root = $_SERVER["DOCUMENT_ROOT"]; 
	
	}
}


      