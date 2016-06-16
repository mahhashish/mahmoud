<?php

class languagesAdminCatalogs extends languagesControllers {
	protected static $instance = null;

	public static function getInstance ($manager) {
		if (self::$instance == null) self::$instance = new languagesAdminCatalogs ($manager);
		return self::$instance;
	}

	public function listTask () {
		$view = new catalogsView();
		$view->render($this->renderer);
	}

	public function editTask () {
		$this->renderer->addvar('domain', $this->getParam($_REQUEST, 'domain'));
		$view = new editView();
		$view->render($this->renderer);
	}

	public function cancelTask () {
		$this->redirector('list', 'catalogs');
	}
	
	public function updateTask () {
		$charset = $this->aliroLanguage->charset;
        $textdomain = $this->class_base.'/language';
        $gettext_admin = new PHPGettextAdmin(true);
        $gettext_admin->compile($this->lang, $textdomain,  $charset);
        $this->aliroLanguage->save();
        $this->redirector ('list', 'catalogs');
	}
	
	public function auto_translateTask () {
        $domain = $this->getParam($_POST, 'domain');
        $textdomain = $this->getParam($_POST, 'textdomain');

        $language = new aliroLanguageExtended($this->lang, $textdomain);
        $catalog = new PHPGettextFilePO (true, $domain, $textdomain, $this->lang);
        $catalog->setComments($_POST['comments']);
        $catalog->setHeaders($_POST['headers']);        

        foreach ($_POST as $key => $value) {
            if (preg_match('/^([a-z]+)[_]?([0-9]+)?_([0-9]+)$/', $key, $matches))  {
                switch ($matches[1])
                {
                    case 'msgid':
						$messages[$matches[3]]['msgid'] = $value;
						break;
                    case 'msgid_plural':
						$messages[$matches[3]]['msgid_plural'] = $value;
						break;
                    case 'msgstr':
						if ($matches[2] != '') $messages[$matches[3]]['msgstr'][$matches[2]] =  stripslashes($value);
						else $messages[$matches[3]]['msgstr'] =  stripslashes($value);
						break;
                    case 'fuzzy':
						$messages[$matches[3]]['fuzzy'] = $value == 'true' ? true : false;
						break;
                }
            }
        }
        foreach ($messages as $index => $arr) {
            if (0 == strcmp($catalog->strings[$index]->msgid, $arr['msgid'])) {
                $catalog->strings[$index]->setmsgstr($arr['msgstr']);
                if ($arr['fuzzy']) $catalog->strings[$index]->setfuzzy($arr['fuzzy']);
            }
        }
        $catalog->save();        
        
        $gettext_admin = new PHPGettextAdmin();
        $gettext_admin->update_translation($domain, $textdomain, $this->lang);

		$this->renderer->addvar('domain', $domain);
		$view = new editView();
		$view->render($this->renderer);
	}

	public function saveTask () {
		$this->commonSave();
        $this->redirector('list', 'catalogs');
	}
	
	public function applyTask () {
		$this->commonSave();
		$this->renderer->addvar('domain', $this->getParam($_REQUEST, 'domain'));
		$view = new editView();
		$view->render($this->renderer);
	}
	
	private function commonSave () {
        $domain     = $this->getParam($_POST, 'domain');
        $textdomain = $this->getParam($_POST, 'textdomain');
        $lang       = $this->getParam($_POST, 'lang');

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
	}

}