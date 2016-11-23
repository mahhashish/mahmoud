{include file="open.tpl"}
{literal}
<script language="JavaScript">
function clickFolder(id)
{
	if ( document.getElementById && document.getElementById(id) !== null )
	{
		alert('true' + id);
	}
	else
	{
		alert('false');
	}
}
</script>
{/literal}
{$folders}
{include file="close.tpl"}
