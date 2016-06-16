<?php

class languagesAdminLanguages extends languagesControllers {
	protected static $instance = null;
	protected $basepath = '';
	protected $frontpaths = array (
		array('/includes', true),
		array('/classes', false),
		array('/extclasses', true),
		array('/', false)
	);
	protected $adminpaths = array (
		array('/classes', true)
	);

	public function __construct ($manager) {
		$this->basepath = $this->class_base.'/language/';
		foreach ($this->frontpaths as &$frontpath) $frontpath[0] = $this->class_base.$frontpath[0];
		foreach ($this->adminpaths as &$adminpath) $adminpath[0] = $this->class_base.$this->admin_dir.$adminpath[0];
		parent::__construct($manager);
	}

	public static function getInstance ($manager) {
		if (self::$instance == null) self::$instance = new languagesAdminLanguages ($manager);
		return self::$instance;
	}

	public function listTask () {
        $xlanguage = new aliroLanguageExtended();
		$this->renderer->addvar('locales', $xlanguage->getlocales());
		$this->renderer->addvar('languages', $xlanguage->getLanguages());
		$view = new languageView();
		$view->render($this->renderer);
	}

	public function editTask () {
		$view = new editView();
		$view->render($this->renderer);
    }

    public function newTask () {
		$view = new editView();
		$view->render($this->renderer);
    }

	public function removeTask () {
		if ('en' != $this->lang) aliroExtensionHandler::getInstance()->removeExtensions($this->lang);
		$this->redirector('list', 'languages');
	}

    public function cancelTask () {
    	$this->listTask();
    }

	public function defaultTask () {
        $xlanguage = new aliroLanguageExtended();
		$languages = $xlanguage->getLanguages();
		if (isset($languages[$this->lang])) {
			$language = $languages[$this->lang];
			$locales = explode(',', $language->locale);
			if (isset($locales[3])) $this->setConfigLanguage($locales[3], $this->lang);
			else trigger_error(T_('Chosen language has invalid locale'));
		}
		else trigger_error (T_('Invalid default language selection'));
        $this->redirector('list', 'languages');
	}

    public function saveTask ()
    {
        $iso639 = strtolower($this->getParam($_POST, 'iso639'));
        $iso3166 = $this->getParam($_POST, 'iso3166_2');
        $iso3166_3 = $this->getParam($_POST, 'iso3166_3');
        $lang  = $iso639;
        $lang .= strlen($iso3166) == 2 ? '-'.$iso3166 : '';
        $langfile = $this->basepath."$lang/$lang.xml";
        switch ($_POST['act'])
        {
            case 'languages':
				$language = new aliroLanguageExtended($lang);
	            if (file_exists($langfile)) $language->update();
	            else $language->createLanguage($iso639, $iso3166, $iso3166_3);
	            return $this->redirector('list', 'languages');
	            break;
            case 'catalogs':
			default:
	            $this->updatecatalog(false);
	            return $this->redirectoror('list', 'catalogs');
	            break;
        }
    }

    public function extractTask ()
    {
    	$untranslated = $this->basepath.'untranslated';
    	if (file_exists($untranslated)) $this->redirector('list', 'languages');
        aliroFileManager::getInstance()->createDirectory($untranslated);
        // $cmtpaths = array_merge(glob("$path/administrator/components/com*"), glob("$path/components/com*"));
        // Leave components for now - a separate issue
        $cmtpaths = array();
/*
        foreach ($cmtpaths as $p) {
            preg_match('/com_(.*)$/', $p, $matches);
            $components[$matches[1]][]  =  $p;
        }
*/
        set_time_limit(300);
        $this->extract('frontend', $this->frontpaths);
        $this->extract('administrator', $this->adminpaths);
/*
        foreach ($components as $name => $dirs) {
            if (!file_exists("$dir/$name.pot")) {
                $this->extract($name, $dirs);
            }
        }
*/
        $this->redirector('list', 'languages');
    }

    public function translateTask () {
       $_SESSION['cor_languages_session']['lang'] = $_REQUEST['lang'];
       $this->redirector('list', 'catalogs');
    }
	
	public function exportTask () {
        $language = new aliroLanguageExtended($this->lang, null, true);
		$language->export();
	}

    private function extract ($domain, $scandirs, $language='untranslated')
    {
        if (!file_exists("{$this->basepath}$language/$domain.pot")) {
            $catalog = new PHPGettextFilePOT (false, $domain, $this->basepath, $language);
            $catalog->setDefaultCommentsHeaders();
            $catalog->save();
        }

        $this->scan_xml($domain, $this->basepath, $scandirs, $language);

        $gettext_admin = new PHPGettextAdmin();
        $cwd = getcwd();
        chdir($this->class_base);

        $php_sources = array();
        foreach ($scandirs as $onescan)  {
        	$onedir = new aliroDirectory($onescan[0]);
        	$nextdir = $onedir->listFiles('.php$', 'file', $onescan[1], true);
            $php_sources = array_merge($php_sources, $nextdir);
        }

        $gettext_admin->xgettext($domain, $this->basepath, $php_sources, $language);

        chdir($cwd);

        return true;
    }

