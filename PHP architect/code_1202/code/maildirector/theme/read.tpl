<html>
	<head>
		<title></title>
	{literal}
	<script language="JavaScript" src="theme/compose.js"></script>
	<style type="text/css">
	BODY,TABLE,TR,TD
	{
		font-family: sans-serif;
		font-size: 12px;
		margin: 0px 0px 0px 0px;
	}

	.headers
	{
		width: 100%;
		background-color: #b0c1db;
		border: 1px outset #8484a7;
	}
	
	</style>
	{/literal}
	</head>
<body>
<table border="0" cellpadding="4" cellspacing="0" class="headers">
	<tr>
		<td>
			<span style="font-weight: bold;">From:</span> {$message->headers.from.1} ({$message->headers.from.0}) &nbsp;&nbsp;&nbsp;
			<span style="font-weight: bold;">Date:</span> {$message->headers.date}<br>
			<span style="font-weight: bold;">To:</span>
				{foreach from=$message->headers.to item=t}
				{$t.1} ({$t.0})&nbsp;
				{/foreach}
			&nbsp;&nbsp;&nbsp; 
			{if $message->headers.cc}
			<span style="font-weight: bold;">Cc:</span>
				{foreach from=$message->headers.cc item=t}
				{$t.1} ({$t.0})&nbsp;
				{/foreach}
			&nbsp;&nbsp;&nbsp;
			{/if}
			<br>
		</td>
	</tr>
	<tr>
		<td><span style="font-weight: bold;">Subject:</span> <span style="color: white; font-weight: bold;">{$message->headers.subject}</span></td>
	</tr>
	<tr>
		<td>
			<b>
			<img src="theme/img/emailReplied.gif"> <a href="javascript:newComposer('{$F}','{$M}','reply');">Reply</a>
			<!-- <img src="theme/img/emailReplied.gif"> <a href="javascript:newComposer('{$F}','{$M}','replyall');">Reply To All</a> -->
			<img src="theme/img/emailForward.gif"> <a href="javascript:newComposer('{$F}','{$M}','forward');">Forward</a>
			<img src="theme/img/emailRead.gif"> <a href="?F={$F}&M={$M}&A=delete">{if $md->T}Undelete{else}Delete{/if}</a>
			<img src="theme/img/emailFlag.gif"> <a href="?F={$F}&M={$M}&A=flag">Flag</a>
			<img src="theme/img/folder.gif"> <a href="?F={$F}&M={$M}&A=move">Move</a>
			</b>
			[{$M}]
			{if $message->parts}
				<br>
				{foreach from=$message->parts key=id item=p}
					<img src="theme/img/emailAttachment.gif"> <a href="readAttachment.php?F={$F}&M={$M}&id={$id}" target="_blank">{$p.name}</a> &nbsp;
				{/foreach}
			{/if}
		</td>
	</tr>
</table>
<table border="0" cellpadding="8" cellspacing="0">
	<tr>
		<td style="font-family: monospace;">
		{$body}
		</td>
	</tr>
</table>

</body>
</html>
