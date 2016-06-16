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
 * aliroTemplateBase is a base class for building templates.
 *
 */

abstract class aliroTemplateBase {
	protected $screenarea = array();
	protected $live_site = '';
	protected $sitename = '';
	protected $iso = '';
	protected $request = null;
	protected $areas = array();
	protected $isMain = false;
	protected $translations = array();

    public function __construct () {
		$this->request = aliroUserRequest::getInstance();
		$this->live_site = $this->request->getCfg('live_site');
		$this->sitename = $this->request->getCfg('sitename');
		// This needs sorting out!!!!
		// needed to seperate the ISO number from the language file constant _ISO for XML declaration
		// $iso = explode( '=', _ISO );
		// $this->iso = _ISO;
		$this->iso = 'charset=utf-8';
		foreach ($this->areas as $area) $this->screenarea[$area['position']] = new aliroUserScreenArea ($area['position'], $area['min'], $area['max'], $area['style']);
    }

	// Returns an array of all the screen areas that have been constructed by this template
	public function positions () {
		return $this->screenarea;
	}

	protected function T_ ($string) {
		return T_($string);
	}

	protected function show ($string) {
		return $string;
	}

	protected function overlib () {
		return $this->request->requestOverlib();
	}

}

abstract class aliroMainTemplateBase extends aliroTemplateBase {
	protected $colours = array();
	protected $mainmenu = '';
	protected $username = '';
	protected $versiontext = '';

	public function __construct () {
		parent::__construct();
	}

	protected function mainBody () {
		return aliroComponentHandler::getInstance()->mosMainBody();
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

}

abstract class aliroUserTemplateBase extends aliroMainTemplateBase {
	protected $favicon = '#';
	protected $colours = array();
	protected $template_uri= '';

	public function __construct () {
		parent::__construct();
		$this->isMain = true;
		$this->template_uri = $this->live_site.'/templates/'.$this->tname;
	}

	protected function __get ($property) {
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
		$this->screenarea['debug']->setData($this->request->getDebug());
		return $this->screenarea['debug']->getData();
	}

	protected function header () {
		if (!method_exists($this, $this->doctype)) $doctype = 'xhtml_10_trans';
		else $doctype = $this->doctype;
		$iso = _ISO;
		$request = aliroRequest::getInstance();
		foreach ((array) $this->cssname as $css) $request->addCSS("/templates/$this->tname/$css");

		return <<<HEADER
{$this->$doctype()}
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-type" content="text/html; $iso" />
	{$this->prepareHeader()}
</head>

HEADER;

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