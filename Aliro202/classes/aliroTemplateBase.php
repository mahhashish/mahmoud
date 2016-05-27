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
 * aliroTemplateBase is a base class for building templates.
 *
 */

abstract class aliroTemplateBase {
	protected $screenarea = array();
	protected $live_site = '';
	protected $sitename = '';
	protected $site_slogan = '';
	protected $iso = '';
	protected $request = null;
	protected $areas = array();
	protected $isMain = false;
	protected $translations = array();
	protected $favicon = '';

    public function __construct () {
		$this->request = aliroUserRequest::getInstance();
		$this->favicon = $this->request->getCfg('live_site').'/images/'.$this->request->getCfg('favicon');
		$this->live_site = $this->request->getCfg('live_site');
		$this->sitename = $this->request->getCfg('sitename');
		$this->site_slogan = $this->request->getCfg('site_slogan');
		// This needs sorting out!!!!
		// needed to seperate the ISO number from the language file constant _ISO for XML declaration
		// $iso = explode( '=', _ISO );
		// $this->iso = _ISO;
		$this->iso = 'charset=utf-8';
		foreach ($this->areas as $area) {
			$position = $area['position'];
			$this->screenarea[$position] = new aliroUserScreenArea ($position, $area['min'], $area['max'], $area['style']);
			foreach ($area as $key=>$value) if ('position' != $key) $this->screenarea[$position]->$key = $value;
		}
    }

	// Returns an array of all the screen areas that have been constructed by this template
	public function positions () {
		return $this->screenarea;
	}

	// The parameter is the name of a screen area, or the parent of a group of screen areas
	// The result is the number of areas that have any entries
	// This is now standardized method, and can go into the base class, aliroTemplateBase
	protected function moduleCount ($identifier) {
		$count = 0;
		foreach ($this->screenarea as $position=>$anarea) {
			if (($identifier == $position OR (isset($anarea->parent) AND $identifier = $anarea->parent)) AND $anarea->countModules()) $count++;
		}
		return $count;
	}

	protected function blockWrapper ($preblock, $postblock, $areaname) {
		if ($this->screenarea[$areaname]->countModules()) return <<<SCREEN_BLOCK
		
		$preblock
		{$this->screenarea[$areaname]->getData()}
		$postblock
		<!-- end #$areaname -->
		
SCREEN_BLOCK;

		else return '';		
	}

	protected function T_ ($string) {
		return function_exists('T_') ? T_($string) : $string;
	}

	protected function show ($string) {
		return $string;
	}

	protected function tooltip () {
		return $this->request->requestTooltip();
	}

}

abstract class aliroMainTemplateBase extends aliroTemplateBase {
	protected $colours = array();
	protected $username = '';
	protected $versiontext = '';

	public function __construct () {
		parent::__construct();
	}

	protected function mainBody () {
		return aliroComponentHandler::getInstance()->getBuffer();
	}
	
	protected function endBody () {
		return $this->request->getEndBodyTags ();
	}

	protected function getTimeMessage () {
		return aliro::getInstance()->getTimeMessage();
	}

	protected function errorMessage () {
		$messages = $this->request->pullErrorMessages();
		if (count($messages)) {
			$msghtml = '';
			foreach ($this->colours as $severity=>$colour) if (isset($messages[$severity])) {
				foreach ($messages[$severity] as $text) {
					$msghtml .= $this->oneErrorMessage ($colour, $text);
				}
			}
			$html = $this->errorSet($msghtml);
		}
		else $html = '';
		return $html;
	}

	// Define the HTML for a single error message
	protected function oneErrorMessage ($colour, $text) {
		return <<<ONE_ERROR_MESSAGE
							<div class="$colour errormessage">
								$text
							</div>
ONE_ERROR_MESSAGE;

	}

	// Define the HTML for the whole set of error messages, given the messages
	protected function errorSet ($errorsHTML) {
			return <<<FULL_MESSAGE_SET
					<!-- start Error Message area -->
					<div id="errormessage">
       	        		$errorsHTML
					<!-- end Error Message area -->
					</div>
FULL_MESSAGE_SET;

	}

	protected function xhtml_10_trans () {
		return <<<DOCTYPE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

DOCTYPE;

	}

	protected function xhtml_10_strict () {
		return <<<DOCTYPE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

DOCTYPE;

	}

	protected function xhtml_11 () {
		return <<<DOCTYPE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

DOCTYPE;

	}
	
	protected function html_401 () {
		return <<<DOCTYPE
		return <<<DOCTYPE
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

DOCTYPE;

DOCTYPE;
		
	}

}

abstract class aliroUserTemplateBase extends aliroMainTemplateBase {
	protected $favicon = '#';
	protected $colours = array();
	protected $template_uri= '';

	public function __construct () {
		parent::__construct();
		$this->isMain = true;
		$this->template_uri = $this->admin_site.'/templates'.($this->tname ? '/'.$this->tname : '');
	}

	// protected function __get
	public function __get ($property) {
		switch ($property) {
			case 'pathway':
				return aliroPathway::getInstance();
			case 'thispage':
				return $this->request->getCfg('sef') ? aliroSEF::getInstance()->sefRelToAbs(substr($_SERVER['REQUEST_URI'],1)) : '';
			case 'version':
				return new version();
			default:
				return null;
		}
	}

	protected function prepareHeader () {
		// Obtain the <head> information accumulated by the system
		$headerstuff = $this->request->showHead();
		// If there is a logged in user, then make the editor available
		if (aliroUser::getInstance()->id) $headerstuff .= aliroEditor::getInstance()->initEditor();
		return $headerstuff;
	}

	protected function debugOutput () {
		// Acquire debug information
		if ($this->screenarea['debug'] instanceof aliroScreenArea ) {
			$this->screenarea['debug']->setData($this->request->getDebug());
			return $this->screenarea['debug']->getData();
		}
	}

	protected function header () {
		if (!method_exists($this, $this->doctype)) $doctype = 'xhtml_10_trans';
		else $doctype = $this->doctype;
		$iso = _ISO;
		$tbasepath = '/templates'.($this->tname ? '/'.$this->tname : '').'/';
		foreach ((array) $this->cssname as $css) {
			if (preg_match('#^https?://#', $css)) $this->request->addCSS($css, 'screen', true);
			else $this->request->addCSS($tbasepath.$css);
		}

		return <<<HEADER
{$this->$doctype()}
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; $iso" />
{$this->prepareHeader()}
{$this->extraHeaderText()}
<link rel="shortcut icon" href="$this->favicon" />
</head>

HEADER;

	}
	
	protected function extraHeaderText () {
		if (method_exists($this, 'templateHeaderText')) return $this->templateHeaderText();
	}

	// Only the most basic HTML combined with the output of the invoked component
	// Can be overriden by a template if required
	public function component_render () {
		echo <<<COMPONENT_HTML
{$this->header ()}

	<body>
		{$this->mainBody()}
	</body>
</html>

COMPONENT_HTML;

	}
}