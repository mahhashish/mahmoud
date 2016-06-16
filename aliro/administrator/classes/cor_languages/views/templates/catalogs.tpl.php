<?php
/**
* @package Mambo
* @subpackage Languages
* @copyright  Refer to copyright.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Mambo Foundation Inc see README.php
*/
 defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<script type="text/javascript">
function order(col, asc) {
    c = document.getElementById('col');
    a = document.getElementById('asc');
    c.value = col;
    a.value = asc;
    submitform('sort');
}
</script>

<script type="text/javascript" src="<?php echo $this->getCfg('live_site');?>/administrator/classes/cor_languages/languages.js"></script>

<input type="hidden" id="col" name="col" value="" />
<input type="hidden" id="asc" name="asc" value="" />
<input type="hidden" id="catalogs" name="catalogs" value="1" />
<table width="100%">
		<tr>
			<td width="100%">&nbsp;</td>
			<td align="right">
			<?php echo T_('Search:'); ?>
			</td>
			<td>
			<input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
		</tr>
</table>
<table class="adminlist" id="catalog_table" cellpadding="3" cellspacing="0" width="80%">
<thead>
<tr>
	<th width="5" class="input">
	<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" />
	</th>
	<th width="20%"><?php echo T_('Language File'); ?></th>
	<th width="20%"><?php echo T_('% Translated'); ?></th>
	<th width="20%"><?php echo T_('Total Strings'); ?></th>
	<th width="20%"><?php echo T_('Translated Strings'); ?></th>
	<th width="20%"><?php echo T_('Fuzzy'); ?></th>
</tr>
</thead>
<tbody>
<?php $a=0; $b=0; foreach ($rows as $row) : $link = $this->getCfg('admin_site')."/index.php?core=cor_languages&act=catalogs&task=edit&domain={$row['domain']}&lang={$this->vars['lang']}"; ?>
	<tr class="<?php echo "row$a"; ?>">
		<td>
		<input type="checkbox" id="cb<?php echo $b;?>" name="domain" value="<?php echo $row['domain']; ?>" onClick="isChecked(this.checked);" />
		</td>
		<td width="25%">
		<a href="<?php echo $link ?>"><?php echo $row['domain'];?></a></td>
		<td width="5%">
       <?php echo $row['percent'];?>
		</td>
		<td width="5%">
       <?php echo $row['strings'];?>
		</td>
		<td>
		<?php echo $row['translated']; ?>
		</td>
		<td>
		<?php echo $row['fuzzy']; ?>
		</td>
	</tr>
<?php $a=1-$a; $b++; endforeach; ?>
</tbody>
</table>

<script type="text/javascript">
table = new Table('catalog_table');
table.makeSortable(1,"null,str,float,float,float,float");
</script>