<?php
/**
* @package Mambo Open Source
* @copyright (C) 2005 - 2006 Mambo Foundation Inc.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* Mambo was originally developed by Miro (www.miro.com.au) in 2000. Miro assigned the copyright in Mambo to The Mambo Foundation in 2005 to ensure
* that Mambo remained free Open Source software owned and managed by the community.
* Mambo is Free Software
*/

/**
* Utility class for the button bar
*/
class mosMenuBar {

    /**
	* Writes the start of the button bar table
	*/
    public static function startTable() {
    }

    /**
	* Writes a custom option and task button for the button bar
	* @param string The task to perform (picked up by the switch($task) blocks
	* @param string The image to display
	* @param string The image to display when moused over
	* @param string The alt text for the icon image
	* @param boolean True if required to check that a standard list item is checked
	*/
    public static function custom( $task='', $icon='', $iconOver='', $alt='', $listSelect=true, $prefix='' ) {
    	aliroAdminToolbar::getInstance()->custom ($task, $icon, $iconOver, $alt, $listSelect, $prefix);
    }

    /**
	* Writes a custom option and task button for the button bar.
	* Extended version of custom() calling hideMainMenu() before submitbutton().
	* @param string The task to perform (picked up by the switch($task) blocks
	* @param string The image to display
	* @param string The image to display when moused over
	* @param string The alt text for the icon image
	* @param boolean True if required to check that a standard list item is checked
	*/
    public static function customX( $task='', $icon='', $iconOver='', $alt='', $listSelect=true ) {
        mosMenuBar::custom ($task, $icon, $iconOver, $alt, $listSelect, 'hideMainMenu();');
    }

    /**
	* Standard routine for displaying toolbar icon
	* @param string An override for the task
	* @param string An override for the alt text
	* @param string The name to be used as a legend and as the image name
	* @param
	*/
    public static function addToToolBar ($task, $alt, $name, $imagename, $extended=false, $listprompt='') {
    	aliroAdminToolbar::getInstance()->addToToolBar ($task, $alt, $name, $imagename, $extended, $listprompt);
    }

    /**
	* Writes the common 'new' icon for the button bar
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function addNew( $task='new', $alt=null ) {
        mosMenuBar::addToToolBar ($task, $alt, 'New', 'new');
    }

    /**
	* Writes the common 'new' icon for the button bar.
	* Extended version of addNew() calling hideMainMenu() before submitbutton().
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function addNewX( $task='new', $alt=null ) {
        mosMenuBar::addToToolBar ($task, $alt, 'New', 'new', true);
    }

    /**
	* Writes a common 'publish' button
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function publish( $task='publish', $alt=null ) {
        mosMenuBar::addToToolBar ($task, $alt, 'Publish', 'publish');
    }

    /**
	* Writes a common 'publish' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function publishList( $task='publish', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to publish');
        mosMenuBar::addToToolBar ($task, $alt, 'Publish', 'publish', false, $listprompt);
    }

    /**
	* Writes a common 'default' button for a record
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function makeDefault( $task='default', $alt=null ) {
        $listprompt = T_('Please select an item to make default');
        mosMenuBar::addToToolBar ($task, $alt, 'Default', 'publish', false, $listprompt);
    }

    /**
	* Writes a common 'assign' button for a record
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function assign( $task='assign', $alt=null ) {
        $listprompt = T_('Please select an item to assign');
        mosMenuBar::addToToolBar ($task, $alt, 'Assign', 'publish', false, $listprompt);
    }

    /**
	* Writes a common 'unpublish' button
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function unpublish( $task='unpublish', $alt=null ) {
        mosMenuBar::addToToolBar ($task, $alt, 'Unpublish', 'unpublish');
    }

    /**
	* Writes a common 'unpublish' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function unpublishList( $task='unpublish', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to unpublish');
        mosMenuBar::addToToolBar ($task, $alt, 'Unpublish', 'unpublish', false, $listprompt);
    }

    /**
	* Writes a common 'archive' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function archiveList( $task='archive', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to archive');
        mosMenuBar::addToToolBar ($task, $alt, 'Archive', 'archive', false, $listprompt);
    }

    /**
	* Writes an unarchive button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function unarchiveList( $task='unarchive', $alt=null ) {
        $listprompt = T_('Please select a news story to unarchive');
        mosMenuBar::addToToolBar ($task, $alt, 'Unarchive', 'unarchive', false, $listprompt);
    }

    /**
	* Writes a common 'edit' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function editList( $task='edit', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        mosMenuBar::addToToolBar ($task, $alt, 'Edit', 'edit', false, $listprompt);
    }

    /**
	* Writes a common 'edit' button for a list of records.
	* Extended version of editList() calling hideMainMenu() before submitbutton().
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function editListX( $task='edit', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        mosMenuBar::addToToolBar ($task, $alt, 'Edit', 'edit', true, $listprompt);
    }

    /**
	* Writes a common 'edit' button for a template html
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function editHtml( $task='edit_source', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        mosMenuBar::addToToolBar ($task, $alt, 'Edit HTML', 'html', false, $listprompt);
    }

    /**
	* Writes a common 'edit' button for a template html.
	* Extended version of editHtml() calling hideMainMenu() before submitbutton().
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function editHtmlX( $task='edit_source', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        mosMenuBar::addToToolBar ($task, $alt, 'Edit HTML', 'html', true, $listprompt);
    }

    /**
	* Writes a common 'edit' button for a template css
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function editCss( $task='edit_css', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        mosMenuBar::addToToolBar ($task, $alt, 'Edit CSS', 'css', false, $listprompt);
    }

    /**
	* Writes a common 'edit' button for a template css.
	* Extended version of editCss() calling hideMainMenu() before submitbutton().
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function editCssX( $task='edit_css', $alt=null ) {
        $listprompt = T_('Please select an item from the list to edit');
        mosMenuBar::addToToolBar ($task, $alt, 'Edit CSS', 'css', true, $listprompt);
    }

    /**
	* Writes a common 'delete' button for a list of records
	* @param string  Postscript for the 'are you sure' message
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function deleteList( $msg='', $task='remove', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to delete');
        mosMenuBar::addToToolBar ($task, $alt, 'Delete', 'delete', false, $listprompt);
    }

    /**
	* Writes a common 'delete' button for a list of records.
	* Extended version of deleteList() calling hideMainMenu() before submitbutton().
	* @param string  Postscript for the 'are you sure' message
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function deleteListX( $msg='', $task='remove', $alt=null ) {
        $listprompt = T_('Please make a selection from the list to delete');
        mosMenuBar::addToToolBar ($task, $alt, T_('Delete'), 'delete', true, $listprompt);
    }

    /**
	* Write a trash button that will move items to Trash Manager
	*/
    public static function trash( $task='remove', $alt=null ) {
        mosMenuBar::addToToolBar ($task, $alt, 'Trash', 'delete');
    }

