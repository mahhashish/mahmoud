{include file="header.tpl"}
{section name=g loop=$group}
	{if $smarty.section.g.first}
		<table>
	{/if}
	<tr>
		<th colspan="2">
			<a name="{$group[g].link_group_id}" />
			<h2>{$group[g].group_name}</h2>
			{$group[g].group_desc}
		</td>
	</tr>
	{section name=l loop=$link[g]}
	{if $smarty.section.l.first}
		<tr>
			<th>Link</th>
			<th>Description</th>
		</tr>
	{/if}
	<tr>
		<td>
			<a href="{$link[g][l].url}">{$link[g][l].name}</a>
		</td>
		<td>
			{$link[g][l].link_desc|default:"&nbsp;"}
		</td>
	</tr>
	{/section}
	{if $smarty.section.g.last}
		</table>
	{/if}
{sectionelse}
	<h2>Warning</h2>
	<p>There are no groups with links, please contact the site administrator.</p>
{/section}
{include file="footer.tpl"}
