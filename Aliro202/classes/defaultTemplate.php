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
 * The default template is a rudimentary template with no CSS that causes output to be
 * displayed to the browser.  It will not look good, but it means that Aliro continues
 * to be functional even if all templates are uninstalled.
 *
 */

class defaultTemplate extends aliroUserTemplateBase implements ifAliroTemplate {
	// Options are 'xhtml_10_trans', 'xhtml_10_strict' or 'xhtml_11'
	protected $doctype = 'xhtml_10_trans';
	// Formal name of this template
	protected $tname = '';
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
		<p class="legal">&copy;2012 All Rights Reserved.</p>
		<p class="credit">Based on a design by <a href="http://www.freecsstemplates.org/">Free CSS Templates</a></p>
		{$this->version->footer()}
	</div>	
	{$this->debugOutput()}
</body>
</html>

PAGE_HTML;

	}

}