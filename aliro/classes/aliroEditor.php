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
 * aliroEditor is the basic framework for editors.  The actual editors are mambots
 * and the methods provided here trigger the active editor mambot(s).  The class is
 * a singleton but does not have any data suitable to be cached.
 *
 */

class aliroEditor {

	private static $instance = __CLASS__;
	private $mambothandler = '';
	private $initiated = false;

	private function __construct () {
		$this->mambothandler = aliroMambotHandler::getInstance();
	}

	private function __clone () {
		// Just here to enforce singleton
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	public function initEditor() {
		$this->initiated = true;
		return $this->triggerEditor ('onIniEditor');
	}

	public function getEditorContents( $editorArea, $hiddenField ) {
		echo $this->getEditorContentsText($editorArea, $hiddenField);
	}

	public function getEditorContentsText ( $editorArea, $hiddenField ) {
		if (!$this->initiated) $this->initEditor();
		return $this->triggerEditor ('onGetEditorContents', array($editorArea, $hiddenField));
	}

	public function editorAreaText ($name, $content, $hiddenField, $width, $height, $col, $row) {
		if (!$this->initiated) $this->initEditor();
		return $this->triggerEditor ('onEditorArea', array($name, $content, $hiddenField, $width, $height, $col, $row));
	}
	// just present a textarea
	public function editorArea( $name, $content, $hiddenField, $width, $height, $col, $row ) {
		echo $this->editorAreaText ($name, $content, $hiddenField, $width, $height, $col, $row);
	}

	private function triggerEditor ($trigger, $arguments=null) {
		$html = '';
		if ($arguments) $results = call_user_func(array($this->mambothandler, 'triggerOnce'), $trigger, $arguments);
		else $results = call_user_func(array($this->mambothandler, 'triggerOnce'), $trigger);
		foreach ($results as $result) $html .= trim($result);
		return $html;
	}

}