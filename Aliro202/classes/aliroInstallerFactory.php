<?php

class aliroInstallerFactory {
	
	public static function getInstaller () {
		if (aliro::getInstance()->classExists('oemInstall')) $result = new oemInstall();
		else $result = new aliroInstall();
		return $result;
	}
}