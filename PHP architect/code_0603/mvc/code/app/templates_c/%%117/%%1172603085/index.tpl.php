<?php /* Smarty version 2.5.0, created on 2003-05-22 06:02:44
         compiled from index.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'substr', 'index.tpl', 16, false),)); ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
	<?php if ($this->_sections['g']['first']): ?>
		<table>
		<tr>
			<th>Group</th>
			<th>Description</th>
			<th>Links</th>
			<th>Added/<br />Updated</th>
		</tr>
	<?php endif; ?>
	<tr>
		<td><a href="<?php echo $this->_tpl_vars['view_link']; ?>
list#<?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['link_group_id']; ?>
"><?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['group_name']; ?>
</a></td>
		<td><?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['group_desc']; ?>
</td>
		<td align="center"><?php echo $this->_tpl_vars['group'][$this->_sections['g']['index']]['link_cnt']; ?>
</td>
		<td><?php echo $this->_run_mod_handler('substr', true, $this->_tpl_vars['group'][$this->_sections['g']['index']]['link_add'], 0, 10); ?>
<br /><?php echo $this->_run_mod_handler('substr', true, $this->_tpl_vars['group'][$this->_sections['g']['index']]['link_upd'], 0, 10); ?>
</td>
	</tr>
	<?php if ($this->_sections['g']['last']): ?>
		</table>
	<?php endif; ?>
<?php endfor; else: ?>
	<h2>Warning</h2>
	<p>There are no groups with links, please contact the site administrator.</p>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>