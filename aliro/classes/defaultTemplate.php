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
 * The default template is a rudimentary template with no CSS that causes output to be
 * displayed to the browser.  It will not look good, but it means that Aliro continues
 * to be functional even if all templates are uninstalled.
 *
 */

class defaultTemplate extends aliroUserTemplateBase implements ifAliroTemplate {
	// Options are 'xhtml_10_trans', 'xhtml_10_strict' or 'xhtml_11'
	protected $doctype = 'xhtml_10_trans';
	// Formal name of this template
	protected $tname = '.';
	// File name of CSS file, relative to the template
	protected $cssname = 'default.css';

	public function __construct () {
		// Define screen areas
		$this->areas = array (
		array ('position' => 'main', 		'min' => 519, 'max' => 519, 'style' => 1),
		array ('position' => 'topmenu',		'min' => 519, 'max' => 519, 'style' => 1),
		array ('position' => 'navigation', 	'min' => 200, 'max' => 200, 'style' => 'Side'),
		array ('position' => 'searchbox', 	'min' => 259, 'max' => 259, 'style' => 0),
		array ('position' => 'feature1', 	'min' => 259, 'max' => 259, 'style' => 0),
		array ('position' => 'feature2', 	'min' => 200, 'max' => 200, 'style' => 0),
		array ('position' => 'newsflash', 	'min' => 200, 'max' => 200, 'style' => 'Side'),
		array ('position' => 'extra', 		'min' => 200, 'max' => 200, 'style' => 'Side'),
		array ('position' => 'banner', 		'min' => 468, 'max' => 468, 'style' => 0),
		array ('position' => 'debug', 		'min' => 400, 'max' => 400, 'style' => 1)
		);
		// All the Aliro error levels must have matching colour classes, with definitions in the CSS
		$this->colours = array (
		_ALIRO_ERROR_FATAL => 'fatalcolour',
		_ALIRO_ERROR_SEVERE => 'severecolour',
		_ALIRO_ERROR_WARN => 'warncolour',
		_ALIRO_ERROR_INFORM => 'informcolour'
		);
		parent::__construct();
	}

	// Define the default module position
	public static function defaultModulePosition () {
		return 'extra';
	}
	
	// Return the screen area used for the main body
	public function mainScreenBox () {
		return $this->screenarea['main'];
	}


	// These "style" methods are invoked by the module handler as modules are processed
	// Their function is to place any required HTML around the content created by the module
	// The digit on the end relates to the style number for a screen area
	public function moduleStyle0 ($moduleclass_sfx, $title, $content) {
		$html =  "<table cellpadding=\"0\" cellspacing=\"0\" class=\"moduletable$moduleclass_sfx\">";
		if ($title) $html .= '<tr><th valign="top">'.$title.'</th></tr>';
		$html .= '<tr><td>';
		$html .= $content;
		$html .= '</td></tr></table>';
		return $html;
	}

	public function moduleStyle1 ($moduleclass_sfx, $title, $content) {
		return $content;
	}
	
	public function moduleStyleSide ($moduleclass_sfx, $title, $content) {
		if ($title) return <<<WITH_TITLE
		
				<li><h2>$title</h2>
					$content
				</li>
				
WITH_TITLE;

		else return <<<NO_TITLE
		
			<li>
				$content
			</li>
			
NO_TITLE;

	}

	// HTML for the feature1 and feature1 areas is conditional on there being anything in either area
	private function makeFeatures () {
		if ($this->screenarea['feature1']->countModules() >= 1 OR $this->screenarea['feature2']->countModules() >= 1 ) {
			$html = <<<FEATURES

						<div id="content_top_wrapper">
							<!-- start content top 1.  -->
							<div id="content_top1">
							{$this->screenarea['feature1']->getData()}
							</div>
							<!-- end content top 1 -->
							<!-- start content top 2.  -->
							<div id="content_top2">
							{$this->screenarea['feature2']->getData()}
							</div>
							<!-- end content top 2 -->
						</div>

FEATURES;
		}
		else $html = '';
		return $html;
	}

	// HTML for banners is conditional on there being any banner modules present
	private function makeBanner () {
		if ($this->screenarea['banner']->countModules() >= 1) {
			$html = <<<BANNER

					<!-- start banner.  -->
					<div id="banner">
					{$this->screenarea['banner']->getData()}
					</div>
					<!-- end banner. -->

BANNER;
		}
		else $html = '';
		return $html;
	}

	public function render () {
		// Create the page
		echo <<<PAGE_HTML
{$this->header ()}

<body class="aliroDefault">
	<div id="headerwrapper">
		<div id="header">
			<div id="logo">
				<h1><a href="$this->live_site">$this->sitename</a></h1>
				<h2><a href="http://www.aliro.org">powered by Aliro</a></h2>
			</div>
			{$this->screenarea['topmenu']->getData()}
		</div>
	</div>
	<!-- end #headerwrapper -->
	<div id="page">
		<!-- start sidebar1 -->
		<div id="sidebar1" class="sidebar">
			<ul>
				<li id="search">
					{$this->screenarea['searchbox']->getData()}
				</li>
				{$this->screenarea['navigation']->getData()}
			</ul>
		</div>
		<!-- end sidebar1 -->

		<!-- start content -->
		<div id="content">
			<div id="pathway">
				{$this->pathway->makePathway()}
			</div>
			{$this->errorMessage()}
			<div class="post">
				<!-- start features -->
				{$this->makeFeatures()}
				<!-- end features -->
				<div id="maincontent">
					{$this->mainBody()}
					{$this->makeBanner()}
				</div>
			</div>
		</div>
		<!-- end content -->

		<!-- start sidebar2 -->
		<div id="sidebar2" class="sidebar">
			<ul>
				{$this->screenarea['newsflash']->getData()}
				{$this->screenarea['extra']->getData()}
			</ul>
		</div>
		<!-- end sidebar2 -->

	<div style="clear: both;">&nbsp;</div>
	</div>
	<!-- end page -->
	<div id="footer">
		<p class="legal">&copy;2008 All Rights Reserved.</p>
		<p class="credit">Based on a design by <a href="http://www.freecsstemplates.org/">Free CSS Templates</a></p>
		{$this->version->footer()}
	</div>	
	{$this->debugOutput()}
</body>
</html>

PAGE_HTML;

	}

}