<?php
/**
* @package Mambo
* @subpackage Languages
* @copyright  Refer to copyright.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Mambo Foundation Inc see README.php
*/

class editView extends aliroView {
	
    function render ($renderer) {
        $task = $renderer->getvars('task');
        $act  = $renderer->getvars('act');

        switch ($act)
        {
            case 'catalogs':
            $this->editcatalog($renderer);
            break;

            case 'language':
            default:
            if ($task == 'new') $this->newlanguage($renderer);
            else $this->editlanguage($renderer);
            break;

        }
        $renderer->display('form.tpl.php');
    }

    function newlanguage ($renderer) {
		$header = T_('Language').' : <small>'.T_('New language').'</small>';
		$renderer->addvar('header', $header);
		$language = new aliroLanguageExtended();
        $locales = $language->getlocales();
        $renderer->addvar('language', $language);
        $renderer->addvar('locales',        $locales['locales']);
        $renderer->addvar('territories',    $locales['territories'] );
        $renderer->addvar('codesets',       $locales['codesets']);
        $renderer->addvar('dateformats',    $locales['dateformats']);
        $renderer->addvar('directions',     $locales['directions']);
        $renderer->addvar('plural_forms',   $locales['plural_forms']);
		$renderer->addvar('content', $renderer->fetch('langform.tpl.php'));
    }

    function editlanguage ($renderer) {

        $lang = $renderer->getvars('lang');
        $language = new aliroLanguageBasic($lang);
        $header = T_('Language').' : <small>'.T_('Edit language') . "  [ {$language->title} ]  ".'</small>';
        $renderer->addvar('header', $header);
        $renderer->addvar('language', $language);
        $renderer->addvar('plurals', $this->plurals());
        $renderer->addvar('content', $renderer->fetch('langform.tpl.php'));
    }

    function editcatalog ($renderer) {
        $lang = $renderer->getvars('lang');
        $language = new aliroLanguageBasic($lang);
        $domain = $renderer->getvars('domain');
        $catalog = new PHPGettextFilePO (true, $domain, $this->class_base."/language", $lang);
        $nplurals = 2;
        $_VERSION = new version();

        if (strpos($catalog->headers['Last-Translator'], 'FULL NAME')) {
            $catalog->headers['Last-Translator'] = "Translation <translation@aliro.org>";
        }
        if (strpos($catalog->headers['Language-Team'], 'LANGUAGE')) {
            $catalog->headers['Language-Team'] = "Translation <translation@aliro.org>";
        }
        $catalog->headers['Project-Id-Version'] = $_VERSION->PRODUCT.' '.$_VERSION->RELEASE;
        $catalog->headers['Report-Msgid-Bugs-To'] = 'translation@aliro.org';
        $catalog->headers['Plural-Forms'] = $language->plural_form['expression'];
        $catalog->headers['Content-Type'] = 'text/plain; charset='.$language->charset;
        $renderer->addvar('header', sprintf(T_('Translate Catalog: %s [%s]'),$domain, $lang));
        $renderer->addvar('nplurals', $language->plural_form['nplurals']);
        $renderer->addbyref('catalog', $catalog);
        $renderer->addvar('domain', $domain);
        $renderer->addvar('content', $renderer->fetch('editcatalog.tpl.php'));
    }

    // Method called by edit language
    private function plurals() {
        return array(
        array('Two forms, singular used for one only', 'nplurals=2; plural=n != 1;', array('danish','dutch','english','german','norwegian','swedish','estonian','finnish','greek','hebrew','italian','portuguese','spanish')),
        array('One single form', 'nplurals=1; plural=0;', array('Hungarian','Japanese','Korean','Turkish')),
        array('Two forms, singular used for zero and one', 'nplurals=2; plural=n>1;', array('french','brazilian portuguese')),
        array('Three forms, special case for zero', 'nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2;', array('Latvian')),
        array('Three forms, special cases for one and two', 'nplurals=3; plural=n==1 ? 0 : n==2 ? 1 : 2;', array('Gaeilge')),
        array('Three forms, special case for numbers ending in 1[2-9]', 'nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2;', array('lithuanian')),
        array('Three forms, special cases for numbers ending in 1 and 2, 3, 4, except those ending in 1[1-4]', 'nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;', explode(',', 'croatian,czech,russian,slovak,ukrainian')),
        array('Three forms, special case for one and some numbers ending in 2, 3, or 4', 'nplurals=3; plural=n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;', array('polish')),
        array('Four forms, special case for one and all numbers ending in 02, 03, or 04', 'nplurals=4; plural=n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3;', array('slovenian'))
        );
    }
}