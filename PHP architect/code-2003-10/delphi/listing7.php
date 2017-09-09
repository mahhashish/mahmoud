<?php echo '<?xml version="1.0" encoding="ISO-8859-1" ?>' . "\n" ?>
<HeaderInfo>
<MenuCaption><?php echo $HeaderInfo["Description"]  ?></MenuCaption>
<MenuPath><?php echo $HeaderInfo["MenuPath"]  ?></MenuPath>
<ReportID><?php if (isset($HeaderInfo["ReportID"])) {echo $HeaderInfo["ReportID"]; }else {echo "-1";}   ?></ReportID>
<FileName><?php echo $FileName  ?></FileName>
<VARS Name="Required">
	<?php 
	if (isset($HeaderInfo["RequiredVars"])) 
	{	
		foreach($HeaderInfo["RequiredVars"] as $VAR )
		{	
		
	      	
	?>
		<VAR Name="<?php echo @$VAR['Name']  ?>" Description="<?php echo @$VAR['Description']  ?>" ValuesType="<?php echo @$VAR['ValuesType'] ?>" 
		Values="<?php echo @$VAR['Values']  ?>" DefaultValue="<?php echo @$VAR['Default']  ?>" > </VAR>
	<?php 
		} 
	} ?>
</VARS>
<VARS Name="Optional">
	<?php 
	if (isset($HeaderInfo["OptionalVars"])) 
	{
	    
	
		foreach($HeaderInfo["OptionalVars"] as $VAR )
		{	
		
	      	
	?>
		<VAR Name="<?php echo @$VAR['Name']  ?>" Description="<?php echo @$VAR['Description']  ?>"  ValuesType="<?php echo @$VAR['ValuesType'] ?>" 
		Values="<?php echo @$VAR['Values']  ?>" DefaultValue="<?php echo @$VAR['Default']  ?>" > </VAR>
	<?php   }
	}  ?>
</VARS>
</HeaderInfo>