    // This allows for translation of strings in xml files, in the params section
    private function scan_xml($domain, $path, $scandirs, $language='untranslated')
    {
        $catalog = new PHPGettextFilePOT (true, $domain, $path, $language);
        $xml_sources = array();
        foreach ($scandirs as $subdir)  {
	       	$nextdir = $this->read_dir($subdir[0], 'xml', true);
            if ($nextdir) $xml_sources = array_merge($xml_sources, $nextdir);
        }

        if (count($xml_sources) > 0) {
            $strings = array();
            foreach ($xml_sources as $file) {
                $p = xml_parser_create();
                xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
                xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
                xml_parse_into_struct($p, file_get_contents($this->class_base.'/'.$file), $values);
                xml_parser_free($p);
                foreach($values as $key => $value)
                {
                    switch ($value['tag'])
                    {
                        case 'name':
                        case 'description':
                        case 'option':
                        case 'menu':
                        if (isset($value['value']) && strlen($value['value']) >=1) $strings[$file][] = addcslashes($value['value'],'"');
                        break;
                        case 'param':
                        if (isset($value['attributes']) && $value['attributes']['type'] != 'spacer') {
                            if (isset($value['attributes']['label'])) $strings[$file][] = addcslashes($value['attributes']['label'],'"');
                            if (isset($value['attributes']['description'])) $strings[$file][] = addcslashes($value['attributes']['description'],'"');
                        }
                        break;
                    }
                }
                if (is_array($strings[$file]))
                $strings[$file] = array_values(array_unique($strings[$file]));
            }
            foreach ($strings as $file => $str) {
                foreach ($str as $msg)
                $messages[trim($msg)][] = '#: '.$file;
            }
            if (is_array($messages)){
                foreach ($messages as $msgid => $comments) {
                    if (!empty($msgid))
                    $catalog->addentry($msgid, null, null, $comments);#($msgid, $msgid_plural=null, $msgstr=null, $comments=array())
                }
            }
            $catalog->save();
        }
    }

    private function read_dir($dir, $filetype='php', $checkSlash = false)
    {
        $deep = true;
        if (substr($dir,-1)=='/' && $checkSlash ) $deep = false;
        if (!file_exists($dir)) return false;
        $array = array();
        $d = dir($dir);
        while (false !== ($entry = $d->read())) {
            if($entry!='.' && $entry!='..') {
                $entry = "$dir/$entry";
                $entry = str_replace( '\\', '/', $entry );
                if(is_dir($entry) && $deep) {
                	$nextdir = $this->read_dir($entry, $filetype);
                    if ($nextdir) $array = array_merge($array, $nextdir);
                } elseif (preg_match('/.'.$filetype.'$/', $entry)) {
                    $new_entry = str_replace($this->class_base.'/', '', $entry);
                    if ($new_entry[0] == '/') $new_entry = substr($new_entry, 1);
                    $array[] = $new_entry;
                }
            }
        }
        $d->close();
        return $array;
    }

    private function updatecatalog ($compile = true, $add_to_dict = true) {
        $domain     = $_POST['domain'];
        $textdomain = $_POST['textdomain'];
        $lang       = $_POST['lang'];

        $catalog = new PHPGettextFilePO (true, $domain, $textdomain, $lang);
        $catalog->setComments($_POST['comments']);
        $catalog->setHeaders($_POST['headers']);
        $plural_forms = $catalog->headers['Plural-Forms'];
        preg_match('/nplurals[\s]*[=]{1}[\s]*([\d]+);[\s]*plural[\s]*[=]{1}[\s]*(.*);/', $plural_forms, $matches);
        $is_plural = $matches[1] > 1;
        foreach ($_POST as $key => $value) {
            if (preg_match('/^([a-z]+[_]?[a-z]+?)[_]?([0-9]+)?_([0-9]+)$/', $key, $matches))  {
                switch ($matches[1])
                {
                    case 'msgid':
                        if (get_magic_quotes_gpc() == 1){
                            $value = stripslashes($value);
                            //$value = htmlentities($value);
                        }
                    $messages[$matches[3]]['msgid'] = $value;
                    break;
                    case 'msgid_plural':
                        if ($is_plural){
                            $messages[$matches[3]]['msgid_plural'] = $value;
                        }
                    break;
                    case 'msgstr':
                    if (!empty($messages[$matches[3]]['msgid_plural'])) {

                        if ($matches[2] != '') {
                            $messages[$matches[3]]['msgstr'][$matches[2]] =  stripslashes($value);
                        } else {
                            $messages[$matches[3]]['msgstr'][0] =  stripslashes($value);
                            $messages[$matches[3]]['msgstr'][1] =  '';
                        }
                    } else {
                        $messages[$matches[3]]['msgstr'] =  stripslashes($value);
                    }
                    break;
                    case 'fuzzy':
                    $messages[$matches[3]]['fuzzy'] = $value == 'true' ? true : false;
                    break;
                }
            }
        }
        foreach ($messages as $index => $arr) {
            if (strcmp($catalog->strings[$index]->msgid, $arr['msgid']) == 0) {
                $catalog->strings[$index]->setmsgstr($arr['msgstr']);
                $catalog->strings[$index]->msgid_plural = isset($arr['msgid_plural'])?$arr['msgid_plural']:null;;
                $catalog->strings[$index]->setfuzzy($arr['fuzzy']);
            }
        }
        $catalog->save();

        $language = new aliroLanguageExtended($lang);
        $language->save();

        $gettext_admin = new PHPGettextAdmin();
        $gettext_admin->add_to_dict($domain, $textdomain, $lang, $language->charset);
        $catalog->load();

        if ($compile) $catalog->saveAsMO();

    }

}