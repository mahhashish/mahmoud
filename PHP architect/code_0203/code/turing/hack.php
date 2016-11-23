<?php

	// Read the test source HTML

	$f = fopen ('http://localhost/turing/code/turing_safe.php', 'r');
	$data = fread ($f, 500000);
	fclose ($f);

	// Extract the md5 hash of the token

	if (!ereg ('<input type="hidden" name="tok" value="([^"]*)">', $data, $token))
		die ("Unable to find token");

	$token = $token[1];

	// Extract the image for visual verification

	ereg ('(<img src="data:image/jpeg;base64,[^"]*">)', $data, $img);

	// Record start time

	$start = explode (' ', microtime());

	// Brute-force through all the possible combinations

	$result = 0;

	for ($i = 100000; $i <= 999999; $i++)
	{
		if (md5 ((string) $i) === $token)
		{
			$result = $i;
			break;
		}
	}

	// Record end time

	$end = explode (' ', microtime());

	// Brag!

?>

<html>
<body>
<?=$img[1]?><p />
<?php if ($result > 0) { ?>
Broken with <?= $result ?> in <?= ($end[0] + $end[1]) - ($start[0] + $start[1]) ?> seconds.
<?php } else { ?>
Unable to break. After all, I'm only... not human!
<?php } ?>
</body>
</html>
