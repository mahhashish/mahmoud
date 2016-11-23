<?php
	session_start();
?>
<html>
<body>

<font size=1>
<?php
	foreach ($_SESSION['args'] as $k=>$v)
		echo "$k: $v<br>";
?>
</font>
</body>
</html>

