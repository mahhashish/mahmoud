<html>
	<head>
		<title>Sting Functions</title>
	</head>
	<body>

	<?php
		$firstString = "The quick brown fox";
		$secondString = " jumped over the lazy dog.";
	?>
	
	<?php
		$thirdString = $firstString;
		$thirdString .= $secondString;
		echo $thirdString;
	?>
	</body>
</html>