<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Aliro is open source software, free to use, and licensed under GPL.
 * You can find the full licence at http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * The author freely draws attention to the fact that Aliro derives from Mambo,
 * software that is controlled by the Mambo Foundation.  However, this section
 * of code is totally new.  If it should contain any fragments that are similar
 * to Mambo, please bear in mind (1) there are only so many ways to do things
 * and (2) the author of Aliro is also the author and copyright owner for large
 * parts of Mambo 4.6.
 *
 * Tribute should be paid to all the developers who took Mambo to the stage
 * it had reached at the time Aliro was created.  It is a feature rich system
 * that contains a good deal of innovation.
 *
 * Your attention is also drawn to the fact that Aliro relies on other items of
 * open source software, which is very much in the spirit of open source.  Aliro
 * wishes to give credit to those items of code.  Please refer to
 * http://aliro.org/credits for details.  The credits are not included within
 * the Aliro package simply to avoid providing a marker that allows hackers to
 * identify the system.
 *
 * Copyright in this code is strictly reserved by its author, Martin Brampton.
 * If it seems appropriate, the copyright will be vested in the Aliro Organisation
 * at a suitable time.
 *
 * Copyright (c) 2007 Martin Brampton
 *
 * http://aliro.org
 *
 * counterpoint@aliro.org
 *
 * This file is solely to hold the admine side smart class mapper.
 *
 * The smartAdminClassMapper is used to find classes.  It has written into it the
 * locations for permanent classes on the user side, and separately holds locations
 * for external classes from third parties outside the Aliro project.  These are
 * from other open source projects.  The third source for class information is the
 * database, which contains details of installed classes.  Unlike the user side,
 * classes must be specifically identified in the map to constrain as much as
 * possible the code that will be loaded on the admin side.
 *
 */

class smartAdminClassMapper extends smartClassMapper {
	private static $instance = __CLASS__;

