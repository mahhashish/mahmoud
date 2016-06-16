<?php

class configAdminConfig extends aliroComponentAdminControllers {

	protected static $instance = null;

	protected $session_var = 'alirodoc_classid';
	protected $view_class = 'listConfigHTML';

	public static function getInstance ($manager) {
		if (self::$instance == null) self::$instance = new configAdminConfig ($manager);
		return self::$instance;
	}

	public static function taskTranslator () {
		return array (
		'save' => T_('Save'),
		'clear' => T_('Clear all Caches'),
		'manifest' => T_('Make Manifest'),
		'review' => T_('Create Upgrade'),
		'update' => T_('Make Upgrade'),
		'install' => T_('Install Upgrade'),
		'upgrade' => T_('Do Upgrade'),
		'cancel' => T_('Cancel')
		);
	}

	public function toolbar () {
		if ('review' == $this->task) {
			$this->toolBarButton('update');
			$this->toolBarButton('cancel');
		}
		elseif ('install' == $this->task) {
			$this->toolBarButton('upgrade');
			$this->toolBarButton('cancel');
		}
		else {
			$this->toolBarButton('save');
			$this->toolBarButton('clear');
			$this->toolBarButton('manifest');
			$this->toolBarButton('review');
			$this->toolBarButton('install');
		}
	}

	private function configFields () {
		$fields ['general'] = array ('offline', 'sitename', 'offline_message', 'error_message', 'favicon', 'MetaDesc', 'MetaKeys', 'list_limit', 'shownoauth', 'enable_log_items', 'enable_log_searches', 'back_button');
		$fields ['locale'] = array ('locale', 'lang', 'offset', 'charset', 'locale_use_iconv', 'locale_use_gettext');
		$fields ['sef'] = array ('sef', 'pagetitles');
		$fields ['technical'] = array ('debug', 'locale_debug', 'error_reporting', 'lifetime', 'adminlife', 'caching', 'cachetime', 'fileperms', 'dirperms', 'gzip', 'register_globals');
		$fields ['user'] = array ('useractivation', 'uniquemail');
		$fields ['mail'] = array ('mailer', 'mailfrom', 'fromname', 'sendmail', 'smtpauth', 'smtpuser', 'smtppass', 'smtphost');
		$fields['content'] = array ('hideAuthor', 'hideCreateDate', 'hideModifyDate', 'hidePdf', 'hidePrint', 'hideEmail', 'vote', 'MetaAuthor', 'MetaTitle', 'mbf_content',
		'multipage_toc', 'link_titles', 'item_navigation', 'readmore', 'hits', 'icons');
		return $fields;
	}

	public function listTask () {
		$unpacked = aliroCore::getConfigData('configuration.php');
		$view = new $this->view_class($this);
		$view->showConfig($unpacked, $this->configFields(), aliroCore::getConfigData('corecredentials.php'), aliroCore::getConfigData('credentials.php'));
	}
	
	public function cancelTask () {
		$this->listTask();
	}

	public function saveTask () {
		$config = aliroCore::getConfigData('configuration.php');
		$fields = $this->configFields();
		foreach ($fields as $fieldset) {
			foreach ($fieldset as $key) if (isset($_POST[$key])) $config[$key] = $_POST[$key];
		}
		$config['enable_stats'] = 0;
		$language = aliroLanguage::getInstance();
		if (!$language->validCharset($config['charset'])) $config['charset'] = 'utf-8';
		$freshinstall = new aliroInstall();
		$freshinstall->storeConfig($config, 'configuration.php', true);
		$message = T_('Non-database configuration updated');
		$credentials = $freshinstall->checkDatabaseConfig('gen');
		if ($credentials) {
			$corecredentials = $freshinstall->checkDatabaseConfig('core');
			if ($corecredentials) {
				$freshinstall->storeConfig ($credentials, 'credentials.php', true);
				$freshinstall->storeConfig ($corecredentials, 'corecredentials.php', true);
				$freshinstall->storeConfig($config, 'configuration.php', true);
				$message = T_('Configuration updated');
				$severity = _ALIRO_ERROR_INFORM;
			}
			else {
				$message = T_('The core database details were not valid');
				$severity = _ALIRO_ERROR_SEVERE;
			}
		}
		else {
			$message = T_('The general database details were not valid');
			$severity = _ALIRO_ERROR_SEVERE;
		}
		$this->clearTask();
		$this->redirect( 'index.php?core='.$this->option, $message, $severity);
	}
	
	public function manifestTask () {
		$manifest = new aliroMakeManifest();
		$manifest->buildManifest();
		$this->listTask();
	}
	
	public function reviewTask () {
		$view = new $this->view_class($this);
		$view->acceptManifest();
	}
	
	public function updateTask () {
		$files = aliroFileManager::getInstance()->makeUploadSafe('manifest', true);
		if (!empty($files)) {
			$manifest = new aliroMakeManifest();
			// Only expect a single file, will not really loop
			foreach ($files as $filename=>$file) {
				$manifest->reviewManifest($file);
				$dir = new aliroDirectory(dirname($file));
				break;
			}
			$dir->deleteAll();
		}
		$this->listTask();
	}

	public function installTask () {
		$view = new $this->view_class($this);
		$view->installManifest();
	}
	
	public function upgradeTask () {
		$files = aliroFileManager::getInstance()->makeUploadSafe('manifest', true);
		if (!empty($files)) {
			$manifest = new aliroMakeManifest();
			// Only expect a single file, will not really loop
			foreach ($files as $filename=>$file) {
				$reports = $manifest->installManifest($file);
				break;
			}
		}
		$view = new $this->view_class($this);
		$view->reportManifest($reports);
	}

	public function clearTask () {
		$path = $this->absolute_path.'/cache/singleton/';
		$dir = new aliroDirectory ($path);
		$list = $dir->listFiles();
        $filemanager = aliroFileManager::getInstance();
		foreach ($list as $file) {
			if ('index.html' == $file OR '.' == $file[0]) continue;
			$filemanager->deleteFile($path.$file);
		}
		$this->listTask();
	}

}