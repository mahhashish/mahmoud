<?php /* Smarty version 2.5.0, created on 2003-05-22 06:03:06
         compiled from linkedit.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'assign', 'linkedit.tpl', 30, false),
array('function', 'html_options', 'linkedit.tpl', 40, false),
array('modifier', 'count', 'linkedit.tpl', 63, false),
array('modifier', 'mm', 'linkedit.tpl', 68, false),
array('modifier', 'pp', 'linkedit.tpl', 73, false),)); ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if (isset($this->_sections['l'])) unset($this->_sections['l']);
$this->_sections['l']['name'] = 'l';
$this->_sections['l']['loop'] = is_array($this->_tpl_vars['link']) ? count($this->_tpl_vars['link']) : max(0, (int)$this->_tpl_vars['link']);
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
	<?php if ($this->_sections['l']['first']): ?>
		<form action="<?php echo $this->_tpl_vars['action_link']; ?>
" method="post">
		<div>
		<input type="hidden" name="<?php echo $this->_tpl_vars['action']; ?>
" value="UpdLink" />
		<input type="hidden" name="gid" value="<?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['link_group_id']; ?>
" />
		<table>
		<tr>
			<th colspan="3">
				<?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['group_name']; ?>

				<hr />
			</th>
		</td>
		<tr>
			<th>Link</th>
			<th>Group</th>
			<th rowspan="3">Description</th>
		</tr>
		<tr>
			<th colspan="2">URL</th>
		</tr>
		<tr>
			<th colspan="2">Info</th>
		</tr>
		<tr>
			<th colspan="3"><hr /></th>
		</tr>
	<?php endif; ?>
	<?php echo $this->_plugins['function']['assign'][0](array('var' => 'lid','value' => $this->_tpl_vars['link'][$this->_sections['l']['index']]['link_id']), $this) ; ?>

	<tr valign="top">
		<td>
			<input type="hidden" name="links[]" value="<?php echo $this->_tpl_vars['lid']; ?>
" />
			<input type="text" name="name<?php echo $this->_tpl_vars['lid']; ?>
" value="<?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['name']; ?>
" size="25" /><br />
			<a href="<?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['url']; ?>
"><?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['name']; ?>
</a>
		</td>
		<td>
			<input type="hidden" name="old_link_group<?php echo $this->_tpl_vars['lid']; ?>
" value="<?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['link_group_id']; ?>
" />
			<select name="link_group<?php echo $this->_tpl_vars['lid']; ?>
">
			<?php echo $this->_plugins['function']['html_options'][0](array('options' => $this->_tpl_vars['group_opt'],'selected' => $this->_tpl_vars['link'][$this->_sections['l']['index']]['link_group_id']), $this) ; ?>

			</select><br />
			<a href="<?php echo $this->_tpl_vars['action_link']; ?>
&amp;<?php echo $this->_tpl_vars['action']; ?>
=DelLink&amp;lid=<?php echo $this->_tpl_vars['lid']; ?>
">Delete</a>
		</td>
		<td rowspan="3">
			<textarea name="link_desc<?php echo $this->_tpl_vars['lid']; ?>
" rows="5" cols="40"><?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['link_desc']; ?>
</textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="text" name="url<?php echo $this->_tpl_vars['lid']; ?>
" value="<?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['url']; ?>
" size="45" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			Created: <?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['date_crtd']; ?>
<br />
			Updated: <?php echo $this->_tpl_vars['link'][$this->_sections['l']['index']]['date_last_chngd']; ?>

		</td>
	</tr>
	<tr>
		<td colspan="3" align="center">
		<?php echo $this->_plugins['function']['assign'][0](array('var' => 'gid','value' => $this->_tpl_vars['link'][$this->_sections['l']['index']]['link_group_id']), $this) ; ?>

		<a href="<?php echo $this->_tpl_vars['action_link']; ?>
?<?php echo $this->_tpl_vars['action']; ?>
=DelLink&amp;lid=<?php echo $this->_tpl_vars['lid']; ?>
&amp;gid=<?php echo $this->_tpl_vars['gid']; ?>
">Delete</a>
		<?php if ($this->_run_mod_handler('count', false, $this->_tpl_vars['link']) > 1): ?>
			<br />
			<?php if (! $this->_sections['l']['first']): ?>
				<a href="<?php echo $this->_tpl_vars['action_link']; ?>
?<?php echo $this->_tpl_vars['action']; ?>
=OrdLink&amp;lid=<?php echo $this->_tpl_vars['lid']; ?>
&amp;ord=1&amp;gid=<?php echo $this->_tpl_vars['gid']; ?>
">Top</a>
				<?php if ($this->_sections['l']['index'] > 1): ?>
					<a href="<?php echo $this->_tpl_vars['action_link']; ?>
?<?php echo $this->_tpl_vars['action']; ?>
=OrdLink&amp;lid=<?php echo $this->_tpl_vars['lid']; ?>
&amp;ord=<?php echo $this->_run_mod_handler('mm', true, $this->_tpl_vars['link'][$this->_sections['l']['index']]['link_ord']); ?>
&amp;gid=<?php echo $this->_tpl_vars['gid']; ?>
">Up</a>
				<?php endif; ?>
			<?php endif; ?>
			<?php if (! $this->_sections['l']['last']): ?>
				<?php if ($this->_sections['l']['rownum'] < $this->_run_mod_handler('mm', true, $this->_run_mod_handler('count', false, $this->_tpl_vars['link']))): ?>
					<a href="<?php echo $this->_tpl_vars['action_link']; ?>
?<?php echo $this->_tpl_vars['action']; ?>
=OrdLink&amp;lid=<?php echo $this->_tpl_vars['lid']; ?>
&amp;ord=<?php echo $this->_run_mod_handler('pp', true, $this->_tpl_vars['link'][$this->_sections['l']['index']]['link_ord']); ?>
&amp;gid=<?php echo $this->_tpl_vars['gid']; ?>
">Down</a>
				<?php endif; ?>
				<a href="<?php echo $this->_tpl_vars['action_link']; ?>
?<?php echo $this->_tpl_vars['action']; ?>
=OrdLink&amp;lid=<?php echo $this->_tpl_vars['lid']; ?>
&amp;ord=<?php echo $this->_run_mod_handler('count', false, $this->_tpl_vars['link']); ?>
&amp;gid=<?php echo $this->_tpl_vars['gid']; ?>
">Bottom</a>
			<?php endif; ?>
		<?php endif; ?>
		</td>
	</tr>
	<?php if ($this->_sections['l']['last']): ?>
		<tr>
			<th colspan="3">
				<input type="submit" value="Update" />
			</th>
		</table>
		</div>
		</form>
	<?php else: ?>
		<tr>
			<th colspan="3"><hr /></th>
		</tr>
	<?php endif; ?>
<?php endfor; else: ?>
	<h2>Warning</h2>
	<p>There are no links to display for this group!</p>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>