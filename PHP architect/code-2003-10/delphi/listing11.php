	
	//Create COM progress form.
	$prog = new com("tfmComLibrary.ProgressBar");
	
	$prog->max($Count);
	$rs = mysql_query($SomeSQL,$db);
	$row = mysql_fetch_array($rs);
	while(!$row  == false)
	{
				
		//Do Some stuff with your data.
		echo $row['Userinfo'];
		
		
		//Increment progress display.
		$prog->StepIt();
		
		//Check if user has cancelled report.
		if ($prog->Cancelled) 
		{
			echo "Report Cancelled by User";
			Break;			    
		}
		
		$row = mysql_fetch_array($rs);
	} // while	
