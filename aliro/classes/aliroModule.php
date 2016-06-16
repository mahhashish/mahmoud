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
 * aliroLoginDetails is a simple data class used to create an object to carry the
 * information from a user login - user ID, password and the flag for whether the
 * system is to "remember" the user and automatically log them in.  The main use
 * for objects of this class is to pass data to mambots related to the authentication
 * process.
 *
 * aliroExtensionHandler knows all about the various installed extensions in
 * the system.  Anything not integral to the core - components, modules, mambots,
 * templates - are counted as extensions.  It is a cached singleton class and
 * uses common code the implement the object cache.
 *
 * aliroAuthenticator is the abstract class that contains common code for use
 * on both the user and admin sides of Aliro.
 *
 * aliroUserAuthenticator is the class that is instantiated to handle user side
 * authentication - basically login and logout.  On the user side, the actual
 * authentication is done by mambots.  The default Aliro authentication mambot
 * checks the credentials against the database, although it calls back to the
 * aliroUserAuthenticator class to perform the actual validation.  It is possible
 * to supplement the default processing with other mambots, or replace it
 * completely.  Uses for such an approach might include use of an LDAP system.
 * There are several mambot "hooks" and the other purpose for this is to be able
 * to integrate extensions that elaborate the handling of users with additional
 * properties and such like.
 *
 * aliroModuleHandler is a cached singleton class that looks after all the data
 * for modules within Aliro.  It is optimised towards creating useful data
 * structures in the constructor, which are then cached.  The access methods
 * are as simple as possible, so as to give the best run time performance.
 *
 * aliroModule is the object that corresponds to an entry in the module table.
 * In addition, it has methods to assist in the rendering of modules on the
 * browser screen.  Details of format are referred to the template, so that
 * control of XHTML is kept out of the core.
 *
 */

class aliroModuleHandler extends aliroCommonExtHandler {
	protected static $instance = __CLASS__;

	private $allModules = array();
	private $keyToSubscript = array();
	private $user_area_links = array();
	private $admin_area_links = array();
	private $allMenusByModule = array();
	private $allModulesByMenu = array();
	private $distinct_user_side = array();
	private $visibleKeys = array();
	private $modulesByFormalName = array();

	protected $extensiondir = '/modules/';

	protected function __construct () {
		$query = "SELECT m1.*, (CASE WHEN m2.menuid = 0 THEN 'All' WHEN m2.menuid IS NULL THEN 'None' ELSE 'Varies' END) pages"
		." FROM `#__modules` m1 LEFT JOIN `#__modules_menu` m2 ON m1.id = m2.moduleid"
		." GROUP BY m1.id ORDER BY m1.position, m1.ordering";
		$database = aliroCoreDatabase::getInstance();
		if ($result = $database->doSQLget($query, 'aliroModule')) $this->allModules = $result;
		$translatePages = array ('All' => T_('All'), 'None' => T_('None'), 'Varies' => T_('Varies'));
		foreach ($this->allModules as $sub=>&$module) {
			$this->keyToSubscript[$module->id] = $sub;
			$this->modulesByFormalName[$module->module] = $sub;
			$module->pages = $translatePages[$module->pages];
			if ($module->published) {
				if ($module->admin & 1) $this->user_area_links[$module->position][] = $module->id;
				if ($module->admin & 2) $this->admin_area_links[$module->position][] = $module->id;
			}
			if ($module->admin & 1) $distinct_user_side[$module->module] = 1;
		}
		if (isset($distinct_user_side)) {
			$this->distinct_user_side = array_keys($distinct_user_side);
			sort($this->distinct_user_side);
		}
		$database->setQuery ("SELECT * FROM #__modules_menu");
		if ($menus = $database->loadObjectList()) foreach ($menus as $menu) {
			$this->allMenusByModule[$menu->moduleid][] = $menu->menuid;
		}
		$database->setQuery("SELECT m1.menuid, m2.moduleid FROM `#__modules_menu` m1"
		." INNER JOIN `#__modules_menu` m2 ON m1.menuid = m2.menuid OR m2.menuid =0"
		." GROUP BY m1.menuid, m2.moduleid");
		if ($menus = $database->loadObjectList()) foreach ($menus as $menu) {
			$this->allModulesByMenu[$menu->menuid][] = $menu->moduleid;
		}
	}

