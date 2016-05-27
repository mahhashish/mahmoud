<?php

/**
*
* This is the Wall Component, designed to be installed into Aliro to provide for message posting, comments, votes, etc.
*
* Copyright in this edition belongs to Martin Brampton
* Email - counterpoint@aliro.org
* Web - http://aliro.org
*
* Information about Aliro can be found at http://aliro.org
*
*/

// The Configurator for the Wall Component

class sefAdminConfig extends sefAdminControllers {
	private static $instance = null;
	
	protected $configItems = '';
	protected $title = '';
	protected $subtitle = '';
	protected $componentName = 'cor_sef';

	// If no code is needed in the constructor, it can be omitted, relying on the parent class
	protected function __construct () {
		parent::__construct ();
		$this->title = T_('SEF Administration');
		$this->subtitle = T_('Configuration');
		$this->configItems = <<<SEF_CONFIG
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE configuration SYSTEM "http://www.aliro.org/xml/configuration.dtd">
<configuration>
	<params>
		<param name="enabled" type="radio" default="0" label="{$this->T_('SEF Enabled')}" description="{$this->T_('Generate and handle search engine friendly URIs')}">
			<option value="0">{$this->T_('No')}</option>
			<option value="1">{$this->T_('Yes')}</option>
		</param>
		<param name="url_rewrite" type="radio" default="0" label="{$this->T_('Use Apache URL rewriting')}" description="{$this->T_('Requires a suitable .htaccess file in the document root, sample provided as htaccess.txt')}">
			<option value="0">{$this->T_('No')}</option>
			<option value="1">{$this->T_('Yes')}</option>
		</param>
		<param name="buffer_size" type="text" default="100" label="{$this->T_('Count of URI buffer entries')}" description="{$this->T_('How many URIs do you want to be buffered in memory?')}" />
		<param name="use_cache" type="radio" default="1" label="{$this->T_('Use the cache')}" description="{$this->T_('Should the SEF handler use the Aliro cache?')}">
			<option value="0">{$this->T_('No')}</option>
			<option value="1">{$this->T_('Yes')}</option>
		</param>
		<param name="cache_time" type="text" default="600" label="{$this->T_('Cache time in seconds')}" description="{$this->T_('How long should a URI be cached before being considered stale?')}" />
		<param name="strip_chars" type="text" default="?|!|'|$|+" label="{$this->T_('Characters to be removed, separated by |')}" description="{$this->T_('Some characters are a nuisance in a URI and should be removed')}" />
		<param name="lower_case" type="radio" default="1" label="{$this->T_('Make all URIs lowercase')}" description="{$this->T_('Lower case URIs are generally preferred')}">
			<option value="0">{$this->T_('No')}</option>
			<option value="1">{$this->T_('Yes')}</option>
		</param>
		<param name="unique_id" type="radio" default="0" label="{$this->T_('Force unique ID number in URIs')}" description="{$this->T_('Text can lead to ambiguities, resolve by using a record ID where possible')}">
			<option value="0">{$this->T_('No')}</option>
			<option value="1">{$this->T_('Yes')}</option>
		</param>
		<param name="underscore" type="radio" default="0" label="{$this->T_('Redirect underscore URLs to hyphen')}" description="{$this->T_('If URIs with underscore have been in use, it may help to redirect them')}">
			<option value="0">{$this->T_('No')}</option>
			<option value="1">{$this->T_('Yes')}</option>
		</param>
		<param name="log_transform" type="radio" default="0" label="{$this->T_('Log incoming transformations')}" description="{$this->T_('Usually only needed for diagnostic purposes')}">
			<option value="0">{$this->T_('No')}</option>
			<option value="1">{$this->T_('Yes')}</option>
		</param>
		<param name="pagetitles" type="radio" default="1" label="{$this->T_('Show page titles in the browser')}" description="{$this->T_('Insert a page title into the browser title bar')}">
			<option value="0">{$this->T_('No')}</option>
			<option value="1">{$this->T_('Yes')}</option>
		</param>
		<param name="max_words" type="text" default="12" label="{$this->T_('Maximum words in a URL SEF name')}" description="{$this->T_('Automatic use of titles for URL should limit number of words to this value')}" />
		<param name="home_title" type="text" default="{$this->T_('Home')}" label="{$this->T_('Home page title')}" description="{$this->T_('What should appear in the browser title bar of the home page?')}" />
		<param name="default_robots" type="text" default="index,follow" label="{$this->T_('Robots default meta data')}" description="{$this->T_('Default instruction to crawler bots for site pages')}" />
		<param name="title_separator" type="text" default="|" label="{$this->T_('Title separator (from site name)')}" description="{$this->T_('Separator to be used in the browser title bar for different parts of the title')}" />
		<param name="google_verify" type="text" default="" label="{$this->T_('Google verification code')}" description="{$this->T_('Google verification code e.g. F2hcQ1affhHwl7VDAfuM1Zl153DPzo+F19eOWugd7yQ=')}" />
		<param name="google_analytics" type="text" default="" label="{$this->T_('Google analytics site identification code')}" description="{$this->T_('The code generated by Google Analytics for the site, e.g. UA-472673-2')}" />
	</params>
</configuration>
	
SEF_CONFIG;

	}
	
