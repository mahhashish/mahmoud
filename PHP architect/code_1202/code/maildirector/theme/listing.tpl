<html>
	<head>
		<title></title>
	</head>
{literal}
<script language="JavaScript">
	function messageClick(mid)
	{
		{/literal}
		window.parent.frames['message'].location = '/read.php?F={$folder->path}&M=' + mid;
		{literal}
	}

	function messageOver(obj)
	{
		obj.className = 'messageOver';
	}

	function messageOut(obj)
	{
		obj.className = 'messageRow';
	}
	
</script>
<style type="text/css">
	BODY, TABLE, TR, TD
	{
		font-family: sans-serif;
		font-size: 12px;
		margin: 0px 0px 0px 0px;
	}

	.header
	{
		font-size: 22px;
		font-weight: bold;
		width: 100%;
		background-color: whitesmoke;
		/*color: #8484a7;*/
		color: #656565;
		height: 25px;

		border-bottom: 1px solid #404040;
	}

	.headerRow
	{
		background-color: #9999cc;
		color: white;
	}

	.headerField
	{
		font-weight: bold;
		font-size: 12px;
	}

	.messageRow
	{
		background-color: white;
		padding: 2px 2px 2px 2px;
	}

	.messageOver
	{
		color: blue;
		/*color: white;
		background-color: #b0c1db;*/
	}

	.messageField
	{
		border-bottom: 1px solid #8484a7;
	}

</style>
{/literal}

<body>
<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr class="header">
		<td colspan="7"><img src="theme/img/folder.gif"> {$folder->path}</td>
		<td colspan="1" align="right">
		{if $expunge gt 0 }
		Expunged {$expunge} messages 
		{/if}
		<a href="{$smarty.server.PHP_SELF}?F={$folder->path}&A=expunge">[Expunge]</a></td>
	</tr>
	<tr class="headerRow">
		<td class="headerField" width="20">&nbsp;</td>
		<td class="headerField" width="16"><img src="theme/img/emailHigh.gif"></td>
		<td class="headerField" width="16"><img src="theme/img/emailAttachment.gif"></td>
		<td class="headerField" width="16"><img src="theme/img/emailFlag.gif"></td>
		<td class="headerField">From</td>
		<td class="headerField">Subject</td>
		<td class="headerField">Size</td>
		<td class="headerField">Date</td>
	</tr>

{foreach from=$messages key=mid item=info}
	<tr class="messageRow" onClick="messageClick('{$mid}');" onMouseOver="messageOver(this);" onMouseOut="messageOut(this);"
	{if $info.state eq 'tmp'} style="color: orange;"{/if}>
		<td class="messageField"><img src="theme/img/{if $info.T}emailHigh{elseif $info.R}emailReplied{elseif $info.S}emailRead{else}emailUnread{/if}.gif"></td>
		<td class="messageField">&nbsp;</td>
		<td class="messageField">{if $info['x-attachment']}x{/if}&nbsp;</td>
		<td class="messageField">{if $info.F eq 1}<img src="theme/img/emailFlag.gif">{/if}&nbsp;</td>
		<td class="messageField" nowrap>{$info.from.1}&nbsp;</td>
		<td class="messageField" width="50%">{if $info.subject}{$info.subject}{else}[ {$mid} ]{/if}&nbsp;</td>
		<td class="messageField">{$info.size}</td>
		<td class="messageField" nowrap>{$info.date}&nbsp;</td>
	</tr>
{/foreach}

	<tr class="headerRow">
		<td colspan="8">
		{if $prev gte 0}<a href="?F={$folder->path}&offset={$prev}"><< Prev {$size}</a>{/if}
		{if $next lt $total}<a href="?F={$folder->path}&offset={$next}">Next {$size}>></a>{/if}

		</td>
	</tr>

</table>

</body>
</html>
