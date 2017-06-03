<?PHP

$MysqlHost = "localhost";
$MysqlUser = "testuser";
$MysqlPass = "testpw";
$MysqlDb = "testdb";
$CustomerID = 1; // for testing

mysql_connect ("$MysqlHost", "$MysqlUser", "$MysqlPass");
mysql_select_db ("$MysqlDb");

$CustomerQueryString = "SELECT * FROM customers WHERE id LIKE '$CustomerID'";
$CustomerQuery = mysql_query ($CustomerQueryString);
$Customer = mysql_fetch_array ($CustomerQuery);

mysql_close ();

/**
 *	Echoes $Line with an appended new-line character;
 * 	helps clean up PHP code as well as parser output
 */
function PrintLn ($Line) {
	echo $Line, "\n";	
}

/**
 *	Echoes $Line to $Socket with an appended new-line character;
 *	helps clean up PHP code and adds necessary new-line separator to socket output
 */
function PrintSoc ($Socket, $Line) {
	fputs ($Socket, $Line . "\n");
}

if ($_POST ['Submit']) {
	$ServerConnection = fsockopen ($_POST ['Server'], 64401, $ErrNu, $ErrStr);
	if (!$ServerConnection) {
		PrintLn ("$ErrStr ($ErrNu)");
	}
	else {
		PrintSoc ($ServerConnection, ?PHP|ARCH?); // pass-phrase
		PrintSoc ($ServerConnection, $Customer ['Domain']);
		PrintSoc ($ServerConnection, $Customer ['ServicePlan']);
		PrintSoc ($ServerConnection, $Customer ['Email']);
		PrintSoc ($ServerConnection, $Customer ['Username']);
		PrintSoc ($ServerConnection, $Customer ['Password']);

		while (!feof ($ServerConnection)) {
			PrintLn (fgets ($fp, 128));
		}

		fclose ($ServerConnection);
	}
}

PrintLn ("<HTML>");
PrintLn ("<HEAD><TITLE>PHP Client</TITLE></HEAD>");
PrintLn ("<BODY>");

PrintLn ("<FORM method=POST action=client.php>");

PrintLn ("<TABLE cellPadding=2 cellSpacing=0 border=0>");
PrintLn ("<TR><TD>Domain</TD>		<TD>" . $Customer ['Domain'] . "</TD></TR>");
PrintLn ("<TR><TD>Service plan</TD>	<TD>" . $Customer ['ServicePlan'] . "</TD></TR>");
PrintLn ("<TR><TD>E-mail</TD>		<TD>" . $Customer ['Email'] . "</TD></TR>");
PrintLn ("<TR><TD>Username</TD>		<TD>" . $Customer ['Username'] . "</TD></TR>");
PrintLn ("<TR><TD>Password</TD>		<TD>" . $Customer ['Password'] . "</TD></TR>");

PrintLn ("<TR><TD>&nbsp;</TD>		<TD>");
PrintLn ("					<SELECT name=Server size=1>");
PrintLn ("					<OPTION value=10.0.0.1>Server 1</OPTION>");
PrintLn ("					<OPTION value=10.0.0.2>Server 2</OPTION>");
PrintLn ("					<OPTION value=10.0.0.3>Server 3</OPTION>");
PrintLn ("					</SELECT>");
PrintLn ("					</TD></TR>");

PrintLn ("<TR><TD>&nbsp;</TD>		<TD><INPUT type=submit name=Submit value=Submit></TD></TR>");
PrintLn ("</TABLE>");

PrintLn ("</FORM>");

PrintLn ("</BODY>");
PrintLn ("</HTML>");

?>
