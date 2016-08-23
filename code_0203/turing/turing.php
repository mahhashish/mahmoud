<html>
<body>
<center><h1>Marco's Turing Test</h1>
<h2>Are you human?</h2></center>

<?php

	// Generate a Turing Test image and output it to the browser

	function GenerateImage($token)
	{
		$iFont = 5;	// Font ID
		$iSpacing = 2;	// Spacing between characters

		// Establish font metric and image size

		$iCharWidth = ImageFontWidth ($iFont);
		$iCharHeight = ImageFontHeight ($iFont);
		$iWidth = strlen($token) * ($iCharWidth + $iSpacing);
		$iHeight = $iCharHeight;

		// Create the image
	
		$pic = ImageCreate ($iWidth, $iHeight); 

		// Allocate a background and foreground colour

		$col = ImageColorAllocate ($pic, 200, 200, 200); 
		$col2 = ImageColorAllocate ($pic, 0, 0, 100); 

		$iX=1;

		for ($i=0; $i < strlen ($token); $i++)
		{
			ImageChar ($pic, $iFont, $iX, 0, $token[$i], $col2);
			$iX += $iCharWidth + $iSpacing;
		}

		ob_start();
		ImageJPEG($pic); 
		$data = ob_get_contents();
		ob_clean();
		ImageDestroy($pic); 

		return $data;
	}

function AdministerTest ($error = false)
{
	// Generate a six-digit random string

	$token = (string) rand (100000, 999999);

	// Output form to the user

	if ($error)
		echo '<b>ERROR! You\'re not human!</b><p />';
?>
	<img src="data:image/jpeg;base64,<?= base64_encode (GenerateImage ($token)) ?>">

	<form action="<?= $_SERVER['PHP_SELF'] ?>" method=post>
	<input type="hidden" name="tok" value="<?= md5 ($token) ?>">
	Please enter the combination you see in the string above:
	<input type="text" name="inp">
	<input type="submit">
	</form>
<?
}

function CheckResults()
{
	$token = $_POST['inp'];
	$hash = $_POST['tok'];

	if (md5 ($token) === $hash)
	{
	?>

	<b>Congratulations! You're either human or a really smart machine!

	<?
	}
	else
		AdministerTest (true);
}

// If the user has not POSTed the page, then
// administer the test. Otherwise, check the results.

if (strcmp ($_SERVER['REQUEST_METHOD'], 'POST'))
	AdministerTest();
else
	CheckResults();

?>

</body>
</html>

