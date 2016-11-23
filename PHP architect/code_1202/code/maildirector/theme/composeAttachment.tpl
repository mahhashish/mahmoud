{include file="open.tpl"}

{if $attachments}
	<a href="?F={$F}&M={$M}&A=form">[Add Attachment]</a><br>
	{foreach from=$attachments key=id item=item}
		<a href="?F={$F}&M={$M}&A=rm&rm={$id}">[Del]</a>
		<img src="theme/img/emailAttachment.gif"> {$item.file} 
		<br>
	{/foreach}
{else}
	<form action="{$smarty.server.PHP_SELF}" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="A" value="add">
	<input type="hidden" name="F" value="{$F}">
	<input type="hidden" name="M" value="{$M}">
	<input type="file" name="attach">
	<input type="submit" value="Upload">
	</form>
{/if}
{include file="close.tpl"}
