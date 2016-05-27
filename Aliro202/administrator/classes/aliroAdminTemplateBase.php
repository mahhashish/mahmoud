<?php

abstract class aliroAdminTemplateBase extends aliroMainTemplateBase {
	protected $admin_site = '';
	protected $toolbar = '';
	protected $heading = '';
	protected $entries = array();
	protected $template_uri= '';
	protected $retrotoolbar = false;

	public function __construct () {
		parent::__construct();
		$this->tpath = _ALIRO_ADMIN_DIR.'/templates/';
		$this->admin_site = $this->request->getCfg('admin_site');
		// Help xgettext by showing string for translation outside heredoc
		T_('Administration [Aliro]');
		$this->template_uri = $this->admin_site.'/templates'.($this->tname ? '/'.$this->tname : '');
	}

	protected function preRender () {
		$this->showToolbar();
		$this->showMainHeading();
		if (!$this->request->getParam($_REQUEST, 'hidemainmenu', 0)) $this->entries = aliroAdminMenuHandler::getInstance()->makeMenu();
		$this->username = aliroUser::getInstance()->username;
		$this->versiontext = version::getInstance()->footer();
	}
	
	protected function createMenu () {
		$indents = array("\n\t", "\n\t\t", "\n\t\t\t", "\n\t\t\t\t", "\n\t\t\t\t\t");
		$html = "\n<ul id=\"nav\">";
		$clevel = 0;
		foreach ($this->entries as $entry) {
			while ($entry->level < $clevel) {
				// Dropped at least one level, so we need to close some elements
				$html .= $indents[$clevel].'</ul></li>';
				$clevel--;
			}
			$clevel = $entry->level;
			// Add one element, indented according to level
			$html .= $indents[$clevel].$this->createListElement($entry->name, $entry->link, $entry->active, $entry->node);
		}
		if ($html) while (0 < $clevel) {
			// Close elements until we get back to top level
			$html .= $indents[$clevel-1].'</ul></li>';
			$clevel--;
		}
		// Close the outermost list
		$html .= "\n</ul>";
		return $html;
	}
	
	protected function createListElement ($name, $link, $active, $node) {
		// Set classes for active or node or nothing; if a node there is a sublevel, so add <ul>
		// If not a node, close the element with </li>
		if ($active) $classes[] = 'active';
		if ($node) {
			$classes[] = 'node';
			$suffix = '<ul>';
		}
		else $suffix = '</li>';
		// Form up the class attribute for the list element
		$classlist = isset($classes) ? implode(' ', $classes) : '';
		$classtext = $classlist ? " class=\"$classlist\"" : '';
		return "<li$classtext>".$this->createMenuLink($name,$link).$suffix;
	}
	
	protected function createMenuLink ($name, $link) {
		return $link ? "<a href=\"$link\">$name</a>" : "<a>$name</a>";
	}

	protected function header ($login=false) {
		if (!method_exists($this, $this->doctype)) $doctype = 'xhtml_10_trans';
		else $doctype = $this->doctype;
		if ($login) {
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
		    //Does this initEditor call need to be here?  Doesn't seem like it and it causes a double load when present.
			//aliroEditor::getInstance()->initEditor();
			
			$css = $this->cssname;
			$adminscript = <<<ADMIN_SCRIPT
		    <script type="text/javascript">
    			var aliroDebugMode = '{$this->request->getCfg('debug')}'; //Used by YUI Console
    		</script>
ADMIN_SCRIPT;
		}
		$iso = _ISO;

        //Create a custom module metadata set
        $aliroCoreReqs = array("yui", "base", "event", "event-custom");
        if ($this->request->getCfg('debug')) {
            $debugReqs = array("console", "dd-plugin");
            $aliroCoreReqs = array_merge($aliroCoreReqs, $debugReqs);
        }
        $customModules = array(
            "adminTemplate" => array(
                "name" => 'adminTemplate',
                "type" => 'css',
                "fullpath" => _ALIRO_ADMIN_DIR.'/templates/'.($this->tname ? $this->tname. '/' : '').$css,
                "requires" => array("cssreset", "cssfonts")
            ),
            "aliroCore" => array(
                "name" => 'aliroCore',
                "type" => 'js',
                "fullpath" => '/core_includes/js/aliro_core.js',
                "requires" => $aliroCoreReqs
            ),
            "aliroBackend" => array(
                "name" => 'aliroBackend',
                "type" => 'js',
                "fullpath" => '/core_includes/js/aliro_backend.js',
                "requires" => array("aliroCore")
            ),
            "aliroCoreUI" => array(
                "name" => 'aliroCoreUI',
                "type" => 'js',
                "fullpath" => '/core_includes/js/aliro_core_ui.js',
                "requires" => array("aliroCore")
            ),
            "aliroAdminMgr" => array(
                "name" => 'aliroAdminMgr',
                "type" => 'js',
                "fullpath" => '/core_includes/js/aliro_admin_manager.js',
                "requires" => array("aliroCore", "aliroBackend")
            )
            
            /* aliroBackwardsCompatibility is only required to support older plugins.  Includes 
                remapping functions. It should not be on by default, but perhaps we should offer 
                a way to enable from the global config? */
            
            /*
            "aliroBackwardsCompatibility" => array(
                "name" => 'aliroBackwardsCompatibility',
                "type" => 'js',
                "fullpath" => '/includes/js/alirojavascript.js',
                "requires" => array("aliroCore")
            )
            */
        );

		//Bring in the Aliro Resource Loader
		$aliroLoader = aliroResourceLoader::getInstance();
		$aliroLoader->addCustomModules($customModules);
		$aliroLoader->loadOptimized();
		
		return <<<HTML_HEADER
{$this->$doctype()}
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; $iso" />
		<title>$this->sitename - {$this->T_('Administration [Aliro]')}</title>
		{$this->request->getCustomTags()}
		$adminscript
	</head>

HTML_HEADER;

	}
	
