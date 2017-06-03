#!/usr/bin/php -q
<?PHP
$MysqlHost = "localhost";
$MysqlUser = "testuser";
$MysqlPass = "testpw";
$MysqlDb = "testdb";

mysql_connect ("$MysqlHost", "$MysqlUser", "$MysqlPass") or die ("Could not connect to database");
mysql_select_db ("$MysqlDb") or die ("Could not select database");

$ServerList = Array ('Server 1' => Array ('Name' => 'Server 1', 'IP' => '10.0.0.1', 'Port' => 64402),
		'Server 2' => Array ('Name' => 'Server 2', 'IP' => '10.0.0.2', 'Port' => 64402),
		'Server 3' => Array ('Name' => 'Server 3', 'IP' => '10.0.0.3', 'Port' => 64402)
		);

foreach ($ServerList as $Server) {
	$ServerConnection = fsockopen ($Server ['IP'], $Server ['Port'], $ErrNu, $ErrStr);
	if (!$ServerConnection) {
		PrintLn ("$ErrStr ($ErrNu)");
	}
	else {
		while (!feof ($ServerConnection)) {
			$ServerLoad = fgets ($ServerConnection, 128);
		}
		
		$TimeStamp = time ();
		$Query = "INSERT INTO ServerLog (Load, ServerName, Time) 
			  VALUES (\"$ServerLoad\", \"$Server[Name]\", \"$TimeStamp\")";
		mysql_query ($Query) or die ("Query failed");
		
		fclose ($ServerConnection);
	}
}
?>