	// Singleton accessor with cache
	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}

	public function makeModuleFromExtension ($extension) {
		$newmodule = new aliroModule();
		$newmodule->title = T_('Please select a title');
		// Can't set ordering until we know position
		$newmodule->published = 1;
		$newmodule->module = $extension->formalname;
		$newmodule->showtitle = 1;
		$newmodule->admin = $extension->admin;
		$newmodule->class = $extension->class;
		$newmodule->adminclass = $extension->adminclass;
		return $newmodule;
	}

	private function getVisibleKeys ($position, $isAdmin) {
		if (isset($this->visibleKeys[$position][$isAdmin])) return $this->visibleKeys[$position][$isAdmin];
		$result = array();
		if ($isAdmin) $elements = isset($this->admin_area_links[$position]) ? $this->admin_area_links[$position] : array();
		else $elements = isset($this->user_area_links[$position]) ? $this->user_area_links[$position] : array();
		$currentmenu = aliroRequest::getInstance()->getItemid();
		if (!isset($this->allModulesByMenu[$currentmenu])) $currentmenu = 0;
		if (isset($this->allModulesByMenu[$currentmenu])) {
			$elements = array_intersect($elements, $this->allModulesByMenu[$currentmenu]);
			$authoriser = aliroAuthoriser::getInstance();
			foreach ($elements as $element) if ($authoriser->checkUserPermission ('view', 'aliroModule', $element)) $result[] = $element;
		}
		$this->visibleKeys[$position][$isAdmin] = $result;
		return $result;
	}

	public function countModules ($position, $isAdmin) {
		return count($this->getVisibleKeys ($position, $isAdmin));
	}

	public function getModules ($position, $isAdmin) {
		$result = array();
		$keys = $this->getVisibleKeys ($position, $isAdmin);
		foreach ($keys as $key) $result[] = $this->allModules[$this->keyToSubscript[$key]];
		return $result;
	}

	public function getModuleByID ($id) {
		return isset($this->allModules[$this->keyToSubscript[$id]]) ? $this->allModules[$this->keyToSubscript[$id]] : null;
	}
	
	public function getModuleByFormalName ($formalname) {
		return isset($this->modulesByFormalName[$formalname]) ? $this->allModules[$this->modulesByFormalName[$formalname]] : null;
	}

	public function getSelectedModules ($position='', $formalname='', $search='', $admin=false) {
		$results = array();
		foreach ($this->allModules as $module) {
			if ($admin) {
				if (!($module->admin & 2)) continue;
			}
			elseif (!($module->admin & 1)) continue;
			if ($position AND $module->position != $position) continue;
			if ($formalname AND $module->module != $formalname) continue;
			if ($search AND strpos(strtolower($module->title), $search) === false) continue;
			$results[] = $module;
		}
		return $results;
	}

	public function getModulesByPosition ($admin) {
		$results = array();
		$check = $admin ? 2 : 1;
		foreach ($this->allModules as $module) {
			if ($module->admin & $check) $results[$module->position][] = $module;
		}
		return $results;
	}

	public function getMenus ($module_id) {
		return isset($this->allMenusByModule[$module_id]) ? $this->allMenusByModule[$module_id] : array();
	}

	public function getDistinctNames () {
		return $this->distinct_user_side;
	}

	public function deleteModules ($ids) {
		foreach ($ids as &$id) $id = intval($id);
		$idlist = implode (',', $ids);
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL ("DELETE FROM #__modules WHERE id IN ('$idlist')");
		$database->doSQL ("DELETE FROM #__modules_menu WHERE moduleid IN ('$idlist')");
		$this->clearCache();
	}

	public function publishModules ($ids, $new_publish) {
		foreach ($ids as &$id) $id = intval($id);
		$new_publish = intval($new_publish);
		$idlist = implode (',', $ids);
		$database = aliroCoreDatabase::getInstance();
		$database->doSQL ("UPDATE #__modules SET published = $new_publish WHERE id IN ($idlist)");
		$this->clearCache();
	}

	public function changeOrder ($id, $direction) {
		$module = $this->allModules[$this->keyToSubscript[$id]];
		$movement = 'down' == $direction ? 15 : -15;
		$this->updateOrdering (array($id => $module->ordering + $movement));
	}

	public function updateOrdering ($orders) {
		foreach ($orders as $id=>$order) {
			$module =  $this->allModules[$this->keyToSubscript[$id]];
			if ($module->ordering != $order) $changes[$id] = $order;
		}
		foreach ($this->allModules as $module) {
			$ordering = isset($changes[$module->id]) ? $changes[$module->id] : $module->ordering;
			$allmodules[$module->position][$ordering] = $module->id;
		}
		$changed = false;
		$query = "UPDATE #__modules SET ordering = CASE ";
		foreach ($allmodules as $position=>$orderings) {
			$order = 10;
			ksort($orderings);
			foreach ($orderings as $ordering=>$id) {
				$module = $this->allModules[$this->keyToSubscript[$id]];
				if ($order != $module->ordering) {
					$query .= "WHEN id = $id THEN $order ";
					$changed = true;
				}
				$order += 10;
			}
		}
		if ($changed) {
			$query .= 'ELSE ordering END';
			aliroCoreDatabase::getInstance()->doSQL ($query);
			$this->clearCache();
		}
	}

}

/**
* Module database table class
* Aliro
*/
class aliroModule extends aliroDatabaseRow {
	protected $DBclass = 'aliroCoreDatabase';
	protected $tableName = '#__modules';
	protected $rowKey = 'id';
	protected $handler = 'aliroModuleHandler';
	protected $formalfield = 'module';

	// overloaded check function
	public function check() {
		// check for presence of a name
		if (trim( $this->title ) == '') {
			$this->_error = T_('Your Module must contain a title.');
			return false;
		}
		return true;
	}

	public function getParams () {
	    $params = new aliroParameters ($this->params);
	    return $params;
	}

	public function loadLanguage () {
		// check for custom language file
		$basepath = criticalInfo::getInstance()->absolute_path.'/modules/'.$this->module;
		$path = $basepath.aliroCore::get('mosConfig_lang').'.php';
		if (file_exists( $path )) include( $path );
		else {
			$path = $basepath.'.en.php';
			if (file_exists( $path )) include( $path );
		}
	}

	public function renderModule ($area, $template) {
		$this->loadLanguage();
		$params = $this->getParams();
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		$title = $this->showtitle ? $this->title : '';
		$moduleclass = ($this->admin & 2) ? $this->adminclass : $this->class;
		$modobject = new $moduleclass;
		$modobject->activate($this, $content, $area, $params);
		$method = 'moduleStyle'.$area->style;
		return $template->$method($moduleclass_sfx, $title, $content);
	}

	public function renderModuleTitle ($area, $template) {
		$params = $this->getParams();
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		$title = $this->showtitle ? $this->title : '';
		$method = 'moduleStyle'.$area->style;
		return $template->$method($moduleclass_sfx, $title, '');
	}

}