	private static $adminmap = array (
	'aliroAdminRequest' => 'aliroAdminRequest',
	'aliroAdminTemplateBase' => 'aliroAdminTemplateBase',
	'defaultAdminTemplate' => 'defaultAdminTemplate',
	'aliroAdminMenu' => 'aliroAdminMenu',
	'aliroAdminMenuHandler' => 'aliroAdminMenu',
	'aliroAdminToolbar' => 'aliroAdminToolbar',
	'aliroAdminPageNav' => 'aliroAdminPageNav',
	'aliroComponentAdminManager' => 'aliroComponentAdminManager',
	'aliroComponentAdminControllers' => 'aliroComponentAdminManager',
	'aliroDBUpdateController' => 'aliroDBUpdateController',
	'aliroMakeManifest' => 'aliroMakeManifest',
	'basicAdminHTML' => 'basicAdminHTML',
	'advancedAdminHTML' => 'basicAdminHTML',
	'widgetAdminHTML' => 'basicAdminHTML',
	'configAdminConfig' => 'cor_config/admin.config',
	'listConfigHTML' => 'cor_config/admin.config.html',
	'sefAdminSef' => 'cor_sef/admin.sef',
	'sefAdminPage404' => 'cor_sef/admin.sef',
	'sefAdminUri' => 'cor_sef/admin.sef',
	'sefAdminMetadata' => 'cor_sef/admin.sef',
	'sefAdminHTML' => 'cor_sef/admin.sef.html',
	'HTML_installer' => 'cor_installer/admin.installer.html',
	'HTML_component' => 'cor_installer/component.html',
	'HTML_module' => 'cor_installer/module.html',
	'HTML_mambot' => 'cor_installer/mambot.html',
	'modulesAdminModules' => 'cor_modules/admin.modules',
	'HTML_modules' => 'cor_modules/admin.modules.html',
	'mambotsAdminMambots' => 'cor_mambots/admin.mambots',
	'listMambotsHTML' => 'cor_mambots/admin.mambots.html',
	'templatesAdminTemplates' => 'cor_templates/admin.templates',
	'listTemplatesHTML' => 'cor_templates/admin.templates.html',
	'errorsAdminErrors' => 'cor_errors/admin.errors',
	'listErrorsHTML' => 'cor_errors/admin.errors.html',
	'err404AdminErr404' => 'cor_err404/admin.err404',
	'listErr404HTML' => 'cor_err404/admin.err404.html',
	'foldersAdminFolders' => 'cor_folders/admin.folders',
	'foldersAdminHTML' => 'cor_folders/admin.folders.html',
	'listFoldersHTML' => 'cor_folders/admin.folders.html',
	'editFoldersHTML' => 'cor_folders/admin.folders.html',
	'sysinfoAdminSysinfo' => 'cor_sysinfo/admin.sysinfo',
	'helpAdminHelp' => 'cor_help/admin.help',
	'aliroExtensionInstaller' => 'aliroExtensionInstaller',
	'aliroLanguageHandler' => 'aliroExtensionInstaller',
	'aliroPatchHandler' => 'aliroExtensionInstaller',
	'aliroIncludeHandler' => 'aliroExtensionInstaller',
	'aliroParameterHandler' => 'aliroExtensionInstaller',
	'installerAdminInstaller' => 'cor_installer/admin.installer',
	'aliroInstaller' => 'cor_installer/installer.class',
	'extensionsAdminExtensions' => 'cor_extensions/admin.extensions',
	'listExtensionsHTML' => 'cor_extensions/admin.extensions.html',
	'menutypesAdminMenutypes' => 'cor_menutypes/admin.menutypes',
	'listMenutypesHTML' => 'cor_menutypes/admin.menutypes.html',
	'menusAdminMenus' => 'cor_menus/admin.menus',
	'menuInterface' => 'cor_menus/admin.menus',
	'listMenusHTML' => 'cor_menus/admin.menus.html',
	'languagesControllers' => 'cor_languages/admin.languages',
	'catalogsView' => 'cor_languages/views/catalogs.view',
	'editView' => 'cor_languages/views/edit.view',
	'indexView' => 'cor_languages/views/index.view',
	'languageView' => 'cor_languages/views/language.view',
	'applyAction' => 'cor_languages/actions/apply.action',
	'auto_translateAction' => 'cor_languages/actions/auto_translate.action',
	'cancelAction' => 'cor_languages/actions/cancel.action',
	'convertAction' => 'cor_languages/actions/convert.action',
	'defaultAction' => 'cor_languages/actions/default.action',
	'editAction' => 'cor_languages/actions/edit.action',
	'exportAction' => 'cor_languages/actions/export.action',
	'extractAction' => 'cor_languages/actions/extract.action',
	'indexAction' => 'cor_languages/actions/index.action',
	'installAction' => 'cor_languages/actions/install.action',
	'newAction' => 'cor_languages/actions/new.action',
	'publishAction' => 'cor_languages/actions/publish.action',
	'removeAction' => 'cor_languages/actions/remove.action',
	'saveAction' => 'cor_languages/actions/save.action',
	'sortAction' => 'cor_languages/actions/sort.action',
	'translateAction' => 'cor_languages/actions/translate.action',
	'updateAction' => 'cor_languages/actions/update.action',
	'languagesAdminLanguages' => 'cor_languages/languagesAdmin',
	'languagesAdminCatalogs' => 'cor_languages/catalogsAdmin',
	'catalogsAdminLanguages' => 'cor_languages/catalogsAdmin',
	'tagsAdminTags' => 'cor_tags/admin.tags',
	'aliroTag' => 'cor_tags/admin.tags',
	'listTagsHTML' => 'cor_tags/admin.tags.html',
	'editTagsHTML' => 'cor_tags/admin.tags.html'
	);

	public static function getInstance () {
		if (!is_object(self::$instance)) {
			self::$instance = parent::getCachedSingleton(self::$instance);
			self::$instance->reset();
		}
		self::$instance->checkDynamic();
		return self::$instance;
	}

	protected function populateMap () {
	    $database = aliroCoreDatabase::getInstance();
	    $database->setQuery('SELECT * FROM #__classmap');
	    $maps = $database->loadObjectList();
	    $admindir = substr(criticalInfo::getInstance()->admin_dir, 1);
	    if ($maps) foreach ($maps as $map) {
	    	switch ($map->type) {
	    		case 'component':
					$path = ($map->side == 'admin') ? $admindir.'/components/' : 'components/';
					$path .= $map->formalname.'/';
					break;
	    		case 'module':
					$path = ($map->side == 'admin') ? $admindir.'/modules/' : 'modules/';
					$path .= $map->formalname.'/';
					break;
	    		case 'mambot':
					$path = 'mambots/'.$map->formalname.'/';
					break;
	    		case 'template':
					$path = ($map->side == 'admin') ? $admindir.'/templates/' : 'templates/';
					$path .= $map->formalname.'/';
					break;
	    		default: continue;
	    	}
			$this->saveMap($path, $map);
		}
	}

	protected function getClassPath ($classname) {
		if (isset(self::$adminmap[$classname])) {
			$debuginfo = aliroDebug::getInstance();
			$debuginfo->setDebugData ("About to load $classname, current free memory ".(is_callable('memory_get_usage') ? memory_get_usage() : 'not known').$this->timer->mark('seconds'));
			return str_replace('\\', '/', dirname(__FILE__)).'/classes/'.self::$adminmap[$classname].'.php';
		}
	    return parent::getClassPath($classname);
	}
	
}