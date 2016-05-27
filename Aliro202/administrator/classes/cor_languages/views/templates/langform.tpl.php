<?php
/**
* Aliro Language Manager
*/
	defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
	mosCommonHTML::loadOverlib();
	$iso = explode( '=', _ISO );
	$terr_script = '';
	foreach ($territories as $name => $territory) {
		$str = '';
		$cnt = count($territories[$name]);
		for ($a=0; $a<$cnt; $a++) {
		    $str .= '{iso3166_2:"'.$territory[$a]['iso3166_2'].'", iso3166_3:"'.$territory[$a]['iso3166_3'].'", territory:"'.$territory[$a]['territory'].'"}';
		    $str .= $a != $cnt-1 ? ", " : "";
		}
		$terr_script .= <<<TERR_SCRIPT
territories['<?php echo $name ?>'] =  [$str];

TERR_SCRIPT;

	}
	
	$code_script = '';
	foreach ($codesets as $name => $charset) {
		$str = '';
		$cnt = count($codesets[$name]);
		for ($b=0; $b<count($codesets[$name]); $b++) {
		    $str .= '{value:"'.$charset[$b].'", text:"'.$charset[$b].'"}';
		    $str .= $b != $cnt-1 ? ", " : "";
		}
		$code_script .= <<<CODE_SCRIPT
codesets['$name'] =  [$str];
		
CODE_SCRIPT;
	
	}
	
	$direct_script = '';
	foreach ($directions as $name => $dir) $direct_script .= <<<DIRECT_SCRIPT
directions['$name'] =  '$dir';		
		
DIRECT_SCRIPT;

	$date_script = '';
	foreach ($dateformats as $name => $fmt) $date_script .= <<<DATE_SCRIPT
dateformats['$name'] =  '$fmt';	
	
DATE_SCRIPT;
	
	$plural_script = '';
	foreach ($plural_forms as $name => $exp) $plural_script .= <<<PLURAL_SCRIPT
plurals['$name'] =  '$exp'>;	
	
PLURAL_SCRIPT;
	
	echo <<<LANG_SCRIPT_SUB
	
<script type="text/javascript">
var iso = "<?php echo $iso[1];?>";
var wincharset = "<?php echo $language->charset;?>";
function submitbutton(pressbutton) {
    var form = document.adminForm;
	var page_ = document.adminForm.page_.value;
    if (pressbutton == 'cancel') {
        YUI.ALIRO.CORE.submitform( pressbutton );
        return;
    }	    // do field validation
    if (form.language.value == "") {
        alert( "<?php echo T_('You must choose a language.') ?>" );
    } else if (form.charset.value == "") {
        alert( "<?php echo T_('You must choose a character set.') ?>" );
    }else if ( (iso!=wincharset) && (page_=="editpage") )
	{
		alert( "<?php echo T_("Cannot save! You must set \\\"$language->title\\\" to default language before.") ?>");
    }  else {
        YUI.ALIRO.CORE.submitform( pressbutton );
    }
}
</script>

LANG_SCRIPT_SUB;

	if ($task == 'new') echo <<<LANG_SCRIPT
	
<script type="text/javascript">
function langselect(select) {
    var language       = select.form.elements["language"];
    var iso3166_2      = select.form.elements["iso3166_2"];
    var charset        = select.form.elements["charset"];
    var plural_form    = select.form.elements["plural_form"];
    var date_format    = select.form.elements["date_format"];
    var text_direction = select.form.elements["text_direction"];
    var territory      = select.form.elements["territory"];
    lang               = select.options[select.selectedIndex].value;
    language.value     = select.options[select.selectedIndex].text;
    territory.value     = '';
    iso3166_2.options.length = 0;
    charset.options.length = 0;
    select.options[0] = new Option("<?php echo T_("choose language"); ?>", "", true, false);
    if (lang != "") {
        if(plurals[lang])  plural_form.value = plurals[lang];
        if(dateformats[lang])  date_format.value = dateformats[lang];
        if(directions[lang])  text_direction.value = directions[lang];
        var t_options   = territories[lang];
        var c_options   = codesets[lang];
        iso3166_2.options[0] = new Option("<?php echo T_("choose territory"); ?>", "", true, false);
        charset.options[0]   = new Option("<?php echo T_("choose character set"); ?>", "utf-8", true, false);
        for (var a = 0; a < t_options.length; a++) {
            iso3166_2.options[a + 1] = new Option(t_options[a].territory, t_options[a].iso3166_2);
        }
        for (var b = 0; b < c_options.length; b++) {
            charset.options[b + 1] = new Option(c_options[b].text, c_options[b].value);
        }
    }
}
function territoryselect(select) {
    var territory  = select.form.elements["territory"];
    var iso3166_3  = select.form.elements["iso3166_3"];
    iso3166_3.value= '';
    iso            = select.options[select.selectedIndex].value;
    if(iso != '') territory.value= select.options[select.selectedIndex].text;
    else territory.value= '';

    var tarr   = territories[lang];
    for (var a = 0; a < tarr.length; a++) {
        if(iso == tarr[a].iso3166_2) {
            iso3166_3.value = tarr[a].iso3166_3;
        }
    }
}
var territories = new Object();
$terr_script
var codesets = new Object();
$code_script
var directions = new Object();
$direct_script
var dateformats = new Object();
$date_script
var plurals = new Object();
$plural_script
</script>

