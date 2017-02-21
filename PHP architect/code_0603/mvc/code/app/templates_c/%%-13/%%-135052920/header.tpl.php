<?php /* Smarty version 2.5.0, created on 2003-05-22 06:02:44
         compiled from header.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'count', 'header.tpl', 21, false),)); ?><?php echo '<?xml'; ?>
 version="1.0" encoding="UTF-8"<?php echo '?>'; ?>

<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php $this->config_load("links.conf", null, 'global'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php if ($this->_tpl_vars['title_extra']): ?>
<?php echo $this->_config[0]['vars']['short_site_title']; ?>
 - <?php echo $this->_tpl_vars['title_extra']; ?>

<?php else: ?>
<?php echo $this->_config[0]['vars']['site_title']; ?>
 Website
<?php endif; ?>
</title>
<link rel="stylesheet" type="text/css" href="links.css"></link>
</head>
<body>
<div id="main">
<h1><?php echo $this->_config[0]['vars']['site_title']; ?>
</h1>
<?php if (is_array ( $this->_tpl_vars['errors'] ) && $this->_run_mod_handler('count', false, $this->_tpl_vars['errors']) > 0): ?>
	<br />
	<div class="errormsg">
	<h2>Attention:</h2>
	<ul>
	<?php if (isset($this->_sections['e'])) unset($this->_sections['e']);
$this->_sections['e']['name'] = 'e';
$this->_sections['e']['loop'] = is_array($this->_tpl_vars['errors']) ? count($this->_tpl_vars['errors']) : max(0, (int)$this->_tpl_vars['errors']);
$this->_sections['e']['show'] = true;
$this->_sections['e']['max'] = $this->_sections['e']['loop'];
$this->_sections['e']['step'] = 1;
$this->_sections['e']['start'] = $this->_sections['e']['step'] > 0 ? 0 : $this->_sections['e']['loop']-1;
if ($this->_sections['e']['show']) {
    $this->_sections['e']['total'] = $this->_sections['e']['loop'];
    if ($this->_sections['e']['total'] == 0)
        $this->_sections['e']['show'] = false;
} else
    $this->_sections['e']['total'] = 0;
if ($this->_sections['e']['show']):

            for ($this->_sections['e']['index'] = $this->_sections['e']['start'], $this->_sections['e']['iteration'] = 1;
                 $this->_sections['e']['iteration'] <= $this->_sections['e']['total'];
                 $this->_sections['e']['index'] += $this->_sections['e']['step'], $this->_sections['e']['iteration']++):
$this->_sections['e']['rownum'] = $this->_sections['e']['iteration'];
$this->_sections['e']['index_prev'] = $this->_sections['e']['index'] - $this->_sections['e']['step'];
$this->_sections['e']['index_next'] = $this->_sections['e']['index'] + $this->_sections['e']['step'];
$this->_sections['e']['first']      = ($this->_sections['e']['iteration'] == 1);
$this->_sections['e']['last']       = ($this->_sections['e']['iteration'] == $this->_sections['e']['total']);
?>
		<li><?php echo $this->_tpl_vars['errors'][$this->_sections['e']['index']]; ?>
</li>
	<?php endfor; endif; ?>
	</ul>
	</div>
	<br />
<?php endif; ?>
<hr />