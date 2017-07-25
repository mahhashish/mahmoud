<?php //poorauthmenu.php ?>
<html>
<?php

if ($_GET['un'] == 'user' && $_GET['pw'] == 'pass') {
	echo "<form>\n" .
	"<input type=\"button\" value=\"Resource 1\"
		onClick=\"location.replace('poorauthresource.php?res=1');\">\n<br>\n"
	. "<input type=\"button\" value=\"Resource 2\"
			onClick=\"location.replace('poorauthresource.php?res=2');\">\n<br>\n"
	. "</form>\n";
} else {
	echo "Auth failed\n";
}

?>
</html>