LANG_SCRIPT;

	if ('new' == $task) :
?>
<table class="adminform">
	<tr>
		<td colspan="2">
			<?php echo T_('You must choose a language. Do not choose a territory unless it is necessary for your purpose. If you do not choose a character set, it will default to UTF-8, which is the preferred choice.') ?>
		</td>
	</tr>
	<tr>
		<td style="width:200px"><?php echo T_('Language') ?></td>
		<td>
            <select id="iso639" name="iso639" style="width:250px" class="inputbox" onchange="langselect(this)">
            <option value=""><?php echo T_('choose language') ?></option>
            <?php foreach ($locales as $name => $locale): ?>
            <option value="<?php echo $name ?>"><?php echo $locale['title'] ?></option>
            <?php endforeach; ?>
            </select>
		</td>
	</tr>
	<tr>
		<td style="width:200px"><?php echo T_('Territory') ?>:</td>
		<td>
            <select id="iso3166_2" name="iso3166_2" style="width:250px" class="inputbox" onchange="territoryselect(this)">
            <option value=""><?php echo T_('choose territory') ?></option>
    		</select>
       </td>
	</tr>
	<tr>
		<td><?php echo T_('Character Set') ?>:</td>
		<td>
            <select id="codeset" name="charset" style="width:250px" class="inputbox">
            <option value="utf-8"><?php echo T_('choose charset') ?></option>
            </select>
			<input type="hidden" name="page_" value="addpage" />
    		<?php #echo $this->html('toolTip', T_('The character encoding for this language. Ex. utf-8') ); ?>
		</td>
	</tr>
</table>
<?php endif; //if ($task == 'new') : ?>

<input type="hidden" id="language" name="language" value="<?php echo isset($language->name) ? $language->name : '' ?>" />
<input type="hidden" id="text_direction" name="text_direction" value="<?php echo isset($language->text_direction) ? $language->text_direction : '' ?>" />
<input type="hidden" id="plural_form" name="plural_form" value="<?php echo isset($language) && isset($language->plural_form['expression']) ? $language->plural_form['expression'] : 'nplurals=2; plural=n != 1;' ?>" />
<input type="hidden" id="date_format" name="date_format" value="<?php echo isset($language->date_format) ? $language->date_format : '' ?>" />
<input type="hidden" id="territory" name="territory" value="<?php echo isset($language->territory) ? $language->territory : '' ?>" />
<input type="hidden" id="iso3166_3" name="iso3166_3" value="<?php echo isset($language->iso3166_3) ? $language->iso3166_3 : '' ?>" />

<?php if ($task == 'edit') : ?>
<?php
$tabs = new mosTabs(1);
$tabs->startPane("editlanguage");
$tabs->startTab(T_("Details"),"details");

?>
<table class="adminform">
<tr><td style="width:150px">&nbsp;</td><td>&nbsp;</td></tr>
    <tr>
        <td><?php echo T_('Language') ?>:</td>
        <td>
        <input type="hidden" id="iso639" name="iso639" value="<?php echo $language->iso639 ?>" />
        <span style="font-weight:bold;font-size:110%"><?php echo $language->title; ?></span>
        </td>
    </tr>
    <?php if (!empty($language->territory)) : ?>
    <tr>
        <td><?php echo T_('Territory') ?>:</td>
        <td>
            <input type="hidden" id="iso3166_2" name="iso3166_2" value="<?php echo $language->iso3166_2  ?>" />
            <span style="font-weight:bold;font-size:110%"><?php echo $language->territory; ?> </span>
        </td>
    </tr>
    <?php endif; ?>
    <tr>
        <td><?php echo T_('Character Set') ?>:</td>
        <td><div style="width:400px">
        <input type="hidden" id="charset" name="charset" value="<?php echo $language->charset ?>" />
        <div style="display:inline;;margin-right:120px;font-weight:bold;font-size:110%"><?php echo $language->charset; ?> </div>
