<?php
/**
* @package Aliro
* @subpackage Languages
* @copyright  Refer to copyright.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Martin Brampton
*/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$this->addCSS('/administrator/classes/cor_languages/views/css/translate.css');

$head1 = T_('Verified');
$head2 = T_('Original');
$head3 = T_('Translation');
$head4 = T_('Reference');

$translate_body = '';
foreach ($catalog->strings as $id => $message) {
    if (is_array($message->comments)) {
        $ref = $tt = '';
        foreach ($message->comments as $comment) {
            if (strncmp($comment, "#: ",3) == 0)
            $ref .= ltrim(trim($comment), '#: ')."\n";
            $tt  .= addslashes(htmlspecialchars(ltrim(trim($comment),'#: ,#. ,#  ')))."<br />";
        }
    }
	$translate_body .= <<<BODY_HTML
	
			<tr>
				<td>
					<input type="checkbox" name="fuzzy_$id" {$this->checkedIfTrue(!$message->is_fuzzy)} />
				</td>
				<td>
					<textarea class="scroll" readonly="readonly" name="msgid_$id">$message->msgid</textarea>
				</td>
				<td>
					<textarea class="scroll" name="msgstr_$id">$message->msgstr</textarea>
				</td>
			</tr>
	
BODY_HTML;

}

$html = <<<TRANSLATE_HTML

<div>
	<input type="hidden" name="domain" value="$catalog->name" />
	<input type="hidden" name="textdomain" value="$catalog->path" />
	<input type="hidden" name="lang" value="$catalog->lang" />
</div>

<div id="tableContainer" class="tableContainer">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="adminlist scrollTable">
		<thead class="fixedHeader">
			<tr>
				<th><a href="#">$head1</a></th>
				<th><a href="#">$head2</a></th>
				<th><a href="#">$head3</a></th>
			</tr>
		</thead>
		<tbody class="scrollContent">
			$translate_body
		</tbody>
	</table>
</div>

TRANSLATE_HTML;

echo $html;

