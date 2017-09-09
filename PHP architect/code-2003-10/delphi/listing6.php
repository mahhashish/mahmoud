//Are we expected to return config information.
if (isset($Config_Query)) 
{
	//Check we actually have some configuration information to return.
	if (isset($HeaderInfo)) 
	{
		//At this point all the information we need is contained in
		//the $HeaderInfo array, so all we need do is include the
		//xmlconfig.inc file and the host application will recieve it.
		include('xmlconfig.inc');			
	}

	//End execution.
	exit;    
}