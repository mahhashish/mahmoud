<?php
/**
* Aliro language core component
*/

class languagesControllers extends aliroComponentAdminControllers {
	protected $lang = '';
	protected $aliroLanguage = null;
	public $renderer = null;
	public $search = '';

	public function __construct ($manager) {
		parent::__construct($manager);
		$defaultlang = isset($_SESSION['cor_languages_session']['lang']) ? $_SESSION['cor_languages_session']['lang'] : $this->getCfg('locale');
		$this->lang = $_SESSION['cor_languages_session']['lang'] = $this->getParam($_REQUEST, 'lang', $defaultlang);
		$this->aliroLanguage = new aliroLanguageExtended($this->lang);
		$this->renderer = aliroRenderer::getRenderer();
		$this->renderer->setdir(dirname(__FILE__).'/views/templates/');
	}

	public function getRequestData () {
		$this->search = $this->getParam($_REQUEST, 'search');
		if (!$this->act) $this->act = 'languages';
		// if ('list' == $this->task AND 'languages' == $this->act) $this->task = $this->act;
		$this->renderer->addvar('task',  $this->task);
		$this->renderer->addvar('act',  $this->act);
		$this->renderer->addvar('lang', $this->lang);
		$this->renderer->addvar('search', $this->search);
	}

    public function redirector ($task=null, $act=null) {
        $url  = $_SERVER['PHP_SELF'].'?core=cor_languages';
        $url .= !is_null($task) ? '&task='.$task : '';
        $url .= !is_null($act) ? '&act='.$act : '';
        if (headers_sent()) {
            echo "<script>document.location.href='$url';</script>";
        } else {
            #if (ob_get_contents()) while (@ob_end_clean()); // clear output buffer if one exists
            header( "Location: $url" );
        }
        exit;
    }

	public function toolbar () {
		$menubar = aliroAdminToolbar::getInstance();
		if ('languages' == $this->act) $this->languagesToolbar($menubar);
		else $this->catalogToolbar($menubar);
	}
	
	private function languagesToolbar($menuBar) {
		$gettextadmin = new PHPGettextAdmin();
		switch ($this->task) {
			case 'list':
			case 'cancel';
			case 'save':
				if (!file_exists(_ALIRO_ABSOLUTE_PATH.'/language/untranslated') AND $gettextadmin->has_gettext()) {
					$menuBar->customX( 'extract', 'langbuild.png', 'langbuild.png', T_('Scan Sources'), false );
				}
				$menuBar->custom( 'install', 'move.png', 'move_f2.png', T_('Install'), false );
				$menuBar->custom( 'translate', 'edit.png', 'edit_f2.png', T_('Translate'), true );
				$menuBar->custom( 'export', 'upload.png', 'upload_f2.png', T_('Export'));
				$menuBar->addNewX();
				$menuBar->editListX( 'edit' );
				$menuBar->deleteList();
				break;
			case 'edit':
			case 'new':
				$menuBar->save();
				$menuBar->cancel();
				break;
		}
	}
				
	private function catalogToolbar ($menuBar) {
		switch ($this->task) {
			case 'list':
			case 'cancel';
				$menuBar->customX( 'update', 'publish.png', 'publish_f2.png', T_('Update'), false );
				$menuBar->editListX( 'edit' );
				$menuBar->cancel();
				break;
			case 'edit':
			case 'apply':
				$menuBar->custom( 'auto_translate', 'copy.png', 'copy_f2.png', T_('Auto Translate'), false );
				$menuBar->apply();
				$menuBar->save();
				$menuBar->cancel();
				break;
			case 'update':
			    $menuBar->cancel();
				break;
		}
	}
			
	protected function setConfigLanguage ($lang, $locale) {
		$config = aliroCore::getConfigData('configuration.php');
		$config['lang'] = $lang;
		$_SESSION['cor_languages_session']['lang'] = $locale;
		aliroCore::set('lang', $lang);
		$config['locale'] = $locale;
		aliroCore::set('locale', $locale);
		$freshinstall = new aliroInstall();
		$freshinstall->storeConfig($config, 'configuration.php', true);
	}

}
