{include file="header.tpl"}
<h2>Add New Link</h2>
{if $group|@count gt 0}
<form action="{$action_link}" method="post">
<div>
<input type="hidden" name="{$action}" value="AddLink" />
<table>
<tr>
	<th>Group:</th>
	<td>
		<select name="link_group">
		{html_options options=$group_opt}
		</select>
	</td>
</tr>
<tr>
	<th>Name:</th>
	<td><input type="text" name="name" size="45" /></td>
</tr>
<tr>
	<th>URL:</th>
	<td><input type="text" name="url" size="65" /></td>
</tr>
<tr>
	<th>Description:</th>
	<td><textarea name="link_desc" rows="3" cols="55"></textarea></td>
</tr>
<tr>
	<th colspan="2" align="center">
		<input type="submit" value="Add Link" />
	</th>
</tr>
</table>
</form>
</div>
{else}
<p>
Define some groups so you can add links.
</p>
{/if}
<h2>Editing Link Groups</h2>
{section name=g loop=$group}
{assign var=gid value=$group[g].link_group_id}
	{if $smarty.section.g.first}
		<form action="{$action_link}" method="post">
		<div>
		<table>
	{/if}
		<tr>
			<th colspan="2">
				{if $smarty.section.g.first}
					<input type="hidden" name="{$action}" value="UpdGroup" />
				{else}
					<hr />
				{/if}
				<h2>{$group[g].group_name}</h2>
			</th>
		</tr>
	<tr>
		<th>Name:</th>
		<td>
			<input type="hidden" name="groups[]" value="{$gid}" />
			<input type="text" name="group_name{$gid}" value="{$group[g].group_name}" size="45" max="50" />
		</td>
	</tr>
	<tr>
		<th>Description:</th>
		<td><textarea name="group_desc{$gid}" rows="4" cols="40">{$group[g].group_desc}</textarea></td>
	</tr>
	<tr>
		<th>Links ({$group[g].link_cnt}):</th>
		<td>
			{if $group[g].link_cnt eq 0}
				<a href="{$action_link}?{$action}=DelGroup&amp;gid={$gid}">Delete</a>
			{else}
				<a href="{$view_link}linkedit&amp;gid={$gid}">Edit</a>
			{/if}
			{section name=l loop=$link[g]}
			{if $smarty.section.l.first}<br />({/if}
		 		<a href="{$link[g][l].url}">{$link[g][l].name}</a>
		 	{if $smarty.section.l.last}){else},	{/if}
			{/section}
		 </td>
	</tr>
	<tr>
		<th>Order:</th>
		<td>
			{if !$smarty.section.g.first}
				<a href="{$action_link}?{$action}=OrdGroup&amp;ord=1&amp;gid={$gid}">Move to Top</a>
			{/if}
			{if !$smarty.section.g.last}
				<a href="{$action_link}?{$action}=OrdGroup&amp;ord={$group|@count}&amp;gid={$gid}">Move to Bottom</a>
			{/if}
		</td>
	</tr>
	{if $smarty.section.g.last}
		<tr>
			<th colspan="2">
				<hr />
				<input type="submit" value="Update" />
			</th>
		</tr>
		</table>
		</div>
		</form>
	{/if}
{sectionelse}
	<h2>Warning</h2>
	<p>There are no groups, please add one.</p>
{/section}
<h2>Add New Group</h2>
<form action="{$action_link}" method="post">
<div>
<input type="hidden" name="{$action}" value="AddGroup" />
<table>
	<tr>
		<th>Name:</th>
		<td>
			<input type="text" name="group_name" value="{$group[g].group_name}" size="45" max="50" />
		</td>
	</tr>
	<tr>
		<th>Description:</th>
		<td><textarea name="group_desc" rows="4" cols="40">{$group[g].group_desc}</textarea></td>
	</tr>
	<tr>
		<th colspan="2">
			<hr />
			<input type="submit" value="Add" />
		</th>
	</tr>
</table>
</div>
</form>
{include file="footer.tpl"}
