<?php
/**
* Aliro
*/

class aliroAdminToolbar extends aliroFriendlyBase  {

	private static $instance = __CLASS__;

	private $template = null;

	protected function __construct() {
		$this->template = $this->getTemplateObject();
	}

	private function __clone () {
		// Enforce singleton
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	function show () {
    	$version = version::getInstance();
		$aliroVersion = $version->RELEASE.'/'.$version->DEV_STATUS.'/'.$version->DEV_LEVEL;
		if ($component = $this->getComponentObject()) {
			if ($class = $component->adminclass) {
				$controller = new $class($component, "Aliro", $aliroVersion);
				if (method_exists($controller, 'toolbar')) $controller->toolbar();
			}
			else {
				if ($path = mosMainFrame::getInstance()->getPath( 'toolbar', $this->getOption() ) AND file_exists($path)) {
					$this->invokeRetroCode($path);
				}
			}
		}
	}

	public function imageCheck ( $file, $directory, $param=NULL, $param_directory=NULL, $alt=NULL, $name=NULL, $type=1, $align='middle' ) {
		if (is_null($param_directory)) $param_directory = $this->admin_dir.'/images/';
		if ($param) $image = $this->getCfg('live_site').$param_directory.$param;
		else {
			$morepath = '/templates/'.$this->getTemplate().'/images/'.$file;
			$image = file_exists($this->admin_absolute_path.$morepath) ? $this->getCfg('admin_site').$morepath : $this->getCfg('live_site').$directory.$file;
		}
		if ($type) $image = <<<IMAGE
		<img src="$image" align="$align" alt="$alt" name="$name" border="0" />
IMAGE;

		return $image;
	}

	public function addToToolBar ($task, $alt, $name, $imagename, $extended=false, $listprompt='') {
        if (!$alt) $alt = $name;
        $image = $this->imageCheck ( $imagename.'.png', $this->admin_dir.'/images/', NULL, NULL, $alt, $task );
        $image2 = $this->imageCheck ( $imagename.'_f2.png', $this->admin_dir.'/images/', NULL, NULL, $alt, $task, 0 );
        $script = $this->template->makeJavaScript($task, $extended, $listprompt);
		$scriptText = <<<JSTAG
		
            YUI().use('*', function(Y) {
                 Y.on("click", function(e) {
                     e.preventDefault();
                     $script
                 }, "#{$task}__toolbarLink", Y);
             });
            
JSTAG;
        $this->addScriptText($scriptText, 'late', true);
        $this->template->toolBarItemHTML($task, $alt, "#", $image, $image2);
    }

    public function custom($task='', $icon='', $iconOver='', $alt='', $listSelect=true, $prefix='' ) {
     	// if ($prefix) trigger_error(T_('Aliro does not support the prefix parameter for toolbar entries'));
    	
        $icon = $this->imageCheck ($icon, '', $icon, NULL, $alt, $task);
		$this->template->toolBarItemHTML ($task, $alt, "#", $icon, $this->getCfg('admin_site').'/images/'.$iconOver);
		
		if ($listSelect) {
    		$message = sprintf(T_('Please make a selection from the list to %s'), $alt);
			$adminScript = <<<JSTAG
		var prefix     = '$prefix',
		    boxchecked = Y.one('#boxchecked') || Y.one('input[name="boxchecked"]');
		    
        if (!Y.Lang.isUndefined(boxchecked) && boxchecked.getAttribute("value") == 0) {
            e.preventDefault();
            alert('$message'); 
        } else if (prefix === '') {
            YUI.ALIRO.CORE.submitbutton('$task');
        } else {
            //This is expected to be a custom JavaScript function.  Not one provided by the core.
            {$prefix}submitbutton('$task');
        }
JSTAG;
		} else if ($prefix == '') {
            $adminScript = "YUI.ALIRO.CORE.submitbutton('$task');";
        } else {
		    //This is expected to be a custom JavaScript function.  Not one provided by the core.
            $adminScript = "{$prefix}submitbutton('$task');";
        }
		
		$scriptText = <<<JSTAG
		
            YUI().use('*', function(Y) {
                 Y.on("click", function(e) {
                     e.preventDefault(); 
                     $adminScript
                 }, "#{$task}__toolbarLink", Y);
             });
            
JSTAG;
        $this->addScriptText($scriptText, 'late', true);
    }

    public function customX( $task='', $icon='', $iconOver='', $alt='', $listSelect=true ) {
        $this->custom ($task, $icon, $iconOver, $alt, $listSelect, 'YUI.ALIRO.CORE.hideMainMenu();');
    }

    public function addNew( $task='new', $alt=null ) {
        $this->addToToolBar ($task, $alt, T_('New'), 'new');
    }

    public function addNewX( $task='new', $alt=null ) {
        $this->addToToolBar ($task, $alt, T_('New'), 'new', true);
    }

    public function publish( $task='publish', $alt=null ) {
        $this->addToToolBar ($task, $alt, T_('Publish'), 'publish');
    }

    public function publishList( $task='publish', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to publish');
        $this->addToToolBar ($task, $alt, T_('Publish'), 'publish', false, $listprompt);
    }

    public function makeDefault( $task='default', $alt=null ) {
        $listprompt = T_('Please select an item to make default');
        $this->addToToolBar ($task, $alt, T_('Default'), 'publish', false, $listprompt);
    }

    public function assign( $task='assign', $alt=null ) {
        $listprompt = T_('Please select an item to assign');
        $this->addToToolBar ($task, $alt, T_('Assign'), 'publish', false, $listprompt);
    }

    public function unpublish( $task='unpublish', $alt=null ) {
        $this->addToToolBar ($task, $alt, T_('Unpublish'), 'unpublish');
    }

    public function unpublishList( $task='unpublish', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to unpublish');
        $this->addToToolBar ($task, $alt, T_('Unpublish'), 'unpublish', false, $listprompt);
    }

    function archiveList( $task='archive', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to archive');
        $this->addToToolBar ($task, $alt, T_('Archive'), 'archive', false, $listprompt);
    }

    public function unarchiveList( $task='unarchive', $alt=null ) {
        $listprompt = T_('Please select a news story to unarchive');
        $this->addToToolBar ($task, $alt, T_('Unarchive'), 'unarchive', false, $listprompt);
    }

    public function editList( $task='edit', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        $this->addToToolBar ($task, $alt, T_('Edit'), 'edit', false, $listprompt);
    }

    public function editListX( $task='edit', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        $this->addToToolBar ($task, $alt, T_('Edit'), 'edit', true, $listprompt);
    }

    public function editHtml( $task='edit_source', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        $this->addToToolBar ($task, $alt, T_('Edit HTML'), 'html', false, $listprompt);
    }

    public function editHtmlX( $task='edit_source', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        $this->addToToolBar ($task, $alt, T_('Edit HTML'), 'html', true, $listprompt);
    }

    public function editCss( $task='edit_css', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        $this->addToToolBar ($task, $alt, T_('Edit CSS'), 'css', false, $listprompt);
    }

    public function editCssX( $task='edit_css', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        $this->addToToolBar ($task, $alt, T_('Edit CSS'), 'css', true, $listprompt);
    }

    public function deleteList( $msg='', $task='remove', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to delete');
        $this->addToToolBar ($task, $alt, T_('Delete'), 'delete', false, $listprompt);
    }

    public function deleteListX( $msg='', $task='remove', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to delete');
        $this->addToToolBar ($task, $alt, T_('Delete'), 'delete', true, $listprompt);
    }

    public function trash( $task='remove', $alt=null ) {
        $this->addToToolBar ($task, $alt, T_('Trash'), 'delete');
    }

    // Aliro does not support popup windows because of accessibility problems
    // Components requiring preview should adapt their code accordingly
    public function preview( $popup='', $updateEditors=false ) {
    }

    public function help( $ref, $com=false ) {
        $image = $this->imageCheck ( 'help.png', $this->admin_dir.'/images/', NULL, NULL, T_('Help'), 'help' );
        $image2 = $this->imageCheck ( 'help_f2.png', $this->admin_dir.'/images/', NULL, NULL, T_('Help'), 'help', 0 );
        /*
        $url = $this->getCfg('live_site').'/help/';
        $option = $this->getOption();
        if ($com) $url = $this->getCfg('admin_site').'/components/'.$option.'/help/';
        $url .= substr($option, 4).'.'.$ref . '.html';
        */
		$this->template->toolBarItemHTML ('help', T_('Help'), 'http://docs.aliro.org', $image, $image2, false);
    }

    public function apply( $task='apply', $alt=null ) {
        $this->addToToolBar ($task, $alt, T_('Apply'), 'apply');
    }

    public function save( $task='save', $alt=null ) {
        $this->addToToolBar ($task, $alt, T_('Save'), 'save');
    }

    /**
	* Writes a save button for a given option (NOTE this is being deprecated)
	*/
    public function savenew() {
        $this->addToToolBar ('savenew', null, T_('Save'), 'save');
    }

    /**
	* Writes a save button for a given option (NOTE this is being deprecated)
	*/
    public function saveedit() {
        $this->addToToolBar ('saveedit', null, T_('Save'), 'save');
    }

    public function cancel( $task='cancel', $alt=null ) {
        $this->addToToolBar ($task, $alt, T_('Cancel'), 'cancel');
    }

    /**
	* Writes a cancel button that will go back to the previous page without doing
	* any other operation
	*/

    public function back( $alt=null, $href='' ) {
        if (is_null($alt)) $alt = T_('Back');
        $image = $this->imageCheck ( 'back.png', $this->admin_dir.'/images/', NULL, NULL, 'back', 'cancel' );
        $image2 = $this->imageCheck ( 'back_f2.png', $this->admin_dir.'/images/', NULL, NULL, 'back', 'cancel', 0 );
		if ($href OR $this->getStickyAliroParam($_POST, 'alironoscript')) $link = $href;
        else $link = 'javascript:window.history.back();';
        if ($link) $this->template->toolBarItemHTML ('back', $alt, $link, $image, $image2, (strpos($href, 'javascript:') === false));
    }

    /**
	* Write a divider between menu buttons
	*/
    public function divider() {
        $image = $this->imageCheck ('menu_divider.png', $this->admin_dir.'/images/');
        $this->template->divider($image);
    }

    /**
	* Writes a media_manager button
	* @param string The sub-drectory to upload the media to
	*/
    public function mediaManager( $directory = '', $alt=null ) {
        if (is_null($alt)) $alt = T_('Upload');
        $image = mosAdminMenus::ImageCheckAdmin( 'upload.png', $this->admin_dir.'/images/', NULL, NULL, T_('Upload Image'), 'uploadPic' );
        $image2 = mosAdminMenus::ImageCheckAdmin( 'upload_f2.png', $this->admin_dir.'/images/', NULL, NULL, T_('Upload Image'), 'uploadPic', 0 );
        $href = $this->getCfg('admin_site').'/index.php?option=com_media';
        $this->template->toolBarItemHTML ('uploadPic', $alt, $href, $image, $image2, false);
    }

    /**
	* Writes the end of the menu bar table
	*/
    public function endTable() {
    }

}