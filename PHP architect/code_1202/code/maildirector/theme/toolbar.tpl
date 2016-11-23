<html>
	<head>
		<title>Toolbar</title>
	</head>
	{literal}
	<script language="JavaScript" src="theme/compose.js"></script>
	<script language="JavaScript">
		function buttonOver(obj)
		{
			obj.className = 'button buttonOver';
		}

		function buttonOut(obj)
		{
			obj.className = 'button';
		}
		
		function buttonClick(obj)
		{
			obj.className = 'button buttonClick';
		}

		function mouseClick(obj)
		{
			if ( obj.id == 'win' )
			{
				window.open('index.php', '', 'toolbar=no,menubar=no,location=no,width=1200,height=768,resizable=yes');
			}

			if ( obj.id == 'getmail' )
			{
				window.open('getmail.php', 'getmail', 'toolbar=no,menubar=no,location=no,width=450,height=350,resizable=yes');
				getmail.focus();
			}

			return false;
		}
	</script>
	<style type="text/css">
		BODY
		{
			font-family: Arial, Tahoma, Verdana, sans-serif;
			font-size: 12px;
			margin: 0px 0px 0px 0px;
			border: 1px outset #b0c1db;
			background-color: #9999cc;
			color: #dce4ee;
		}

		.spacer
		{
			width: 2px;
		}
	
		.button
		{
			width: 20px;
			height: 20px;
			padding: 2px 2px 2px 2px;
			border: 1px;
			cursor: hand;
		}
	
		.buttonOver
		{
			border: 1px outset #dce4ee;
		}
	
		.buttonClick
		{
			border: 1px inset #b0c1db;
		}
	</style>
	{/literal}
<body>
<table border="0" cellpadding="0" cellspacing="0" style="padding-left: 3px; padding-top: 1px:" height="100%">
	<tr>
		<td class="spacer"></td>

		<td id="getmail" class="button"
			onMouseOver="buttonOver(this);"
			onMouseUp="buttonOver(this);"
			onMouseDown="buttonClick(this);"
			onMouseOut="buttonOut(this);"
			onClick="mouseClick(this)" align="center" valign="middle"><img src="theme/img/mailbox.gif"></td>
		<td class="spacer"></td>
		
		<td id="compose" class="button"
			onMouseOver="buttonOver(this);"
			onMouseUp="buttonOver(this);"
			onMouseDown="buttonClick(this);"
			onMouseOut="buttonOut(this);"
			onClick="newComposer('{$draft}')" align="center" valign="middle"><img src="theme/img/emailUnread.gif"></td>
		<td class="spacer"></td>
		
		<td id="win" class="button"
			onMouseOver="buttonOver(this);"
			onMouseUp="buttonOver(this);"
			onMouseDown="buttonClick(this);"
			onMouseOut="buttonOut(this);"
			onClick="mouseClick(this)" align="center" valign="middle"><img src="theme/img/form.gif"></td>
		<td class="spacer"></td>
	</tr>
</table>
</body>
</html>
