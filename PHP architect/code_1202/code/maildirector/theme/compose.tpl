{include file="open.tpl"}

{literal}
<script language="JavaScript">
	function toolbarCancel()
	{
		if ( confirm('Are you sure you want to quit without saving?') )
		{
			window.close();
		}
	}
</script>
{/literal}

<form action="{$smarty.server.PHP_SELF}" method="POST" name="email">
<input type="hidden" name="A" value="send">
<input type="hidden" name="F" value="{$F}">
<input type="hidden" name="M" value="{$M}">
<table border="0" cellpadding="2" cellspacing="0" width="800" height="100%">
	<tr>
		<td colspan="2">
		<img src="theme/img/emailUnread.gif" align="middle">
		<a href="javascript:document.email.submit();">[Send]</a>
		<a href="javascript:toolbarCancel();">[Cancel Compose]</a>
		</td>
		<td align="right">{$M}</td>
	</tr>
	<tr>
		<td class="composeField">From</td>
		<td width="100%"><select class="composeInput composeFrom" name="from">
						{foreach from=$from key=account item=addr}
							{foreach from=$addr item=pair}
								<option value="{$account}#{$pair.email}">{$pair.name} ({$pair.email}) [{$account}]</option>
							{/foreach}
						{/foreach}
			</select></td>
		<td valign="top" rowspan="4">
			<iframe src="composeAttachment.php?F={$F}&M={$M}" style="border: 1px inset #656565;" width="300" height="100"></iframe>
		</td>
	</tr>
	<tr>
		<td class="composeField">To</td>
		<td><input type="text" name="to" class="composeInput composeAddress" value="{$to}"></td>
	</tr>
	<tr>
		<td class="composeField">CC</td>
		<td><input type="text" name="cc" class="composeInput composeAddress" value="{$cc}"></td>
	</tr>
	<tr>
		<td class="composeField">BCC</td>
		<td><input type="text" name="bcc" class="composeInput composeAddress" value="{$bcc}"></td>
	</tr>
	<tr>
		<td class="composeField">Subject</td>
		<td valign="top" colspan="2"><input type="text" name="subject" class="composeInput composeSubject" value="{$subject}"></td>
	</tr>
	<tr>
		<td valign="top" colspan="3" height="100%"><textarea name="body" cols="78" rows="50"class="composeInput composeBody">{$body}</textarea></td>
	</tr>
</table>
</form>

{include file="close.tpl"}
