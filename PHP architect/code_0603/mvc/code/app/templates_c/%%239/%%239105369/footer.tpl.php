<?php /* Smarty version 2.5.0, created on 2003-05-22 06:02:44
         compiled from footer.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'date_format', 'footer.tpl', 8, false),
array('function', 'debug', 'footer.tpl', 21, false),)); ?><?php if ('index' != $this->_tpl_vars['view']): ?>
	<p><a href="<?php echo $this->_tpl_vars['view_link']; ?>
index">Return to Group List</a></p>
<?php endif; ?>
<?php if ($this->_tpl_vars['admin'] && 'groupedit' != $this->_tpl_vars['view']): ?>
	<p><a href="<?php echo $this->_tpl_vars['view_link']; ?>
groupedit">Edit</a></p>
<?php endif; ?>
<hr />
<p class="fineprint">Page Generated on <?php echo $this->_run_mod_handler('date_format', true, time(), "%A, %B %d, %Y"); ?>
</p>
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
<?php if ($this->_tpl_vars['debug']): ?>
	<pre style="text-align:left"><?php echo $this->_tpl_vars['test']; ?>
</pre>
	<?php echo $this->_plugins['function']['debug'][0](array('output' => 'HTML'), $this) ; ?>

<?php endif; ?>
</div>
</body>
</html>