	protected function showMainHeading () {
    	$version = version::getInstance();
		$aliroVersion = $version->RELEASE.'/'.$version->DEV_STATUS.'/'.$version->DEV_LEVEL;
		$component = $this->request->getComponentObject();
		if ($component) {
			$class = $component->adminclass;
			if ($class) {
				$controller = new $class($component, "Aliro", $aliroVersion);
				if (method_exists($controller, 'mainHeading')) return $controller->mainHeading();
			}
		}
	}

	protected function showToolbar () {
    	$version = version::getInstance();
		$aliroVersion = $version->RELEASE.'/'.$version->DEV_STATUS.'/'.$version->DEV_LEVEL;
		$component = $this->request->getComponentObject();
		if ($component) {
			$class = $component->adminclass;
			if ($class) {
				$controller = new $class($component, "Aliro", $aliroVersion);
				if (method_exists($controller, 'toolbar')) $controller->toolbar();
			}
			else {
				if ($path = mosMainFrame::getInstance()->getPath( 'toolbar', $this->request->getOption() ) AND file_exists($path)) {
					$this->request->invokeRetroCode($path);
				}
			}
		}
	}
	
	public function headingDisplay ($name, $class) {
		$this->heading = <<<USER_HEADING
	
	<div class="topadminheading $class">
		<h2 class="usermanager">$name</h2>
	</div>
	
USER_HEADING;

	}
	
	// Preferred new toolbar creator - simple form submit buttons
	public function toolBarButton ($name, $requireSelect=false) {
	    $id = strtolower(preg_replace("/[^a-zA-Z0-9]/", "_", $name));
    	if ($requireSelect) {
    		$message = sprintf(T_('Please make a selection from the list to %s'), $name);
    		$script = <<<BOX_CHECKED
                
                YUI().use('*', function(Y) {
                     Y.on("click", function(e) {
                         var boxchecked = Y.one('#boxchecked') || Y.one('input[name="boxchecked"]'); 
                         if (!Y.Lang.isUndefined(boxchecked) && boxchecked.getAttribute("value") == 0) {
                             e.preventDefault();
                             alert('$message');
                         }
                     }, "#{$id}__toolbarLink", Y);
                 });
                
BOX_CHECKED;
    		$this->request->addScriptText($script, 'late', true);
		}
		$html = <<<BUTTON_HTML
		
		<input type="submit" class='toolitem' value="$name" id="{$id}__toolbarLink" name="toolbarbutton" />
		
BUTTON_HTML;

		$this->toolbar = $html.$this->toolbar;
	}

