<?php

/* ******************************************************************
* This file is a generic interface to Aliro, Joomla 1.5+, Joomla 1.0.x and Mambo
* Copyright (c) 2008 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://acmsapi.org
* To contact Martin Brampton, write to martin@remository.com
*
*/

class cmsapiInstaller {
	protected $http = null;
	protected $isJoomla25 = false;
	protected $database = null;

	public function __construct () {
		$this->database = aliroDatabase::getInstance();
	}

	public function getFileData ($url, $security='') {
		$this->http = new aliroHTTP();
		if ($security) $this->http->header($security);
		$contents = $this->http->get($url);
		$result = $this->http->getHttpStatus();
		if (200 != $result) {
			aliroRequest::getInstance()->setErrorMessage(T_('Server connect failed').', '.$result);
			return false;
		}
		return $contents;
	}

	public function storeFile ($contents, $url) {
		if ($this->http instanceof aliroHTTP) {
			$response_headers = $this->http->getHeaders();
			foreach ($response_headers as $header) {
				if (0 === strpos($header, 'Content-Disposition')) {
					$contentfilename = explode ("\"", $header);
					if (isset($contentfilename[1])) {
						$target = $contentfilename[1];
						break;
					}
				}
			}
		}
		// Set the target path if not given
		if (empty($target)) $target = $this->getFilenameFromURL($url);
		$manager = aliroFileManager::getInstance();
		$path = $manager->makeTemp().$target;
		file_put_contents($path, $contents);
		$installer = new aliroInstaller();
		$installer->installFromFile($path, true);
		return $path;
	}

	private function getFilenameFromURL ($url) {
		return is_string($url) ? end(explode('/', $url)) : false;
	}

	public function unpack ($name) {
		return true;
	}

	public function installPackage ($package) {
		return true;
	}

	public function cleanUp ($package) {
	}

	public function securityFieldsHTML () {
		$usertext = T_( 'Username' );
		$passtext = T_( 'Password' );
		$username = @aliroComponentConfiguration::getConfiguration('plugin_jaliro')->user;
		$password = @aliroComponentConfiguration::getConfiguration('plugin_jaliro')->password;
		return <<<SECURITY_FIELDS

			<label for="jaliro_user"> Jaliro $usertext:</label>
			<input class="input_box" id="jaliro_user" name="jaliro_user" type="text" size="20" value="$username" />
			<label for="jaliro_pass"> Jaliro $passtext:</label>
			<input class="input_box" id="jaliro_pass" name="jaliro_pass" type="password" size="20" value="$password" />

SECURITY_FIELDS;

	}
	public function makeMenuEntry () {
		$componentnum = $this->getComponentID();
		if ($componentnum AND $this->getMenuCount()) {
			$cidname = $this->isJoomla25 ? 'component_id' : 'componentid';
			$this->database->setQuery("UPDATE #__menu SET $cidname = $componentnum WHERE link LIKE 'index.php?option=$this->cname%'");
			$this->database->query();
			return;
		}
		if ('Aliro' == _CMSAPI_CMS_BASE) $this->makeAliroMenuEntry();
		elseif ('Joomla' == _CMSAPI_CMS_BASE) $this->makeJoomlaMenuEntry();
		else {
			$this->database->setQuery("SELECT MAX(ordering) FROM `#__menu`");
			$ordering = intval($this->database->loadResult() + 1);
			$this->database->setQuery("INSERT INTO `#__menu` "
			." (`id`, `menutype`, `name`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`) "
			." VALUES (NULL , 'mainmenu', '$this->ctitle', 'index.php?option=$this->cname', 'components', '1', '0', $componentnum, '0', $ordering, '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '')");
			$this->database->query();
		}
	}
	
	protected function makeAliroMenuEntry () {
		$newmenu = new aliroMenuItem();
		$extension = aliroExtensionHandler::getInstance()->getExtensionByName($this->cname);
		if ($extension instanceof aliroExtension) {
			$newmenu->menutype = 'mainmenu';
			$newmenu->name = $extension->name;
			$newmenu->link = 'index.php?option='.$this->cname;
			$newmenu->type = 'component';
			$newmenu->component = $this->cname;
			$newmenu->published = 1;
			$newmenu->componentid = $extension->id;
			aliroMenuHandler::getInstance()->saveMenu($newmenu);
		}
	}
	
	protected function makeJoomlaMenuEntry () {}
	
	protected function getComponentID () {
		if ('Aliro' == _CMSAPI_CMS_BASE) {
			$extension = aliroExtensionHandler::getInstance()->getExtensionByName($this->cname);
			return $extension instanceof aliroExtension ? $extension->id : 0;
		}
		$this->database->setQuery("SELECT MIN(id) FROM `#__components` WHERE `option` = '$this->cname'");
		return intval($this->database->loadResult());
	}
	
	protected function getMenuCount () {
		if ('Aliro' == _CMSAPI_CMS_BASE) {
			$extension = aliroExtensionHandler::getInstance()->getExtensionByName($this->cname);
			return $extension instanceof aliroExtension ? aliroMenuHandler::getInstance()->getCountByTypeComponentID('mainmenu', (array) $extension->id) : 0;
		}
		$this->database->setQuery("SELECT count(*) FROM `#__menu` WHERE link LIKE 'index.php?option={$this->cname}%'");
		return intval($this->database->loadResult());
	}
}