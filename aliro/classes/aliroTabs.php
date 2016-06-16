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
 * Everything here is to do with database management.
 *
 * aliroTabs is the class that supports easy creation of tabbed displays using
 * Erik Arvidsson's Tab Pane JavaScript based tabbing utility
 *
 * mosTabs is for backwards compatibility
 *
 */

class aliroTabs {
	private $useCookies = 0;

	function __construct($useCookies) {
		$request = aliroRequest::getInstance();
		$tab_headers = <<<TAB_TAGS

<link id="luna-tab-style-sheet" type="text/css" rel="stylesheet" href="{$request->getCfg('live_site')}/includes/js/tabs/tab.css" />
<script type="text/javascript" src="{$request->getCfg('live_site')}/includes/js/tabs/tabpane.js"></script>

TAB_TAGS;

		$request->addCustomHeadTag($tab_headers);
		$this->useCookies = $useCookies;
	}

	function startPane($id){
		echo <<<START_PANE

		<div class="tab-page" id="$id">
		<script type="text/javascript">
			var tabPane1 = new WebFXTabPane( document.getElementById( "$id" ), $this->useCookies )
		</script>

START_PANE;

	}

	function endPane() {
		echo "</div>";
	}

	static function startTab( $tabText, $paneid ) {
		echo <<<START_TAB

		<div class="tab-page" id="$paneid">
		<h2 class="tab">$tabText</h2>
		<script type="text/javascript">
			tabPane1.addTabPage( document.getElementById( "$paneid" ) );
		</script>

START_TAB;

	}

	static function endTab() {
		echo "</div>";
	}
}

class mosTabs extends aliroTabs {

	function mosTabs ($useCookies) {
		parent::__construct($useCookies);
	}

}