	// Can be redefined in an actual admin side template if desired
	public function toolBarItemHTML ($task, $alt, $href, $icon, $iconOver, $linkIfJavaScript=true) {
		$this->retrotoolbar = true;
		$iconfilename = basename(preg_replace('#".*$#', '', preg_replace('#.?http://[^/]*#', '', $icon)));
		$iconclass = substr($iconfilename,0,strrpos($iconfilename,'.'));
		if (!$iconclass) $iconclass = 'generic';
    	if ($linkIfJavaScript AND $this->request->getStickyAliroParam($_POST, 'alironoscript')) $startlink = $endlink = '';
    	else {
    		$startlink = <<<LINK_START
        	<a class="toolbar" href="$href">
LINK_START;
			$endlink = '</a>';
		}
       	$html = <<<TOOL_ITEM
        	
        	<div class="toolitem">
			$startlink
			<object id="{$task}__toolbarLink">
	        	<div class="toolitemicon tool_$iconclass"></div>
				<div class="toolitemtext">$alt</div>
			</object>
			$endlink
        	</div>
		<noscript><input type="radio" name="alironstask" value="$task" /></noscript>
TOOL_ITEM;
 
		$this->toolbar = $html.$this->toolbar;
	}

	public function publishedProcessing ($row, $i) {
		$img 	= $row->published ? 'publish_g.png' : 'publish_x.png';
		$task 	= $row->published ? 'unpublish' : 'publish';
		$alt 	= $row->published ? T_('Published') : T_('Unpublished');
		$action	= $row->published ? T_('Unpublish Item') : T_('Publish item');
		return <<<PUBLISH_LINK
		<a id="{$task}__cb{$i}" class="list-item-task" href="#" title="$action">
		    <img src="$this->template_uri/images/$img" border="0" alt="$alt" />
		</a>
PUBLISH_LINK;

	}
	
	public function checkedOut ($hover) {
		return <<<CHECKED_OUT
		<img src="$this->template_uri/images/checked_out.png"/ $hover alt="Checked Out"/>
		
CHECKED_OUT;

	}
	
	public function orderUpIcon( $i, $limitcondition, $condition=true, $task='orderup', $alt='Move Up' ) {
		if (($i > 0 OR $limitcondition) AND $condition) {
		    return <<<ORDERUP
		    <a id="{$task}_cb{$i}" class="list-item-task" href="#reorder" title="$alt">
				<img src="$this->template_uri/images/uparrow.png" width="12" height="12" border="0" alt="$alt" />
			</a>
			
ORDERUP;

  		} else return '&nbsp;';
	}

	public function noJavaOrderUpIcon( $i, $limitcondition, $condition, $url ) {
		if (($i > 0 OR $limitcondition) AND $condition) {
			$alt = T_('Move up');
		    return <<<ORDERUP
		    <a href="$url" title="$alt">
				<img src="$this->template_uri/images/uparrow.png" width="12" height="12" border="0" alt="$alt" />
			</a>
ORDERUP;
  		} else {
  		    return '&nbsp;';
		}
	}

	public function orderDownIcon( $i, $limitcondition, $n, $condition=true, $task='orderdown', $alt='Move Down' ) {
		if (($i < $n-1 OR $limitcondition) AND $condition) {
			return <<<ORDERDOWN
			<a id="{$task}_cb{$i}" class="list-item-task" href="#reorder" title="$alt">
				<img src="$this->template_uri/images/downarrow.png" width="12" height="12" border="0" alt="$alt" />
			</a>
			
ORDERDOWN;

  		} else return '&nbsp;';
	}

	public function noJavaOrderDownIcon( $i, $limitcondition, $n, $condition, $url ) {
		if (($i < $n-1 OR $limitcondition) AND $condition) {
			$alt = T_('Move down');
			return <<<ORDERDOWN
			<a href="$url" title="$alt">
				<img src="$this->template_uri/images/downarrow.png" width="12" height="12" border="0" alt="$alt" />
			</a>
ORDERDOWN;
  		} else {
  		    return '&nbsp;';
		}
	}
	
	// Can be redefined in an actual admin side template if desired
    public function makeJavaScript ($task, $extended, $listprompt='') {
        $script = '';
        if ($listprompt) {
            $script .= "if (document.adminForm.boxchecked.value == 0){ alert('$listprompt'); } else";
        }
        $script .= '{';
        if ($extended) {
            $script .= 'YUI.ALIRO.CORE.hideMainMenu();';
        }
        $script .= "YUI.ALIRO.CORE.submitbutton('$task'); }";
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
    
    protected function getHeading () {
    	return <<<HEADING_HTML
    	
    		<div id='AliroAdminMainHeading'>
				$this->heading
			</div>
    	
HEADING_HTML;
    	
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
