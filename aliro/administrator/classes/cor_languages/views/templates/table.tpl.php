<?php 
/**
* @package Mambo
* @subpackage Languages
* @copyright  Refer to copyright.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Mambo Foundation Inc see README.php
*/ 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<table class="adminheading">
	<tr>
		<th class="langmanager">
		<?php echo T_('Language Manager') ?>
		</th>
		<td style="white-space: nowrap;text-align:right">
		<?php echo mosCurrentDate();?>   			
        </td>
	</tr>
</table>
<table class="adminlist">
<tr>
	<th width="30">&nbsp;
	
	</th>
	<th width="25%" class="title">
	<?php echo T_('Languages') ?>
	</th>
	<th width="5%">
	<?php echo T_('Default') ?>
	</th>
	<th width="10%">
	<?php echo T_('Version') ?>
	</th>
	<th width="10%">
	<?php echo T_('Date') ?>
	</th>
	<th width="20%">
	<?php echo T_('Author') ?>
	</th>
	<th width="25%">
	<?php echo T_('Author Email') ?>
	</th>
</tr>	
<?php for ($i=0, $n=count( $rows ); $i < $n; $i++) :  $row = $rows[$i];?>
	<tr class="<?php echo "row$i"; ?>">			
		<td width="20">
		<input type="radio" id="cb<?php echo $i;?>" name="lang[]" value="<?php echo $row->language; ?>" onClick="isChecked(this.checked);" />
		</td>
		<td width="25%">
		<a href="#" onclick="hideMainMenu();return listItemTask('cb<?php echo $i;?>','edit')"><?php echo $row->name;?></a></td>
		<td width="5%" align="center">			
		<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','publish')">
		<img src="images/<?php echo ( $row->published ) ? 'tick.png' : 'publish_x.png';?>" width="12" height="12" border="0" alt="<?php echo ( $row->published ) ? T_('Yes') : T_('No');?>" />
		</a>
		</td>
		<td align=center>
		<?php echo $row->version; ?>
		</td>
		<td align=center>
		<?php echo $row->creationdate; ?>
		</td>
		<td align=center>
		<?php echo $row->author; ?>
		</td>
		<td align=center>
		<?php echo $row->authorEmail; ?>
		</td>
	</tr>
<?php endfor; ?>
</table>