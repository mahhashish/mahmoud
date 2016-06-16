<?php
/**
* Aliro Language Manager
*/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// Required because xgettext does not find T_ strings within heredoc
$this->translations[] = T_('You cannot delete English.');
$this->translations[] = T_('You cannot delete default language.');
$this->translations[] = T_('You cannot translate default English.');
$this->translations[] = T_('Language');
$this->translations[] = T_('Country - Region');
$this->translations[] = T_('Default');
$this->translations[] = T_('Character Set');
$this->translations[] = T_('Version');
$this->translations[] = T_('Date');
$this->translations[] = T_('For a new language, please choose');
$this->translations[] = T_('Territory (optional)');
$this->translations[] = T_('...then click on NEW in the toolbar');

aliroRequest::getInstance()->addScript('/includes/js/alirojavascript.js');
aliroRequest::getInstance()->addScript('/includes/js/aliro/twoLevelDynamicSelect.js');
echo <<<LANGUAGES_SCRIPT

<script type="text/javascript"><!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
    var form = document.adminForm;
    if(pressbutton == 'remove') {
		if(getSelectedRadio('adminForm','lang') == "en") {
			alert( "{$this->T_('You cannot delete English.')}" );
		} else {
			var defaultlang=document.adminForm.defaultlang.value;
			var mylang = document.adminForm.lang;
			var candelete=true;
			for(i=0;i<mylang.length;i++) {
				if(mylang[i].checked && mylang[i].value==defaultlang) {
					candelete=false;
					alert( "{$this->T_('You cannot delete default language.')}" );
					break;
				}
			}
			if(candelete)
				submitform( pressbutton );

		}
    } else if (pressbutton == 'translate')   {
        if (getSelectedRadio('adminForm','lang') == "en") {
            alert( "{$this->T_('You cannot translate default English.')}" );
        } else {
            submitform( pressbutton );
        }
    }else{
        submitform( pressbutton );
    }
}
// Attach our behavior onload
window.onload = function() {
	dynamicSelect("iso639", "iso31662");
}
//--><!]]></script>

LANGUAGES_SCRIPT;

$locales = $this->getvars('locales');

$options_territories = '';
foreach ($locales['territories'] as $name => $territory) {
	foreach ($territory as $terr) {
	    $options_territories .= <<<ONE_TERRITORY
		    
		    <option class="$name" value="{$terr['iso3166_2']}">$name - {$terr['territory']}</option>
			    
ONE_TERRITORY;
			    
	}
}

$loptions = '';
foreach ($locales['locales'] as $name => $locale) {
	$loptions .= <<<LANG_OPTION

           <option value="$name">$name - {$locale['title']}</option>
            
LANG_OPTION;

}


$locale = aliroRequest::getInstance()->getCfg('locale');
$langhtml = '';
$i = 0;

foreach ($this->getvars('languages') as $lang=>$language) {
	$image = ($this->getvars('lang') == $lang) ? "images/tick.png" : "images/publish_x.png";
	$link = 'index.php?core=cor_languages&amp;act=languages&amp;task=default&amp;lang='.$lang;
	$langhtml .= <<<ONE_LANGUAGE

	<tr id="$i">
		<td style="width:10px">
		<input type="radio" id="cb$i" name="lang" value="$language->name" onClick="isChecked(this.checked);" />
		</td>
		<td>
		<a href="index.php?core=cor_languages&amp;act=languages&amp;task=edit&amp;lang=$lang">$language->title</a>
		</td>
		<td>
		$language->territory
		</td>
		<td>
			<a href="$link">
			<img src="$image" width="12" height="12" border="0" alt="" />
			</a>
		</td>
		<td>
		$language->charset
		</td>
		<td>
		$language->version
		</td>
		<td>
		$language->creationdate
		</td>
	</tr>

ONE_LANGUAGE;

	$i++;
}

echo <<<LANGUAGE_LIST

<fieldset style="width: 300px">
	<legend>{$this->T_('For a new language, please choose')}</legend>
	<label for="iso639">{$this->T_('Language')}</label><br />
    <select id="iso639" name="iso639" style="width:250px" class="inputbox">
	    <option value="">{$this->T_('choose language')}</option>
        $loptions
    </select>
	<label for="iso31662">{$this->T_('Territory (optional)')}</label><br />
    <select id="iso31662" name="iso3166_2" style="width:250px" class="inputbox">
		<option class="select" value="">{$this->T_('choose territory')}</option>
		$options_territories
	</select><br />
	{$this->T_('...then click on NEW in the toolbar')}
</fieldset>

<table class="adminlist" id="lang_table" cellpadding="3" cellspacing="0" width="80%">
<thead>
<tr>
    <th style="width:10px">&nbsp;</th>
    <th>{$this->T_('Language')}</th>
    <th>{$this->T_('Country - Region')}</th>
    <th>{$this->T_('Default')}</th>
    <th>{$this->T_('Character Set')}</th>
    <th>{$this->T_('Version')}</th>
    <th>{$this->T_('Date')}</th>
</tr>
</thead>
<tbody>
$langhtml
</tbody>
</table>
<input type="hidden" name="defaultlang" value="$locale" />
<input type="hidden" name="core" value="cor_languages" />
<input type="hidden" name="act" value="languages" />
<script type="text/javascript">
table = new Table('lang_table');
table.makeSortable(1,"null,str,null,str,str,date");
</script>

LANGUAGE_LIST;
