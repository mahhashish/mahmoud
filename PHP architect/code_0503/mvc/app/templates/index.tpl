<?xml version="1.0" encoding="US-ASCII"?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>Phrame - Example Application</title>
<style type="text/css">{literal}
img { border: white none 0px; }
{/literal}</style>
</head>
<body>
<form action="{$appl_link}" method="post">
<div>
<input type="hidden" name="{$action}" value="sayHello" />
{if $errors|@count gt 0}
	<ul>
	{section name=e loop=$errors}
		<li><b style="color: red">{$errors[e]}</b></li>
	{/section}
	</ul>
{/if}
What is your name?<br />
<input type="text" name="name" value="{$name}" />
<input type="submit" value="OK" />
</div>
</form>
<p>
<a href="http://validator.w3.org/check/referer">
<img src="valid-xhtml11.png"
     alt="Valid XHTML 1.1!" height="31" width="88" /></a>
&nbsp;
<a href="http://jigsaw.w3.org/css-validator/">
<img style="border:0;width:88px;height:31px"
     src="vcss.png" 
     alt="Valid CSS!" /></a>
</p>
</body>
</html>
