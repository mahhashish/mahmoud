<html>
<head>
<meta name="ROBOTS" content="NOINDEX, FOLLOW">
</head>
<body>
This is a general index of the php|architect new engine. This file should be used exclusively by robots and search engines.<p>
<p>
<?
$conn = mysql_connect ('myserver', 'myuser', 'mypwd');
mysql_select_db ('mydb');
$rs = mysql_query ('select id, title from stories order by date_published desc');
                                                                                                                                                             
while ($a = mysql_fetch_assoc ($rs))
{
  echo "<a href='" . BASE_URL . "/news/{$a['newsid']}'>{$a['title']}</a><br>";
}
                                                                                                                                                             
?>
                                                                                                                                                             
</body>
</html>

