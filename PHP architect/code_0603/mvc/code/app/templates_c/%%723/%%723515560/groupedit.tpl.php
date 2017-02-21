<?php /* Smarty version 2.5.0, created on 2003-05-22 06:02:49
         compiled from groupedit.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'count', 'groupedit.tpl', 3, false),
array('function', 'html_options', 'groupedit.tpl', 12, false),
array('function', 'assign', 'groupedit.tpl', 43, false),)); ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<h2>Add New Link</h2>
<?php if ($this->_run_mod_handler('count', false, $this->_tpl_vars['group']) > 0): ?>
<form action="<?php echo $this->_tpl_vars['action_link']; ?>
" method="post">
<div>
<input type="hidden" name="<?php echo $this->_tpl_vars['action']; ?>
" value="AddLink" />
<table>
<tr>
	<th>Group:</th>
	<td>
		<select name="link_group">
		<?php echo $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['group_opt']), $this) ; ?>

		</select>
	</td>
</tr>
<tr>
	<th>Name:</th>
	<td><input type="text" name="name" size="45" /></td>
</tr>
<tr>
	<th>URL:</th>
	<td><input type="text" name="url" size="65" /></td>
</tr>
<tr>
	<th>Description:</th>
	<td><textarea name="link_desc" rows="3" cols="55"></textarea></td>
</tr>
<tr>
	<th colspan="2" align="center">
		<input type="submit" value="Add Link" />
	</th>
</tr>
</table>
</form>
</div>
<?php else: ?>
<p>
Define some groups so you can add links.
</p>
<?php endif; ?>
<h2>Editing Link Groups</h2>
<?php if (isset($this->_sections['g'])) unset($this->_sections['g']);
$this->_sections['g']['name'] = 'g';
$this->_sections['g']['loop'] = is_array($this->_tpl_vars['group']) ? count($this->_tpl_vars['group']) : max(0, (int)$this->_tpl_vars['group']);
$this->_sections['g']['show'] = true;
$this->_sections['g']['max'] = $this->_sections['g']['loop'];
$this->_sections['g']['step'] = 1;
$this->_sections['g']['start'] = $this->_sections['g']['step'] > 0 ? 0 : $this->_sections['g']['loop']-1;
if ($this->_sections['g']['show']) {
    $this->_sections['g']['total'] = $this->_sections['g']['loop'];
    if ($this->_sections['g']['total'] == 0)
        $this->_sections['g']['show'] = false;
} else
    $this->_sections['g']['total'] = 0;
if ($this->_sections['g']['show']):

            for ($this->_sections['g']['index'] = $this->_sections['g']['start'], $this->_sections['g']['iteration'] = 1;
                 $this->_sections['g']['iteration'] <= $this->_sections['g']['total'];
                 $this->_sections['g']['index'] += $this->_sections['g']['step'], $this->_sections['g']['iteration']++):
$this->_sections['g']['rownum'] = $this->_sections['g']['iteration'];
$this->_sections['g']['index_prev'] = $this->_sections['g']['index'] - $this->_sections['g']['step'];
$this->_sections['g']['index_next'] = $this->_sections['g']['index'] + $this->_sections['g']['step'];
$this->_sections['g']['first']      = ($this->_sections['g']['iteration'] == 1);
$this->_sections['g']['last']       = ($this->_sections['g']['iteration'] == $this->_sections['g']['total']);
?>
<?php echo $this->_plugins['function']['assign'][0](array('var' => 'gid','value' => $this->_tpl_vars['group'][$this->_sections['g']['index']]['link_group_id']), $this) ; ?>

	<?php if ($this->_sections['g']['first']): ?>
		<form action="<?php echo $this->_tpl_vars['action_link']; ?>
" method="post">
		<div>
		<table>
	<?php endif; ?>
		<tr>
			<th colspan="2">
				<?php if ($this->_sections['g']['first']): ?>
					<input type="hidden" name="<?php echo $this->_tpl_vars['action']; ?>
" value="UpdGroup" />
				<?php else: ?>
					<hr />
				<?php endif; ?>
				<h2><?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['group_name']; ?>
</h2>
			</th>
		</tr>
	<tr>
		<th>Name:</th>
		<td>
			<input type="hidden" name="groups[]" value="<?php echo $this->_tpl_vars['gid']; ?>
" />
			<input type="text" name="group_name<?php echo $this->_tpl_vars['gid']; ?>
" value="<?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['group_name']; ?>
" size="45" max="50" />
		</td>
	</tr>
	<tr>
		<th>Description:</th>
		<td><textarea name="group_desc<?php echo $this->_tpl_vars['gid']; ?>
" rows="4" cols="40"><?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['group_desc']; ?>
</textarea></td>
	</tr>
	<tr>
		<th>Links (<?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['link_cnt']; ?>
):</th>
		<td>
			<?php if ($this->_tpl_vars['group'][$this->_sections['g']['index']]['link_cnt'] == 0): ?>
				<a href="<?php echo $this->_tpl_vars['action_link']; ?>
?<?php echo $this->_tpl_vars['action']; ?>
=DelGroup&amp;gid=<?php echo $this->_tpl_vars['gid']; ?>
">Delete</a>
			<?php else: ?>
				<a href="<?php echo $this->_tpl_vars['view_link']; ?>
linkedit&amp;gid=<?php echo $this->_tpl_vars['gid']; ?>
">Edit</a>
			<?php endif; ?>
			<?php if (isset($this->_sections['l'])) unset($this->_sections['l']);
$this->_sections['l']['name'] = 'l';
$this->_sections['l']['loop'] = is_array($this->_tpl_vars['link'][$this->_sections['g']['index']]) ? count($this->_tpl_vars['link'][$this->_sections['g']['index']]) : max(0, (int)$this->_tpl_vars['link'][$this->_sections['g']['index']]);
$this->_sections['l']['show'] = true;
$this->_sections['l']['max'] = $this->_sections['l']['loop'];
$this->_sections['l']['step'] = 1;
$this->_sections['l']['start'] = $this->_sections['l']['step'] > 0 ? 0 : $this->_sections['l']['loop']-1;
if ($this->_sections['l']['show']) {
    $this->_sections['l']['total'] = $this->_sections['l']['loop'];
    if ($this->_sections['l']['total'] == 0)
        $this->_sections['l']['show'] = false;
} else
    $this->_sections['l']['total'] = 0;
if ($this->_sections['l']['show']):

            for ($this->_sections['l']['index'] = $this->_sections['l']['start'], $this->_sections['l']['iteration'] = 1;
                 $this->_sections['l']['iteration'] <= $this->_sections['l']['total'];
                 $this->_sections['l']['index'] += $this->_sections['l']['step'], $this->_sections['l']['iteration']++):
$this->_sections['l']['rownum'] = $this->_sections['l']['iteration'];
$this->_sections['l']['index_prev'] = $this->_sections['l']['index'] - $this->_sections['l']['step'];
$this->_sections['l']['index_next'] = $this->_sections['l']['index'] + $this->_sections['l']['step'];
$this->_sections['l']['first']      = ($this->_sections['l']['iteration'] == 1);
$this->_sections['l']['last']       = ($this->_sections['l']['iteration'] == $this->_sections['l']['total']);
?>
			<?php if ($this->_sections['l']['first']): ?><br />(<?php endif; ?>
		 		<a href="<?php echo $this->_tpl_vars['link'][$this->_sections['g']['index']][$this->_sections['l']['index']]['url']; ?>
"><?php echo $this->_tpl_vars['link'][$this->_sections['g']['index']][$this->_sections['l']['index']]['name']; ?>
</a>
		 	<?php if ($this->_sections['l']['last']): ?>)<?php else: ?>,	<?php endif; ?>
			<?php endfor; endif; ?>
		 </td>
	</tr>
	<tr>
		<th>Order:</th>
		<td>
			<?php if (! $this->_sections['g']['first']): ?>
				<a href="<?php echo $this->_tpl_vars['action_link']; ?>
?<?php echo $this->_tpl_vars['action']; ?>
=OrdGroup&amp;ord=1&amp;gid=<?php echo $this->_tpl_vars['gid']; ?>
">Move to Top</a>
			<?php endif; ?>
			<?php if (! $this->_sections['g']['last']): ?>
				<a href="<?php echo $this->_tpl_vars['action_link']; ?>
?<?php echo $this->_tpl_vars['action']; ?>
=OrdGroup&amp;ord=<?php echo $this->_run_mod_handler('count', false, $this->_tpl_vars['group']); ?>
&amp;gid=<?php echo $this->_tpl_vars['gid']; ?>
">Move to Bottom</a>
			<?php endif; ?>
		</td>
	</tr>
	<?php if ($this->_sections['g']['last']): ?>
		<tr>
			<th colspan="2">
				<hr />
				<input type="submit" value="Update" />
			</th>
		</tr>
		</table>
		</div>
		</form>
	<?php endif; ?>
<?php endfor; else: ?>
	<h2>Warning</h2>
	<p>There are no groups, please add one.</p>
<?php endif; ?>
<h2>Add New Group</h2>
<form action="<?php echo $this->_tpl_vars['action_link']; ?>
" method="post">
<div>
<input type="hidden" name="<?php echo $this->_tpl_vars['action']; ?>
" value="AddGroup" />
<table>
	<tr>
		<th>Name:</th>
		<td>
			<input type="text" name="group_name" value="<?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['group_name']; ?>
" size="45" max="50" />
		</td>
	</tr>
	<tr>
		<th>Description:</th>
		<td><textarea name="group_desc" rows="4" cols="40"><?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['group_desc']; ?>
</textarea></td>
	</tr>
	<tr>
		<th colspan="2">
			<hr />
			<input type="submit" value="Add" />
		</th>
	</tr>
</table>
</div>
</form>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>