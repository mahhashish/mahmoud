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
 * aliroScreenArea is the abstract class used to define a browser area - the locations
 * into which module output is placed.  It also provides a static method for building
 * the output for all screen areas for a template.  The output is captured at this point,
 * so that module code is able to create new information in the HTML header etc.
 *
 * aliroUserScreenArea and aliroAdminScreenArea are the classes that are actually
 * instantiated on the user and admin sides respectively.  Further work is needed
 * clarify the operation of admin side modules.
 *
 */

abstract class aliroScreenArea {
	public $name = '';
	public $min_width = 0;
	public $max_width = 0;
	public $style = 0;
	protected $screen_data = '';

	public function __construct ($name, $min_width, $max_width, $style) {
		$this->name = $name;
		$this->min_width = $min_width;
		$this->max_width = $max_width;
		$this->style = $style;
	}

	public static function prepareTemplate ($template) {
		$areas = $template->positions();
		foreach ($areas as $area) {
			ob_start();
			$area->loadModules($template);
			$area->setData(ob_get_contents());
			ob_end_clean();
		}
	}
	
	public function setData ($data) {
		$this->screen_data = $data;
	}

	public function addData ($data) {
		$this->screen_data .= $data;
	}

	public function getData () {
		return $this->screen_data;
	}

}

class aliroUserScreenArea extends aliroScreenArea {

	public function countModules () {
		return aliroModuleHandler::getInstance()->countModules($this->name, false);
	}

	public function loadModules ($template) {
		$modules = aliroModuleHandler::getInstance()->getModules($this->name, false);
		foreach ($modules as $module) {
			// Could add output directly into module object, but this method captures any diagnostic etc output
			echo $module->renderModule($this, $template);
		}
	}

}

class aliroAdminScreenArea extends aliroScreenArea {

	public function countModules () {
		return aliroModuleHandler::getInstance()->countModules($this->name, true);
	}

	public function loadModules ($template) {
		$modules = aliroModuleHandler::getInstance()->getModules($this->name, true);
		$authoriser = aliroAuthoriser::getInstance();
		foreach ($modules as $module) {
			if ($authoriser->checkUserPermission ('view', 'aliroModule', $module->id)) {
				// $moduleid was second parameter, but not being supplied???
				// if ($moduleid AND $moduleid != $module->id) echo $module->renderModuleTitle($this, $template);
				echo $module->renderModule($this, $template);
			}
		}
	}

}