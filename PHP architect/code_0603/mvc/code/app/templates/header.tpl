<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
{config_load file="links.conf" scope="global"}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>{if $title_extra}
{#short_site_title#} - {$title_extra}
{else}
{#site_title#} Website
{/if}
</title>
<link rel="stylesheet" type="text/css" href="links.css"></link>
</head>
<body>
<div id="main">
<h1>{#site_title#}</h1>
{if is_array($errors) and $errors|@count gt 0}
	<br />
	<div class="errormsg">
	<h2>Attention:</h2>
	<ul>
	{section name=e loop=$errors}
		<li>{$errors[e]}</li>
	{/section}
	</ul>
	</div>
	<br />
{/if}
<hr />