	private function saveActions () {
		aliroSEF::getInstance()->clearCache();
	}

	// The rest of the code is completely standard for a basic configurator
	
	public static function getInstance () {
		return is_object(self::$instance) ? self::$instance : (self::$instance = new self ());
	}

	public function getRequestData () {
		// Get information from $_POST or $_GET or $_REQUEST
		// This method will be called before the toolbar method
		// Note that a number of standard variables have already been retrieved by parent classes
		// They are: act, task, cid
	}

	// If this method is provided, it should return true if permission test is satisfied, false otherwise
	public function checkPermission () {
		return true;
	}

	// This provides for translation of toolbar legends
	// The keys must correspond to methods (when 'Task' is added)
	// The values will be displayed to the administrator
	public static function taskTranslator () {
		return array (
		'save' => T_('Save'),
		'cancel' => T_('SEF CP')
		);
	}

	// The code that creates the toolbar
	public function toolbar () {
		$this->toolbarDEFAULT();
	}


	// The default admin page is often a list of items, with a toolbar something like:
	protected function toolbarDEFAULT() {
		// The toolBarButton method expects an identifying string that matches an entry
		// in the taskTranslator array.  The optional second parameter (if true)
		// indicates that at least one item in the displayed list must be ticked
		$this->toolBarButton('save', false);
		$this->toolBarButton('cancel', false);
	}
	
	protected function T_($string) {
		return T_($string);
	}

	// This is the default action, and will list some items from the database, with page control
	public function listTask () {
		$configurator = aliroComponentConfiguration::getInstance($this->componentName);
		echo <<<COMPONENT_CONFIG
		
		<table class="adminheading">
		<tr>
			<th class="user">
			$this->title <small>[$this->subtitle]</small>
			</th>
		</tr>
		</table>
		{$configurator->displayEditConfiguration($this->configItems)}
		<div>
			<input type="hidden" name="core" value="cor_sef" />
			<input type="hidden" name="act" value="config" />
			<input type="hidden" id="task" name="task" value="" />
		</div>
		
COMPONENT_CONFIG;

	}
	
	// Normally, no parameter passed.  Initial aliroInstall will pass false to get return.
	public function saveTask ($redirect=true) {
		$configurator = aliroComponentConfiguration::getInstance($this->componentName);
		$configurator->saveConfigurationData($this->configItems);
		$this->saveActions();
		if ($redirect) $this->redirect('index.php?core='.$this->componentName, T_('Configuration saved'));
	}
	
	public function cancelTask () {
		$this->redirect('index.php?core='.$this->componentName);
	}
}