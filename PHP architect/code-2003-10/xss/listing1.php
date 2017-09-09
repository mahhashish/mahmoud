<table>
	<tr>
		<th>Username</th>
		<th>Email</th>
	</tr>
<?
if ($_SESSION['admin_ind'])
{
	$sql = 'select username, email from users';
	$result = mysql_query($sql);
	while ($curr_user = mysql_fetch_assoc($result))
	{
		echo "\t<tr>\n";
		echo "\t\t<td>{$curr_user['username']}</td>\n";
		echo "\t\t<td>{$curr_user['email']}</td>\n";
		echo "\t</tr>\n";
	}
}
?>
</table>