{include file="header.tpl"}
{section name=l loop=$link}
	{if $smarty.section.l.first}
		<form action="{$action_link}" method="post">
		<div>
		<input type="hidden" name="{$action}" value="UpdLink" />
		<input type="hidden" name="gid" value="{$link[l].link_group_id}" />
		<table>
		<tr>
			<th colspan="3">
				{$link[l].group_name}
				<hr />
			</th>
		</td>
		<tr>
			<th>Link</th>
			<th>Group</th>
			<th rowspan="3">Description</th>
		</tr>
		<tr>
			<th colspan="2">URL</th>
		</tr>
		<tr>
			<th colspan="2">Info</th>
		</tr>
		<tr>
			<th colspan="3"><hr /></th>
		</tr>
	{/if}
	{assign var=lid value=$link[l].link_id}
	<tr valign="top">
		<td>
			<input type="hidden" name="links[]" value="{$lid}" />
			<input type="text" name="name{$lid}" value="{$link[l].name}" size="25" /><br />
			<a href="{$link[l].url}">{$link[l].name}</a>
		</td>
		<td>
			<input type="hidden" name="old_link_group{$lid}" value="{$link[l].link_group_id}" />
			<select name="link_group{$lid}">
			{html_options options=$group_opt selected=$link[l].link_group_id}
			</select><br />
			<a href="{$action_link}&amp;{$action}=DelLink&amp;lid={$lid}">Delete</a>
		</td>
		<td rowspan="3">
			<textarea name="link_desc{$lid}" rows="5" cols="40">{$link[l].link_desc}</textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="text" name="url{$lid}" value="{$link[l].url}" size="45" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			Created: {$link[l].date_crtd}<br />
			Updated: {$link[l].date_last_chngd}
		</td>
	</tr>
	<tr>
		<td colspan="3" align="center">
		{assign var=gid value=$link[l].link_group_id}
		<a href="{$action_link}?{$action}=DelLink&amp;lid={$lid}&amp;gid={$gid}">Delete</a>
		{if $link|@count gt 1}
			<br />
			{if !$smarty.section.l.first}
				<a href="{$action_link}?{$action}=OrdLink&amp;lid={$lid}&amp;ord=1&amp;gid={$gid}">Top</a>
				{if $smarty.section.l.index gt 1}
					<a href="{$action_link}?{$action}=OrdLink&amp;lid={$lid}&amp;ord={$link[l].link_ord|mm}&amp;gid={$gid}">Up</a>
				{/if}
			{/if}
			{if !$smarty.section.l.last}
				{if $smarty.section.l.rownum lt $link|@count|mm}
					<a href="{$action_link}?{$action}=OrdLink&amp;lid={$lid}&amp;ord={$link[l].link_ord|pp}&amp;gid={$gid}">Down</a>
				{/if}
				<a href="{$action_link}?{$action}=OrdLink&amp;lid={$lid}&amp;ord={$link|@count}&amp;gid={$gid}">Bottom</a>
			{/if}
		{/if}
		</td>
	</tr>
	{if $smarty.section.l.last}
		<tr>
			<th colspan="3">
				<input type="submit" value="Update" />
			</th>
		</table>
		</div>
		</form>
	{else}
		<tr>
			<th colspan="3"><hr /></th>
		</tr>
	{/if}
{sectionelse}
	<h2>Warning</h2>
	<p>There are no links to display for this group!</p>
{/section}
{include file="footer.tpl"}
