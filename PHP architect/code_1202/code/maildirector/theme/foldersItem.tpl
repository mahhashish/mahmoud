<table border="0" cellpadding="0" cellspacing="2">
	<tr>
		<td width="16" id="folder123" onClick="clickFolder(this.id);"><img src="theme/img/arrow_open.gif"></td>
		<td width="16"><img id="{$id}_folder}" src="theme/img/folder.gif"></td>
		<td nowrap><a href="listing.php?F={$folder->path}" id="{$id}_link" target="listing">{$folder->name|default:$folder->path}</a></td>
	</tr>
	{if $children}
	<tr class="folderChildren" id="{$id}_children">
		<td></td>
		<td colspan="2">{$children}</td>
	</tr>
	{/if}
</table>
