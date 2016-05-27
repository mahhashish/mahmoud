<?php
/** Folder component - admin side controllers
/*  Author: Martin Brampton
/*  Date: December 2006
/*  Copyright (c) Martin Brampton 2006
/*  */

class foldersAdminFolders extends aliroComponentAdminControllers {
	private static $instance = null;
	public $act = 'folders';

	private $parentid = 0;
	public $task;
	private $ordering = array();

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}

	public function getRequestData () {
		$this->parentid = $this->getParam($_REQUEST, 'parentid', 0);
		$this->task = $this->getParam($_REQUEST, 'task', 'list');
		$this->ordering = $this->getParam($_REQUEST, 'order', array());
	}

	// create a toolbar based on the parameters found in $_REQUEST
	public function toolbar () {
		$toolbar = aliroAdminToolbar::getInstance();
		if ($this->task == 'new') {
			$toolbar->save();
			$toolbar->custom( 'list', 'cancel.png', 'cancel_f2.png', 'Cancel', false );
		}
		elseif ($this->task == 'edit') {
			$toolbar->save();
			$toolbar->custom( 'list', 'cancel.png', 'cancel_f2.png', 'Cancel', false );
		}
		else {
			$toolbar->publishList();
			$toolbar->unpublishList();
	        $toolbar->custom('saveorder', 'save.png', 'save_f2.png', T_('Save order'), false);
			$toolbar->addNew();
			$toolbar->editList();
			$toolbar->deleteList();
		}
	}

	public function listTask () {
		// Get the search string that will constrain the list of folders displayed
		$search = trim( strtolower( $this->getParam( $_POST, 'search', '' ) ) );
		// Get the flag that tells us whether to continue to nested folders right down to the bottom
		$descendants = intval($this->getParam($_POST, 'descendants', 0));
		// Create the folder above our present position - might be degenerate
		$handler = aliroFolderHandler::getInstance();
		$parent = $handler->getBasicFolder($this->parentid);
		// Get all the folders that are to be displayed
		if ($descendants) $folders = $parent->getDescendants($search);
		else {
			$folders = $parent->getChildren(false,$search);
			foreach (array_keys($folders) as $i) {
				$folders[$i]->upok = isset($folders[$i-1]);
				$folders[$i]->downok = isset($folders[$i+1]);
			}
		}
		// Generate a folder list for user to select where to be
		$clist = $parent->getSelectList('parentid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', false);
		// Create and activate a View object
		$this->makePageNav(count($folders));
		$view = new listFoldersHTML ($this, $clist);
		$items = array_slice($folders,$this->pageNav->limitstart,$this->pageNav->limit);
		$view->view($items, $descendants, $this->fulloptionurl, $search);
	}

	public function newTask () {
		// This is our new folder - nothing much in it to start with
		$folder = new aliroFolder();
		// Its parent is by default the situation we are in at present
		$folder->parentid = $this->parentid;
		// Generate a folder list so the user can change the parent
		$clist = $folder->getSelectList('parentid', 'class="inputbox"', false);
		// Create and activate a View object
		$view = new editFoldersHTML ($this, $clist);
		$view->view($folder);
	}

	public function editTask () {
		// Create a folder object that will be filled with data from the DB using currid as key
		$handler = aliroFolderHandler::getInstance();
		$folder = $handler->getFolder($this->currid);
		$parent = $folder->getParent();
		// Generate a folder list so the user can change the parent
		$clist = $parent->getSelectList('parentid', 'class="inputbox"', false, $folder->id);
		// Create and activate a View object
		$view = new editFoldersHTML ($this, $clist);
		$view->view($folder);
	}

	public function saveTask () {
		// Create a folder object that will be filled with data from the DB using currid as key
		$handler = aliroFolderHandler::getInstance();
	    $folder = $handler->getFolder($this->currid);
	    // Clear tick box fields as nothing will be received if they are unticked
	    $folder->published = 0;
	    // Add the new information from the form just submitted
	    $folder->bind($_POST);
	    // By default, a new folder is automatically published
	    if (0 == $this->currid) $folder->published = 1;
	    // Save the new information about the folder to the database
	    $folder->store();
	    $handler->clearCache(true);
		// Next we locate ourselves where this folder has finished up and list folders
		$this->parentid = $folder->parentid;
		$this->listTask();
	}

	public function saveorderTask () {
		if (is_array($this->ordering)) aliroFolderHandler::getInstance()->updateOrdering ($this->ordering, $this->parentid);
		$this->redirect( 'index.php?core='.$this->option, T_('Ordering updated') );
	}

	public function orderupTask () {
		$this->changeOrder('up');
	}

	public function orderdownTask () {
		$this->changeOrder('down');
	}

	private function changeOrder ($direction) {
		if ($this->idparm) aliroFolderHandler::getInstance()->changeOrder ($this->idparm, $direction, $this->parentid);
		$this->redirect( 'index.php?core='.$this->option, T_('Ordering updated') );
	}

	public function removeTask () {
		// In case the Javascript cannot do the check, ensure at least one item selected
		$this->check_selection(T_('Please mark item(s) for deletion'));
		// For each selected folder, create an object then delete (will delete from DB)
		$cid = $this->getParam($_POST, 'cid', array());
		$dlist = array();
		$handler = aliroFolderHandler::getInstance();
		foreach ($cid as $id) $dlist[] = $handler->getDescendantIDList(intval($id));
		$deletelist = implode (',', $dlist);
		$mambothandler = aliroMambotHandler::getInstance();
		$messages = $mambothandler->trigger('preDelete', $deletelist);
		$failures = array();
		foreach ($messages as $message) if ($message) $failures[] = $message;
		if (count($failures)) {
			// Create and activate a View object
			$view = new messageFoldersHTML ($this, '');
			$view->view($folder, $failures);
		}
		else {
			$mambothandler->trigger('doDelete', $deletelist);
			$handler->delete($deletelist);
			// Now show the list of folders again
			$this->listTask();
		}
	}

	public function publishTask () {
		$this->publishToggle(1);
	}

	public function unpublishTask () {
		$this->publishToggle(0);
	}

	private function publishToggle ($publish) {
		// Check that one or more items have been selected (Javascript may not have run)
		$this->check_selection(_DOWN_PUB_PROMPT.($publish ? 'publish' : 'unpublish'));
	    aliroFolder::togglePublished($this->cid,$publish);
	    // The file/folder counts only include published items, so recalculate
		// List out the folders again
		$this->listTask();
	}

}