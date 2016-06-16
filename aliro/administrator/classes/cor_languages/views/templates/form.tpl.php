<?php
/**
* @package Mambo
* @subpackage Languages
* @copyright  Refer to copyright.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Mambo Foundation Inc see README.php
*/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

?>
<style type="text/css">
.sortbutton {
	border-top : solid 1px #3872B2;
	border-right : solid 1px #3872B2;
	border-bottom : solid 1px #3872B2;
	border-left : solid 1px #fff;
	background-color : #3872B2;
	color : #FFF;
	font-weight : bold;
	height : 2em;
	width : 100%;
	font-size : .9em;
	cursor: pointer;
}
</style>
<style type="text/css">
table.adminlist th {text-align:left;
                	margin: 0px;
                	padding: 0px;
                	height: 25px;
                	font-size: 11px;
                	color: #ffffff;
                   }
</style>
<script type="text/javascript" src="<?php echo $this->getCfg('live_site');?>/administrator/classes/cor_languages/tables.js"></script>

<?php if (isset($mosmsg)) : ?><div class="message"><?php echo $mosmsg ?></div> <?php endif; ?>
<input type="hidden" name="core" value="cor_languages" />
<input type="hidden" name="task" value="<?php echo $task ?>" />
<input type="hidden" name="act" value="<?php echo $act ?>" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />

<table class="adminheading">
	<tr>
		<th class="langmanager">
		<?php echo isset($header) ? $header : T_('Aliro Language Editor') ?>
		</th>
		<?php /*<!--
        <td>
        <?php if ($task == 'list'): ? >
           <select name="lang" class="inputbox" size="1" onchange="document.adminForm.submit();">
            	<option value="en">< ?php echo T_('Select Language') ? ></option>
            < ?php foreach ($languages as $name => $obj): ? >
            <option value="< ?php echo $name.'"';? > < ?php echo $lang==$name?' selected="selected"':''? >>< ?php echo $obj->title; if (!empty($obj->territory)) echo ' ('.$obj->territory.')' ?></option>
            < ?php endforeach; ? >
           </select>
       < ?php endif; ? >
       </td> -->
	   <?php */ ?>
	</tr>
</table>
<br />
<?php echo isset($content) ? $content : T_('No content to display.'); ?>