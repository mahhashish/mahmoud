{if "index" neq $view}
	<p><a href="{$view_link}index">Return to Group List</a></p>
{/if}
{if $admin && "groupedit" neq $view}
	<p><a href="{$view_link}groupedit">Edit</a></p>
{/if}
<hr />
<p class="fineprint">Page Generated on {$smarty.now|date_format:"%A, %B %d, %Y"}</p>
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
{if $debug}
	<pre style="text-align:left">{$test}</pre>
	{debug output=HTML}
{/if}
</div>
</body>
</html>
