<?php
/**
* Aliro 2.0 Installer
*/

class installerAdminInstaller extends aliroComponentAdminControllers {

	private static $instance = __CLASS__;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	public function getRequestData () {
		// No actions currently required
	}

	public function checkPermission () {
		return $this->authoriser->checkUserPermission('manage', 'mosExtension', 0);
	}

	public function toolbar () {
		$toolbar = aliroAdminToolbar::getInstance();
		switch ($this->task){
			case "new":
				$toolbar->	save();
				$toolbar->	cancel();
			break;

			case 'list':
			default:
	    	$element = $this->getParam( $_REQUEST, 'element');
	    	if ($element == 'component' OR $element == 'module' OR $element == 'mambot') {
				$toolbar->	deleteList( '', 'remove', T_('Uninstall'));
				$toolbar->	help( '453.screen.installer.cmm' );
			} else {
				$toolbar->	help( '453.screen.installer.lang' );
			}
			break;
		}
	}

	public function listTask () {
		$viewer = new HTML_installer($this);
	 	$viewer->showInstallForm( T_('Install Aliro extension (component, module, mambot, template, include, parameter, composite)'), 'universal', '', dirname(__FILE__) );
	}

	public function uploadfileTask () {
		$installer = new aliroInstaller();
		$installer->uploadfile();
	 	$viewer = new HTML_installer($this)	;
	 	$viewer->showInstallForm( T_('Install Aliro extension (component, module, mambot, template, include, parameter, composite)'), 'universal', '', dirname(__FILE__) );
	}

	public function installfromurlTask () {
		$installer = new aliroInstaller();
		$installer->installfromurl();
	 	$viewer = new HTML_installer($this)	;
	 	$viewer->showInstallForm( T_('Install Aliro extension (component, module, mambot, template, include, parameter, composite)'), 'universal', '', dirname(__FILE__) );
	}

	public function installfromdirTask () {
		$installer = new aliroInstaller();
		$installer->installfromfile();
	 	$viewer = new HTML_installer($this)	;
	 	$viewer->showInstallForm( T_('Install Aliro extension (component, module, mambot, template, include, parameter, composite)'), 'universal', '', dirname(__FILE__) );
	}

	public function installfromAliroTask () {
		$installer = new aliroInstaller();
		$installer->installfromurl('aliro');
	 	$viewer = new HTML_installer($this)	;
	 	$viewer->showInstallForm( T_('Install Aliro extension (component, module, mambot, template, include, parameter, composite)'), 'universal', '', dirname(__FILE__) );
	}

	public function aliroTask () {
		$viewer = new HTML_installer($this)	;
	 	$viewer->aliroForm ($option, $element, $client);
	}

}

?>