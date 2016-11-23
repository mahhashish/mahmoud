<?php session_start(); ?>
<html>
<body onload="mainform = document.mainform; p = mainform.p; z1 = mainform.z1; z2 = mainform.z2">

<script language=javascript>
	function previouspage()
	{
		if (p.value > 1)
		{
			p.value--;
			mainform.submit();
		}
	}
	function nextpage()
	{
		if (p.value < <?= $_SESSION['args']['Pages'] ?>)
		{
			p.value++;
			mainform.submit();
		}
	}
	function setpage(pg)
	{
		p.value = pg;
		mainform.submit();
	}

	function zoomout()
	{
		if (z1.value > 1)
		{
			z2.value = z1.value;
			z1.value--;
			mainform.submit();
			z2.value = z1.value;
		}
	}

	function zoomin()
	{
		if (z1.value < <?= $_SESSION['max_zoom_level'] ?>)
		{
			z2.value = z1.value;
			z1.value++;
			mainform.submit();
			z2.value = z1.value;
		}
	}
</script>

<form name=mainform action=view.php method=get target=downtarget>
	<input type=hidden name="p" value=1>
	<input type=hidden name="z1" value=1>
	<input type=hidden name="z2" value=1>
</form>

<a href="javascript:zoomin()">Zoom In</a>
<a href="javascript:zoomout()">Zoom Out</a>

<a href="javascript:previouspage();">Previous</a>
<?php

	for ($i = 1; $i <= $_SESSION['args']['Pages']; $i++)
		echo "<a href='javascript:setpage($i);'>Page $i</a><br>"

?>
<a href="javascript:nextpage();">Next</a>
</body>
</html>