<!---->
       <?php /* if (class_exists('ConvertCharset')) : ? >
        <div style="display:inline;text-align:right;margin-right:0px">
        <button class="button" onclick="submitbutton('convert')"><?php echo T_('Convert to') ? >:</button>
        <select name="newcharset" id="newcharset">
        < ?php foreach ($language->codesets as $chr):
        if ($chr != $language->charset) echo '<option value="'.$chr.'">'.$chr.'</option>';
        endforeach; ? >
        </select>
        </div>
        <?php endif; */ ?>
<!---->
        </div>
        </td>
    </tr>
    <tr>
        <td><?php echo T_('Description') ?>:</td>
        <td>
            <input type="text" name="description" maxlength="100" class="inputbox" style="width:400px" value="<?php echo $language->description ?>" />
        </td>
    </tr>
    <tr>
        <td><?php echo T_('Author') ?>:</td>
        <td>
            <input type="text" name="author" maxlength="100" class="inputbox" style="width:400px" value="<?php echo $language->author ?>" />
        </td>
    </tr>
    <tr>
        <td><?php echo T_('Author Email') ?>:</td>
        <td>
            <input type="text" name="authoremail" maxlength="100" class="inputbox" style="width:400px" value="<?php echo $language->authoremail ?>" />
        </td>
    </tr>
    <tr>
        <td><?php echo T_('Author Url') ?>:</td>
        <td>
            <input type="text" name="authorurl" maxlength="100" class="inputbox" style="width:400px" value="<?php echo $language->authorurl ?>" />
        </td>
    </tr>
    <tr>
        <td><?php echo T_('Text Direction') ?>:</td>
        <td>
            <select	 name="text_direction" class="inputbox"  style="width:400px">
            <option value="ltr"<?php if ($language->text_direction == 'ltr') echo ' selected="selected"' ?>><?php echo T_('left to right') ?></option>
            <option value="rtl"<?php if ($language->text_direction == 'rtl') echo ' selected="selected"' ?>><?php echo T_('right to left') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php echo T_('Plural Forms'); ?></td>
        <td>
            <select	 name="plural_form" class="inputbox"  style="width:400px">
            <?php foreach ($plurals as $plural): ?>
            <option value="<?php echo $plural[1] ?>"<?php if ($language->plural_form['expression'] == $plural[1]) echo ' selected="selected"' ?>><?php echo $plural[0] ?></option>
            <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php echo T_('Date Format') ?></td>
        <td>
            <input type="text" name="date_format" maxlength="100" class="inputbox" style="width:400px" value="<?php echo $language->date_format ?>" />
            <?php echo $this->html('toolTip', T_('Date format for strftime().') ); ?>
        </td>
    </tr>
    <tr>
        <td><?php echo T_('Locales') ?></td>
        <td>
            <input type="text" name="locale" maxlength="100" class="inputbox" style="width:400px" value="<?php echo $language->locale ?>" />
			<input type="hidden" name="page_" value="editpage" />
            <?php echo $this->html('toolTip', T_('The locale list for setlocale(). \nLocale names must be comma separated.') ); ?>
        </td>
    </tr>
</table>
<?php $tabs->endTab(); $tabs->startTab(T_('Days'),"days"); ?>
<table class="adminform">
<tr><td style="width:150px">&nbsp;</td><td>&nbsp;</td></tr>
    <?php foreach ($language->days as $kd => $day): ?>
    <tr>
        <td>
            <?php echo strtoupper($kd); ?>
        </td>
        <td>
            <input type="text" name="days[<?php echo $kd ?>]" maxlength="100" class="inputbox" style="width:400px" value="<?php echo $day ?>" />
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php $tabs->endTab(); $tabs->startTab(T_('Months'),"months"); ?>
<table class="adminform">
<tr><td style="width:150px">&nbsp;</td><td>&nbsp;</td></tr>
    <?php foreach ($language->months as $km => $month): ?>
    <tr>
        <td>
            <?php echo strtoupper($km); ?>
        </td>
        <td>
            <input type="text" name="months[<?php echo $km ?>]" maxlength="100" class="inputbox" style="width:250px" value="<?php echo $month ?>" />
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php $tabs->endTab(); $tabs->endPane(); ?>
<?php endif;  ?>