/*


<?php mosCommonHTML::loadOverlib();?>
<script type="text/javascript" src="<?php echo $this->getCfg('live_site');?>/administrator/classes/cor_languages/prototype.js"></script>
<script type="text/javascript" src="<?php echo $this->getCfg('live_site');?>/administrator/classes/cor_languages/languages.js"></script>
<style>
table.adminlist th {text-align:left}
legend {font-weight:bold;}
div.strings {height:400px;overflow:auto;}
</style>


<table><tr><td style="width:75%;vertical-align:top">
<?php
$tabs = new mosTabs(1);
$tabs->startPane("catalog");
$tabs->startTab(T_("Messages"),"messages");
$language_plurals = ($nplurals > 1);
?>
<table class="adminlist">
<tr>
	<th style="width:50px">
	<?php echo T_('Verified') ?>
	</th>
	<th style="width:40%;text-align:center;">
	<?php echo T_('Original') ?>
	</th>
	<th style="width:40%;text-align:center;">
	<?php echo T_('Translation') ?>
	</th>
	<th style="width:70px;text-align:right;">
	<?php echo T_('Ref') ?>
	</th>
</tr>
</table>
<div class="strings">
<table class="adminlist" id="catalog_editor">
<tbody>
<?php $a=0; foreach ($catalog->strings as $id => $message) :?>
        <?php
        if(is_array($message->comments)) {
            $ref = '';
            $tt  = '';
            foreach($message->comments as $comment) {
                if(strncmp($comment, "#: ",3) == 0)
                $ref .= ltrim(trim($comment), '#: ')."\n";
                $tt  .= addslashes(htmlspecialchars(ltrim(trim($comment),'#: ,#. ,#  ')))."<br />";
            }
            $tip = $this->html('toolTip', $tt);
        }
        $is_plural = strlen($message->msgid_plural) > 1 ? 1 : 0;
        ?>
	<tr class="<?php echo "row$a"; ?>" id="row_<?php echo $id ?>" onclick="translate(<?php echo $id ?>, <?php echo ($is_plural && $language_plurals) ? 1 : 0; ?>, <?php echo $nplurals ?>)">
		<td style="width:50px">
		<img style="cursor:pointer;" onclick="return togglefuzzy(this, document.forms[0].fuzzy_<?php echo $id ?>)" src="images/<?php echo ( $message->is_fuzzy ) ? 'publish_x.png' : 'tick.png';?>" width="12" height="12" border="0" alt="<?php echo ( $message->is_fuzzy ) ? T_('Verify Translation') : T_('Translation OK');?>" />
		<input type="hidden" name="fuzzy_<?php echo $id ?>" value="<?php echo ( $message->is_fuzzy ) ? 'true' : 'false';?>"/>
		</td>
		<td style="width:40%">
		<textarea style="display:none" readonly="readonly" id="msgid_<?php echo $id ?>" name="msgid_<?php echo $id ?>"><?php echo $message->msgid ?></textarea>
		<?php if ($is_plural && $language_plurals): ?>
		<textarea style="display:none" readonly="readonly" id="msgid_plural_<?php echo $id ?>" name="msgid_plural_<?php echo $id ?>"><?php echo $message->msgid_plural ?></textarea>
		<?php endif; ?>
		<?php echo $message->msgid; ?>
		</td>
		<td style="width:40%">
		<?php if ($is_plural && $language_plurals): ?>
		<?php for ($b=0; $b < $nplurals; $b++):?>
		<textarea style="display:none" readonly="readonly" id="msgstr_<?php echo $b ?>_<?php echo $id ?>" name="msgstr_<?php echo $b ?>_<?php echo $id ?>"><?php if (isset($message->msgstr[$b])) echo $message->msgstr[$b]; ?></textarea>
		<?php endfor; ?>
		<?php else : ?>
		<textarea style="display:none" readonly="readonly" id="msgstr_<?php echo $id ?>" name="msgstr_<?php echo $id ?>"><?php echo $is_plural ? $message->msgstr[0] : $message->msgstr; ?></textarea>
		<?php endif; ?>
		<span id="msgstr_<?php echo $id ?>_span"><?php echo $is_plural ? $message->msgstr[0] : $message->msgstr;?></span>
		</td>
		<td style="width:50px;text-align:right">
		<?php if ($tip) echo $tip; ?>
        </td>
	</tr>
<?php $a=1-$a;endforeach; ?>
</tbody>
</table>
<script type="text/javascript">
table = new Table('catalog_editor');
</script>
</div>
<?php $tabs->endTab(); $tabs->startTab(T_('Headers'),"headers");?>
<table class="adminform" style="width:250px">
<tr><td>
<?php
foreach($catalog->headers as $key => $value) {
    $disabled = ($key == 'POT-Creation-Date') ? 'readonly="readonly"' : '';
    $str = "<tr>\n\t<td>\n\t<label style=\"font-weight:bold\">$key<br />\n";
    $str .= "<input type=\"text\" size=\"100\" name=\"headers[$key]\" value=\"".str_replace("\\n", "", trim($value))."\" $disabled />";
    $str .= "</label>\n</td>\n\t</tr>\n";
    echo $str;
}
?>
</td></tr>
</table>
<?php $tabs->endTab(); $tabs->startTab(T_('Comments'),"comments"); ?>
<table class="adminform" style="width:250px">
<tr><td>
<textarea rows="20" cols="75" name="comments"><?php echo implode("", $catalog->comments) ?></textarea>
</td></tr>
</table>
<?php $tabs->endTab(); $tabs->endPane(); ?>
</td><td style="vertical-align:top;padding-top:20px">
<input type="hidden" id="row_class" name="row_class" value="" />
<input type="hidden" id="row_id" name="row_id" value="" />


<div id="singular" style="position:relative;top:0;left:0;height:250px">
<table class="adminform"><tr><td>
<fieldset><legend><?php echo T_('Original') ?></legend><textarea id="s_msgid" name="s_msgid" cols="40" rows="11" readonly="readonly"></textarea></fieldset>
</td></tr><tr><td>
<fieldset><legend><?php echo T_('Translation') ?></legend><textarea id="s_msgstr" name="s_msgstr" cols="40" rows="11" onblur="update(this.value, 0, 0)"></textarea></fieldset>
</td></tr></table>
</div>
<div id="plural" style="position:relative;top:0;left:0;height:250px;display:none">
<table class="adminform"><tr><td>
<fieldset><legend><?php echo T_('Original') ?></legend>
<label><?php echo T_('Singular') ?><textarea id="p_msgid" name="p_msgid" cols="40" rows="4" readonly="readonly"></textarea></label>
<label><?php echo T_('Plural') ?><textarea id="p_msgid_plural" name="p_msgid_plural" cols="40" rows="4" readonly="readonly"></textarea></label></fieldset>
</td></tr><tr><td>
<fieldset><legend><?php echo T_('Translation') ?></legend>
<div id="plurals">
<script type="text/javascript">
var pluralPane1 = new WebFXTabPane( document.getElementById( "plurals" ), 0);
</script>
<?php for ($a=0; $a < $nplurals; $a++):?>
<div class="tab-page" id="plural_<?php echo $a ?>">
<h2 class="tab"><?php echo T_("Plural") ?>[<?php echo $a ?>]</h2>
<script type="text/javascript">
pluralPane1.addTabPage( document.getElementById( "plural_<?php echo $a ?>" ) );
</script>
<textarea id="p_msgstr_<?php echo $a ?>" name="p_msgstr_<?php echo $a ?>" cols="38" rows="9" onchange="update(this.value, 1, <?php echo $a ?> )"></textarea>
</div>
<?php endfor; ?>
</div>
</fieldset>
</td></tr></table>
</div>

</td></tr></table>

*/