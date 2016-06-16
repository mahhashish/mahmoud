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
 * aliroExtConfig is for creating configuration objects for extensions
 *
 */

class aliroExtConfig {
	private $formalname = '';
	private $configs = array();
	private $remove = array();
	private $changed = array();

	public function __construct ($formalname) {
		$this->formalname = $formalname;
		$database = aliroDatabase::getInstance();
		$database->setQuery("SELECT property, value FROM #__configurations WHERE system='Aliro' AND component='$formalname'");
		$results = $database->loadObjectList();
		if ($results) foreach ($results as $result) $this->configs[$result->property] = $result->value;
	}

	public function get ($property) {
		return isset($this->configs[$property]) ? $this->configs[$property] : null;
	}

	public function delete ($property) {
		if (isset($this->configs[$property])) unset ($this->configs[$property]);
		$this->remove[$property] = 1;
	}

	public function set ($property, $value) {
		$this->configs[$property] = $value;
		if (isset($this->remove[$property])) unset ($this->remove[$property]);
		$this->changed[$property] = 1;
	}

	public function update () {
		$database = aliroDatabase::getInstance();
		if (count($this->remove)) {
			$deletelist = "'".implode ("','", $this->remove)."'";
			$database->doSQL("DELETE FROM #__configurations WHERE system='Aliro' AND component='$this->formalname' AND property IN ($deletelist)");
		}
		if (count($this->changed)) {
			foreach (array_keys($this->changed) as $property) $setitem[] = " WHEN property = '$property' THEN '{$this->configs[$property]}'";
			$changelist = implode("\n", $setitem);
			$database->doSQL("UPDATE #__configurations SET value = CASE $changelist END WHERE system = 'Aliro' AND component='$this->formalname'");
		}

	}

}