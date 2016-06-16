<?php

abstract class aliroAdminTemplateBase extends aliroMainTemplateBase {
	protected $admin_site = '';
	protected $toolbar = '';
	protected $retrotoolbar = false;

	public function __construct () {
		parent::__construct();
		$this->tpath = criticalInfo::getInstance()->admin_dir.'/templates/';
		$this->admin_site = $this->request->getCfg('admin_site');
		// Help xgettext by showing string for translation outside heredoc
		T_('Administration [Aliro]');
	}

	protected function preRender () {
		$this->showToolbar();
		if ($this->request->getParam($_REQUEST, 'hidemainmenu', 0)) $this->mainmenu = '';
		else $this->mainmenu = aliroAdminMenuHandler::getInstance()->makeMenu();
		$this->username = aliroUser::getInstance()->username;
		$this->versiontext = version::getInstance()->footer();
	}

	protected function header ($login=false) {
		if (!method_exists($this, $this->doctype)) $doctype = 'xhtml_10_trans';
		else $doctype = $this->doctype;
		if ($login) {
			$initeditor = '';
			$css = $this->logincss;
			$adminscript = <<<ADMIN_SCRIPT
		<script type="text/javascript">
			function setFocus() {
				document.loginForm.usrname.select();
				document.loginForm.usrname.focus();
			}
		</script>
ADMIN_SCRIPT;
		
		}
		else {
			$initeditor = aliroEditor::getInstance()->initEditor();
			$css = $this->cssname;
			$adminscript = <<<ALIRO_JS
		<script src='$this->live_site/includes/js/alirojavascript.js' type='text/javascript'></script>
ALIRO_JS;

		}
		$iso = _ISO;
		return <<<HTML_HEADER
{$this->$doctype()}
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; $iso" />
		<title>$this->sitename - {$this->T_('Administration [Aliro]')}</title>
		$initeditor
		<link rel='stylesheet' href='$this->admin_site/templates/$this->tname/$css' type='text/css' />
		{$this->request->getCustomTags()}
		$adminscript
		</head>

HTML_HEADER;

	}

	protected function showToolbar () {
    	$version = version::getInstance();
		$aliroVersion = $version->RELEASE.'/'.$version->DEV_STATUS.'/'.$version->DEV_LEVEL;
		if ($component = $this->request->getComponentObject()) {
			if ($class = $component->adminclass) {
				$controller = new $class($component, "Aliro", $aliroVersion);
				$controller->toolbar();
			}
			else {
				if ($path = mosMainFrame::getInstance()->getPath( 'toolbar', $this->request->getOption() ) AND file_exists($path)) {
					$this->request->invokeRetroCode($path);
				}
			}
		}
	}
	
	// Preferred new toolbar creator - simple form submit buttons
	public function toolBarButton ($name, $requireSelect=false) {
    	if ($requireSelect) {
    		$message = sprintf(T_('Please make a selection from the list to %s'), $name);
    		$script = "if (document.adminForm.boxchecked.value == 0){ alert('$message'); return false; } else";
		}
    	else $script = '';
        $script .= "{return true}";
		$html = <<<BUTTON_HTML
		
		<input type="submit" class='toolitem' value="$name" name="toolbarbutton" onclick="$script" />
		
BUTTON_HTML;

		$this->toolbar = $html.$this->toolbar;
	}

	// Can be redefined in an actual admin side template if desired
	public function toolBarItemHTML ($task, $alt, $href, $icon, $iconOver, $linkIfJavaScript=true) {
		$this->retrotoolbar = true;
    	if ($linkIfJavaScript AND $this->request->getStickyAliroParam($_POST, 'alironoscript')) $startlink = $endlink = '';
    	else {
    		$startlink = <<<LINK_START
        	<a class="toolbar" href="$href" onmouseout='MM_swapImgRestore();' onmouseover="MM_swapImage('$task','','$iconOver',1);">
LINK_START;
			$endlink = '</a>';
		}
        if ($icon AND $iconOver) {
        	$html = <<<TOOL_ITEM
        	<div class="toolitem">
        	$startlink
        	$icon
        	<br />
        	$alt
        	$endlink
			<noscript><input type="radio" name="alironstask" value="$task" /></noscript>
        	</div>
TOOL_ITEM;
        }
        else $html = <<<SHORT_ITEM
        <div class="toolitem">
        	<a class='toolbar' href='$href'><br />$atl</a>
        </div>
SHORT_ITEM;

		$this->toolbar = $html.$this->toolbar;

	}

	// Can be redefined in an actual admin side template if desired
    public function makeJavaScript ($task, $extended, $listprompt='') {
        $script = '';
        if ($listprompt) $script .= "if (document.adminForm.boxchecked.value == 0){ alert('$listprompt'); } else";
        $script .= '{';
        if ($extended) $script .= 'hideMainMenu();';
        $script .= "submitbutton('$task')}";
        return $script;
    }

	// Can be redefined in an actual admin side template if desired
    public function divider($image) {
		$divider = <<<DIVIDER
		<div class="toolitem">
			$image
		</div>
DIVIDER;

		$this->toolbar = $divider.$this->toolbar;
    }

	// Can be redefined in an actual admin side template if desired
    protected function getToolbar () {
    	if ($this->retrotoolbar) {
    		$buttonlegend = T_('Perform action');
    		$noscript = <<<NO_SCRIPT
    		
				<noscript>
					<div class="toolitem">
						<input type="submit" class="button" value="$buttonlegend" />
					</div>
				</noscript>
    		
NO_SCRIPT;

		}
		else $noscript = '';
    	if ($this->toolbar) return <<<TOOLBAR_HTML

    		<div id='AliroAdminToolBar' align='right'>
    			$noscript
				$this->toolbar
			</div>

TOOLBAR_HTML;

    	else return '';
	}

}