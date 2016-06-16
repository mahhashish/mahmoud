<?php
/**
* Aliro default Admin template
*/

class defaultAdminTemplate extends aliroAdminTemplateBase {
	// Options are 'xhtml_10_trans', 'xhtml_10_strict' or 'xhtml_11'
	protected $doctype = 'xhtml_10_trans';
	// Formal name of this template
	protected $tname = '.';
	// File name of CSS file, relative to the template
	protected $cssname = 'adminDefault.css';
	// File name of login CSS file, relative to the template
	protected $logincss = 'adminDefaultLogin.css';

	public function __construct () {
		// These are the screen areas supported by this template
		$this->areas = array (
		array ('position' => 'cpanel', 'min' => 500, 'max' => 800, 'style' => 'Div')
		);
		// All the Aliro error levels must have matching colour classes, with definitions in the CSS
		$this->colours = array (
		_ALIRO_ERROR_FATAL => 'fatalcolour',
		_ALIRO_ERROR_SEVERE => 'severecolour',
		_ALIRO_ERROR_WARN => 'warncolour',
		_ALIRO_ERROR_INFORM => 'informcolour'
		);
		parent::__construct();
		// Help xgettext by showing translation outside heredoc
		T_('Logout');
	}

	public function moduleStyleDiv ($moduleclass_sfx, $title, $content) {
		$html = <<<MODULE_HTML

		<div class="module$moduleclass_sfx">
			<h4>$title</h4>
			$content
		</div>

MODULE_HTML;

		return $html;
	}

	// Define the HTML for a single error message
	// In base class - override here to generate different HTML
	// protected function oneErrorMessage ($colour, $text) {}

	// Define the HTML for the whole set of error messages, given the messages
	// In base class - override here to generate different HTML
	// protected function errorSet ($errorsHTML) {}

	// Construct the HTML for a single toolbar entry
    public function toolBarItemHTML ($task, $alt, $href, $icon, $iconOver, $linkIfJavaScript=true) {
        if ($linkIfJavaScript AND $this->request->getStickyAliroParam($_POST, 'alironoscript')) $startlink = $endlink = '';
        else {
            $startlink = <<<LINK_START
            <a class="toolbar" href="$href">
LINK_START;
            $endlink = '</a>';
        }
        $html = <<<SHORT_ITEM
        
        <div class="toolitem">
            $startlink
            $alt
            $endlink
            <noscript><input type="radio" name="alironstask" value="$task" /></noscript>
        </div>
        
SHORT_ITEM;

        $this->toolbar = $html.$this->toolbar;

    }
	
	// Render a standard admin side page
	public function render () {
		$this->preRender();
		echo <<<ADMIN_HTML
{$this->header()}
	<body>
		<div id="AliroAdmin">
			<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">
			<div id="AliroAdminNavigation">
				<div class="aliroadminlogo">
					Aliro
				</div>
				$this->mainmenu
			</div>
			<div id="AliroMainPart">
				<div id="AliroAdminLogout">
					<a href='index.php?option=logout'><strong>{$this->T_('Logout')}</strong></a> $this->username
				</div>
				<div id="AliroAdminToolbar">
					{$this->getToolbar()}
				</div>
				<div id="AliroAdminMainbox">
					<div id="aliroerrors">
						{$this->errorMessage()}
					</div>
					<div id="AliroAdminMain">
						{$this->mainBody()}
					</div>
					<div id='AliroAdminFooter'>
						$this->versiontext
					</div>
					<div id='AliroAdminPageTimer'>
						{$this->getTimeMessage()}
					</div>
					<div id='AliroAdminDebug'>
						{$this->request->getDebug()}
					</div>
				</div>
			</div>
			</form>
		</div>
	</body>
</html>

ADMIN_HTML;

	}

	// Render the admin side login
	function login () {

		echo <<<HTML_LOGIN
{$this->header(true)}

<body>
  <div id="wrapper">
      	<form action="index.php" method="post" name="loginForm" id="loginForm">
			<div id='AliroLoginBody'>
				<div id='AliroLoginHeading'>
					<h1>$this->sitename</h1>
				</div>
				<div id='AliroUserName'>
					<label for='AliroAdminUser'>User:</label>
					<input id='AliroAdminUser' name='usrname' type='text' size='15' />
				</div>
				<div id='AliroPassword'>
					<label for='AliroPass'>Password:</label>
					<input id='AliroPass' name='pass' type='password' size='15' />
				</div>
				<div id="wrapper2">
					<input type="hidden" name="option" value="login" />
					<div id='AliroLoginButton'>
						<input type="submit" name="submit" class="button" value="Login" />
					</div>
				</div>
			</div>
		</form>
	</div>
</body>
</html>

HTML_LOGIN;

	}

}

?>