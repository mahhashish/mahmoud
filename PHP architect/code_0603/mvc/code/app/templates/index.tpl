{include file="header.tpl"}
{section name=g loop=$group}
	{if $smarty.section.g.first}
		<table>
		<tr>
			<th>Group</th>
			<th>Description</th>
			<th>Links</th>
			<th>Added/<br />Updated</th>
		</tr>
	{/if}
	<tr>
		<td><a href="{$view_link}list#{$group[g].link_group_id}">{$group[g].group_name}</a></td>
		<td>{$group[g].group_desc}</td>
		<td align="center">{$group[g].link_cnt}</td>
		<td>{$group[g].link_add|substr:0:10}<br />{$group[g].link_upd|substr:0:10}</td>
	</tr>
	{if $smarty.section.g.last}
		</table>
	{/if}
{sectionelse}
	<h2>Warning</h2>
	<p>There are no groups with links, please contact the site administrator.</p>
{/section}
{include file="footer.tpl"}