    // Popup windows not supported in Aliro - where preview desired, need to write new code
    public static function preview( $popup='', $updateEditors=false ) {
    }

    /**
	* Writes a preview button for a given option (opens a popup window)
	* @param string The name of the popup file (excluding the file extension for an xml file)
	* @param boolean Use the help file in the component directory
	*/
    public static function help ($ref, $com=false) {
    	aliroAdminToolbar::getInstance()->help ($ref, $com);
    }

    /**
	* Writes a save button for a given option
	* Apply operation leads to a save action only (does not leave edit mode)
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function apply( $task='apply', $alt=null ) {
        mosMenuBar::addToToolBar ($task, $alt, T_('Apply'), 'apply');
    }

    /**
	* Writes a save button for a given option
	* Save operation leads to a save and then close action
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function save( $task='save', $alt=null ) {
        mosMenuBar::addToToolBar ($task, $alt, T_('Save'), 'save');
    }

    /**
	* Writes a save button for a given option (NOTE this is being deprecated)
	*/
    public static function savenew() {
    	mosMenuBar::addToToolBar ('savenew', null, T_('Save'), 'save');
    }

    /**
	* Writes a save button for a given option (NOTE this is being deprecated)
	*/
    public static function saveedit() {
    	mosMenuBar::addToToolBar ('saveedit', null, T_('Save'), 'save');
    }

    /**
	* Writes a cancel button and invokes a cancel operation (eg a checkin)
	* @param string An override for the task
	* @param string An override for the alt text
	*/
    public static function cancel( $task='cancel', $alt=null ) {
        mosMenuBar::addToToolBar ($task, $alt, T_('Cancel'), 'cancel');
    }

    /**
	* Writes a cancel button that will go back to the previous page without doing
	* any other operation
	*/
    public static function back( $alt=null, $href='' ) {
		aliroAdminToolbar::getInstance()->back ($alt, $href);
    }

    /**
	* Write a divider between menu buttons
	*/
    public static function divider() {
    	aliroAdminToolbar::getInstance()->divider ();
    }

    /**
	* Writes a media_manager button
	* @param string The sub-drectory to upload the media to
	*/
    public static function media_manager( $directory = '', $alt=null ) {
    	aliroAdminToolbar::getInstance()->mediaManager($directory, $alt);
    }

    /**
	* Writes a spacer cell
	* @param string The width for the cell
	*/
    public static function spacer( $width='' ) {
    }

    /**
	* Writes the end of the menu bar table
	*/
    public static function endTable() {
    }
}