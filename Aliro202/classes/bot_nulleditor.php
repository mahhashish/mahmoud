<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more
 * information on this, please see the index.php file or visit
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * bot_nulleditor is a default editor plugin that simply handles input as plain
 * text in an HTML textarea element.  It is used if no other editor is installed.
 * 
 */

class bot_nulleditor {

	public function perform () {
		$args = func_get_args();
		$event = array_shift($args);
		$botparams = array_shift($args);
		$published = array_shift($args);
		switch ($event) {
			case 'onIniEditor':
				return $this->init ();

			case 'onGetEditorContents':
				return call_user_func_array(array($this,'getContents'), $args);

			case 'onEditorArea':
				return call_user_func_array(array($this,'editorArea'), $args);

		}
	}

/**
* Aliro null editor
* No WYSIWYG Editor - javascript initialisation
*/
private function init() {
	return <<<EOD
<script type="text/javascript">
	function insertAtCursor(myField, myValue) {
		if (document.selection) {
			// IE support
			myField.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
		} else if (myField.selectionStart || myField.selectionStart == '0') {
			// MOZILLA/NETSCAPE support
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			myField.value = myField.value.substring(0, startPos)
				+ myValue
				+ myField.value.substring(endPos, myField.value.length);
		} else {
			myField.value += myValue;
		}
	}
</script>
EOD;
}
/**
* No WYSIWYG Editor - copy editor contents to form field
* @param string The name of the editor area
* @param string The name of the form field
*/
private function getContents( $editorArea, $hiddenField ) {
	return <<<EOD
EOD;
}
/**
* No WYSIWYG Editor - display the editor
* @param string The name of the editor area
* @param string The content of the field
* @param string The name of the form field
* @param string The width of the editor area
* @param string The height of the editor area
* @param int The number of columns for the editor area
* @param int The number of rows for the editor area
*/
private function editorArea( $name, $content, $hiddenField, $width, $height, $col, $row ) {
/*
echo '<br />Name='.$name;
echo '<br />Content='.$content;
echo '<br />hiddenField='.$hiddenField;
echo '<br />width='.$width;
echo '<br />height='.$height;
echo '<br />col='.$col;
echo '<br />row='.$row;
*/
	$mhandler = aliroMambotHandler::getInstance();
	$results = $mhandler->trigger('onCustomEditorButton');
	$buttons = array();
	foreach ($results as $result) {
	    $buttons[] = '<img src="'.aliroCore::getInstance()->getCfg('live_site').'/mambots/editors-xtd/'.$result[0].'" onclick="insertAtCursor( document.adminForm.'.$hiddenField.', \''.$result[1].'\' )" />';
	}
	$buttons = implode( "", $buttons );
	return <<<EOD
<textarea name="$hiddenField" id="$hiddenField" cols="$col" rows="$row" style="width:{$width}px;height:{$height}px;">$content</textarea>
<br />$buttons
EOD;
}

}