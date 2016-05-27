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
		'manifest' => T_('Start Upgrade'),
		'review' => T_('Create Upgrade'),
		'update' => T_('Make Upgrade'),
		'install' => T_('Complete Upgrade'),
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
		}
		else {
			$this->toolBarButton('save');
			$this->toolBarButton('clear');
			$this->toolBarButton('manifest');
			$this->toolBarButton('install');
		}
	}

	private function configFields () {
		$fields ['general'] = array ('offline', 'sitename', 'site_slogan', 'offline_message', 'error_message', 'favicon', 'MetaDesc', 'MetaKeys', 'list_limit');
		$fields ['locale'] = array ('locale', 'lang', 'timezone', 'charset', 'locale_use_iconv', 'locale_use_gettext');
//		$fields ['mollom'] = array ('mollom_pub', 'mollom_priv');
		$fields ['technical'] = array ('debug', 'locale_debug', 'error_reporting', 'no_user_session', 'max_load', 'max_query_time', 'lifetime', 'adminlife', 'privateip', 'caching', 'cachetype', 'cache_server', 'cachetime', 'fileperms', 'dirperms');
		$fields ['mail'] = array ('mailer', 'mailfrom', 'fromname', 'errormailto', 'sendmail', 'smtpauth', 'smtpuser', 'smtppass', 'smtphost');
//		$fields['content'] = array ('hideAuthor', 'hideCreateDate', 'hideModifyDate', 'hidePdf', 'hidePrint', 'hideEmail', 'vote', 'MetaAuthor', 'MetaTitle', 'mbf_content',
//		'multipage_toc', 'link_titles', 'item_navigation', 'readmore', 'hits', 'icons');
		$fields['amazons3'] = array ('s3accesskey', 's3secretkey', 's3bucket');
		return $fields;
	}
	
	private function getCacheStorageOptions () {
		$dir = new aliroDirectory(_ALIRO_CLASS_BASE.'/classes/');
		$options = $dir->listFiles('^aliroCache[A-Za-z]+Storage.php$');
		return array_map(array($this, 'pullStorage'), $options);
	}
	
	private function pullStorage ($filename) {
		return substr($filename, 10, strlen($filename) - 21);
	}

	public function listTask () {
		$unpacked = aliroCore::getConfigData('configuration.php');
		$view = new $this->view_class($this);
		$view->showConfig($unpacked, $this->configFields(), $this->getCacheStorageOptions(), aliroCore::getConfigData('corecredentials.php'), aliroCore::getConfigData('credentials.php'));
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
		//if (!isset($config['enable_stats'])) $config['enable_stats'] = 0;
		if (!isset($config['offset'])) $config['offset'] = 0;
		$language = aliroLanguage::getInstance();
		if (!$language->validCharset($config['charset'])) $config['charset'] = 'utf-8';
		$freshinstall = new aliroInstall();
		$freshinstall->storeConfig($config, 'configuration.php', true);
		$message = T_('Non-database configuration updated');
		if ($freshinstall->checkDatabaseConfig('gen', $credentials)) {
			if ($freshinstall->checkDatabaseConfig('core', $corecredentials)) {
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
		aliroCache::deleteAll();
		$this->redirect( 'index.php?core='.$this->option, $message, $severity);
	}
	
	public function manifestTask () {
		$manifest = new aliroMakeManifest();
		$manifest->startUpgrade();
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
		}
		else aliroRequest::getInstance()->setErrorMessage(T_('No manifest was received for analysis'), _ALIRO_ERROR_FATAL);
	}

	public function installTask () {
		$manifest = new aliroMakeManifest();
		$manifest->installManifest();

		aliroCache::deleteAll();

		try {
			$database = aliroCoreDatabase::getInstance();
			$sql = file_get_contents(_ALIRO_ADMIN_CLASS_BASE.'/sql/aliro_core.sql');
			$database->setQuery($sql);
			$database->query_batch();
			$database->DBUpgrade();

			$database = aliroDatabase::getInstance();
			$sql = file_get_contents(_ALIRO_ADMIN_CLASS_BASE.'/sql/aliro_general.sql');
			$database->setQuery($sql);
			$database->query_batch();
			$database->DBUpgrade();
    	} catch (databaseException $exception) {
    		$message = sprintf(T_('A database error occurred on %s at %s while attempting upgrade'), date('Y-M-d'), date('H:i:s'));
    		$errorkey = "SQL/{$exception->getCode()}/$exception->dbname/{$exception->getMessage()}/$exception->sql";
    		aliroErrorRecorder::getInstance()->recordError($message, $errorkey, $message, $exception);
    		aliroRequest::getInstance()->setErrorMessage($message, _ALIRO_ERROR_SEVERE);
    	}

		aliroCache::deleteAll();
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
		aliroCache::deleteAll();
		$this->listTask();
	}

}