<?php
/** Tags component - admin side controllers
/*  Author: Martin Brampton
/*  Date: December 2007
/*  Copyright (c) Martin Brampton 2007
/*  */

class aliroTag extends aliroDatabaseRow {
	protected $DBclass = 'aliroDatabase';
	protected $tableName = '#__tags';
	protected $rowKey = 'id';
}

class tagsAdminTags extends aliroComponentAdminControllers {
	private static $instance = __CLASS__;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	public function getRequestData () {
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
			$toolbar->addNew();
			$toolbar->editList();
			$toolbar->deleteList();
		}
	}

	public function listTask () {
		$database = aliroDatabase::getInstance();
		// Get the search string that will constrain the list of tags displayed
		$search = $this->getParam($_POST, 'search');
		if ($search) {
			$search = $database->getEscaped($search);
			$where[] = "name LIKE '%$search%'";
		}
		// Get the type of tag to constrain the list of tags displayed
		$type = $this->getParam($_POST, 'filtype');
		if ($type) {
			$type = $database->getEscaped($type);
			$where[] = "type = '$type'";
		}
		// Get all existing types from database for drop down list
		$database->setQuery("SELECT type FROM #__tags GROUP BY type ORDER BY type");
		$alltypes = $database->loadResultArray();
		if (!$alltypes) $alltypes = array();
		// Get all the tags that are to be displayed - first get count
		$query = "SELECT %s FROM #__tags";
		$condition = isset($where) ? ' WHERE '.implode(' AND ', $where) : '';
		$database->setQuery(sprintf($query, 'COUNT(*)').$condition);
		$total = $database->loadResult();
		if ($total) {
			$this->makePageNav($total);
			$condition .= " LIMIT {$this->pageNav->limitstart},{$this->pageNav->limit}";
			$tags = $database->doSQLget(sprintf($query, '*').$condition, 'aliroTag');
		}
		else $tags = array();
		// Create and activate a View object
		$view = new listTagsHTML ($this, '');
		$view->view($tags, $alltypes, $type, $search);
	}

	public function newTask () {
		// This is our new folder - nothing much in it to start with
		$tag = new aliroTag();
		// Create and activate a View object
		$view = new editTagsHTML ($this, '');
		$view->view($tag);
	}

	public function editTask () {
		// Create a tag object that will be filled with data from the DB using currid as key
		$tag = new aliroTag();
		if (!empty($this->currid)) $tag->load($this->currid);
		// Create and activate a View object
		$view = new editTagsHTML ($this, '');
		$view->view($tag);
	}

	public function saveTask () {
		// Create a tag object that will be filled with data from the DB using currid as key
		$tag = new aliroTag();
		if (!empty($this->currid)) $tag->load($this->currid);
	    // Clear tick box fields as nothing will be received if they are unticked
	    $tag->published = 0;
		$tag->hidden = 0;
	    // Add the new information from the form just submitted
	    $tag->bind($_POST);
		$tag->description = strip_tags($tag->description);
	    // Save the new information about the folder to the database
	    $tag->store();
		aliroTagHandler::getInstance()->clearCache();
		// Next we locate ourselves where this folder has finished up and list folders
		$this->listTask();
	}

	public function removeTask () {
		// In case the Javascript cannot do the check, ensure at least one item selected
		$this->check_selection(T_('Please mark item(s) for deletion'));
		// For each selected folder, create an object then delete (will delete from DB)
		$fixlist = implode(',', $this->cid);
		if ($fixlist) {
			$database = aliroDatabase::getInstance();
			$database->doSQL("DELETE FROM #__tags WHERE id IN ($fixlist)");
		}
		// List out the folders again
		$this->listTask();
		}

	public function publishTask () {
		$this->publishToggle(1);
	}

	public function unpublishTask () {
		$this->publishToggle(0);
	}

	private function publishToggle ($publish) {
		// Check that one or more items have been selected (Javascript may not have run)
		$message = $publish ? T_('Please select item(s) to publish') : T_('Please select item(s) to unpublish');
		$this->check_selection($message);
		$fixlist = implode(',', $this->cid);
		$database = aliroDatabase::getInstance();
		$database->doSQL("UPDATE #__tags SET published = $publish WHERE id IN ($fixlist)");
		// List out the folders again
		$this->listTask();
	}

}