<?php /* Smarty version 2.5.0, created on 2003-07-20 22:01:54
         compiled from menu.tmp */ ?>
<?php $this->_load_plugins(array(
array('function', 'counter', 'menu.tmp', 17, false),)); ?><html>
<head>
<link rel='stylesheet' href='styles.css' type='text/css'>
</head>
<body bgcolor='#FFFFFF'>
<table border=0 width=200 cellpadding=0 cellspacing=0>
<tr>
<td><img src='images/lborder.gif'></td>
<td align=center width=100% background='images/backing.gif' nowrap>
<span class='main_heading'><?php echo $this->_tpl_vars['heading']; ?>
</span> 
</td>
<td><img src='images/rborder.gif'></td>
</tr>
</table>
<table id='view' border=0 width=200 cellpadding=0 cellspacing=0>

<?php echo $this->_plugins['function']['counter'][0](array('start' => -1,'print' => false), $this) ; ?>

<?php if (count((array)$this->_tpl_vars['xml'])):
    foreach ((array)$this->_tpl_vars['xml'] as $this->_tpl_vars['key'] => $this->_tpl_vars['curr']):
?>
	<tr>
	<td width=20 id='c2' align=center><img src='images/cross.gif'></td>
	<td width=180 id='r' align=left><a href='<?php echo $GLOBALS['HTTP_GET_VARS']['page']; ?>
?click=<?php echo $this->_plugins['function']['counter'][0](array(), $this) ; ?>
&<?php echo $this->_tpl_vars['click']; ?>
'><span class='heading'><?php echo $this->_tpl_vars['key']; ?>
</span></a></td>
	</tr>
	<?php if (count((array)$this->_tpl_vars['curr'])):
    foreach ((array)$this->_tpl_vars['curr'] as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<?php if ($this->_tpl_vars['v']['display']): ?>
		<tr>
		<td width=20 id='c2' align=center><img src='images/spacer.gif'></td>
		<td width=180 id='r' align=left><img align=top src='<?php echo $this->_tpl_vars['v']['image']; ?>
'><a href='<?php echo $this->_tpl_vars['v']['href']; ?>
' target='info'><span class='item'><?php echo $this->_tpl_vars['v']['name']; ?>
</span></a></td>
		</tr>
		<?php endif; ?>
	<?php endforeach; endif; ?>
<?php endforeach; endif; ?>

<tr>
<td id='b_c2'><img src='images/spacer.gif'></td>
<td id='rb'><img src='images/spacer.gif' width=1 height=1></td>
</tr>

</table>
</body>
</html>