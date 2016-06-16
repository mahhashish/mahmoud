<?php

/**
* Common HTML Output Files
* @package Mambo
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$mainframe = mosMainFrame::getInstance();
require_once ($mainframe->getCfg('absolute_path').'/includes/mambofunc.php');

class mosAdminMenus {
	/**
	* build the select list for Menu Ordering
	*/
	public static function Ordering( &$row, $id ) {
		if ( $id ) {
			$order = mosAdminMenus::mosGetOrderingList( $row, "SELECT ordering AS value, name AS text"
			. "\n FROM #__menu"
			. "\n WHERE menutype='". $row->menutype ."'"
			. "\n AND parent='". $row->parent ."'"
			. "\n AND published != '-2'"
			. "\n ORDER BY ordering"
			);
			$ordering = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
		} else {
			$ordering = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. T_('New items default to the last place. Ordering can be changed after this item is saved.');
		}
		return $ordering;
	}

	/**
	* build the select list for access level
	*/
	public static function Access( &$row ) {
		$database = aliroDatabase::getInstance();
		$query = 'SELECT id AS value, name AS text FROM #__groups ORDER BY id';
		$database->setQuery( $query );
		$groups = $database->loadObjectList();
		$access = mosHTML::selectList( $groups, 'access', 'class="inputbox" size="3"', 'value', 'text', intval( $row->access ) );
		return $access;
	}

	/**
	* build a radio button option for published state
	*/
	public static function Published( &$row ) {
		$published = mosHTML::yesnoRadioList( 'published', 'class="inputbox"', $row->published );
		return $published;
	}

	/**
	* build the link/url of a menu item
	*/
	public static function Link( &$row, $id, $link=NULL ) {
		die('mosAdminMenus::Link() has been withdrawn');
	}

	/**
	* build the select list for target window
	*/
	public static function Target( &$row ) {
		$click[] = mosHTML::makeOption( '0',  T_('Parent Window With Browser Navigation'));
		$click[] = mosHTML::makeOption( '1',  T_('New Window With Browser Navigation'));
		$click[] = mosHTML::makeOption( '2', T_('New Window Without Browser Navigation'));
		$target = mosHTML::selectList( $click, 'browserNav', 'class="inputbox" size="4"', 'value', 'text', intval( $row->browserNav ) );
		return $target;
	}

	/**
	* build the select list to choose a category
	*/
	public static function Category( &$menu, $id, $javascript='' ) {
		$database = aliroDatabase::getInstance();
		$query = "SELECT c.id AS `value`, c.section AS `id`, CONCAT_WS( ' / ', s.title, c.title) AS `text`"
		. "\n FROM #__sections AS s"
		. "\n INNER JOIN #__categories AS c ON c.section = s.id"
		. "\n WHERE s.scope = 'content'"
		. "\n ORDER BY s.name,c.name"
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		$category = '';
		if ( $id ) {
			foreach ( $rows as $row ) {
				if ( $row->value == $menu->componentid ) {
					$category = $row->text;
				}
			}
			$category .= '<input type="hidden" name="componentid" value="'. $menu->componentid .'" />';
			$category .= '<input type="hidden" name="link" value="'. $menu->link .'" />';
		} else {
			$category = mosHTML::selectList( $rows, 'componentid', 'class="inputbox" size="10"'. $javascript, 'value', 'text' );
			$category .= '<input type="hidden" name="link" value="" />';
		}
		return $category;
	}

	/**
	* build the select list to choose a section
	*/
	public static function Section( &$menu, $id, $all=0 ) {
		$database = aliroDatabase::getInstance();
		$query = "SELECT s.id AS `value`, s.id AS `id`, s.title AS `text`"
		. "\n FROM #__sections AS s"
		. "\n WHERE s.scope = 'content'"
		. "\n ORDER BY s.name"
		;
		$database->setQuery( $query );
		if ( $all ) {
			$rows[] = mosHTML::makeOption( 0, T_('- All Sections -') );
			$rows = array_merge( $rows, $database->loadObjectList() );
		} else {
			$rows = $database->loadObjectList();
		}

		if ( $id ) {
			foreach ( $rows as $row ) {
				if ( $row->value == $menu->componentid ) {
					$section = $row->text;
				}
			}
			$section .= '<input type="hidden" name="componentid" value="'. $menu->componentid .'" />';
			$section .= '<input type="hidden" name="link" value="'. $menu->link .'" />';
		} else {
			$section = mosHTML::selectList( $rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text' );
			$section .= '<input type="hidden" name="link" value="" />';
		}
		return $section;
	}

	/**
	* build the select list to choose a component
	*/
	public static function Component( &$menu, $id ) {
		die ('mosAdminMenu::Component() has been withdrawn');
	}

	/**
	* build the select list to choose a component
	*/
	public static function ComponentName( &$menu, $id ) {
		die ('mosAdminMenu::ComponentName() has been withdrawn');
	}

	/**
	* build the select list to choose an image
	*/
	public static function Images( $name, &$active, $javascript=NULL, $directory=NULL ) {
		if ( !$javascript ) {
			$javascript = "onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='../images/stories/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
		}
		if ( !$directory ) {
			$directory = '/images/stories';
		}

		$info = criticalInfo::getInstance();
		$dir = new aliroDirectory ($info->absolute_path.$directory);
		$imageFiles = $dir->listFiles ();
		$images = array(  mosHTML::makeOption( '', T_('- Select Image -') ) );
		foreach ( $imageFiles as $file ) {
			if ( eregi( "bmp|gif|jpg|png", $file ) ) {
				$images[] = mosHTML::makeOption( $file );
			}
		}
		$images = mosHTML::selectList( $images, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

		return $images;
	}

	/**
	* build the select list for Ordering of a specified Table
	*/
	public static function SpecificOrdering( &$row, $id, $query, $neworder=0 ) {
		if ( $neworder ) {
			$text = T_('New items default to the first place. Ordering can be changed after this item is saved.');
		} else {
			$text = T_('New items default to the last place. Ordering can be changed after this item is saved.');
		}

		if ( $id ) {
			$order = mosAdminMenus::mosGetOrderingList( $row, $query );
			$ordering = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
		} else {
			$ordering = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. $text;
		}
		return $ordering;
	}

	/**
	* Select list of active users
	*/
	public static function UserSelect( $name, $active, $nouser=0, $javascript=NULL, $order='name' ) {
		$database = aliroDatabase::getInstance();
		$query = "SELECT id AS value, name AS text"
		. "\n FROM #__users"
		. "\n WHERE block = '0'"
		. "\n ORDER BY ". $order
		;
		$database->setQuery( $query );
		if ( $nouser ) {
			$users[] = mosHTML::makeOption( '0', T_('- No User -') );
			$users = array_merge( $users, $database->loadObjectList() );
		} else {
			$users = $database->loadObjectList();
		}

		$users = mosHTML::selectList( $users, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

		return $users;
	}

	/**
	* Select list of positions - generally used for location of images
	*/
	public static function Positions( $name, $active=NULL, $javascript=NULL, $none=1, $center=1, $left=1, $right=1 ) {
		if ( $none ) {
			$pos[] = mosHTML::makeOption( '', T_('None') );
		}
		if ( $center ) {
			$pos[] = mosHTML::makeOption( 'center', T_('Center') );
		}
		if ( $left ) {
			$pos[] = mosHTML::makeOption( 'left', T_('Left') );
		}
		if ( $right ) {
			$pos[] = mosHTML::makeOption( 'right', T_('Right') );
		}

		$positions = mosHTML::selectList( $pos, $name, 'class="inputbox" size="1"'. $javascript, 'value', 'text', $active );

		return $positions;
	}

	/**
	* Select list of active categories for components
	*/
	public static function ComponentCategory( $name, $section, $active=NULL, $javascript=NULL, $order='ordering', $size=1, $sel_cat=1 ) {
		$database = aliroDatabase::getInstance();
		$query = "SELECT id AS value, name AS text"
		. "\n FROM #__categories"
		. "\n WHERE section = '". $section ."'"
		. "\n AND published = '1'"
		. "\n ORDER BY ". $order
		;
		$database->setQuery( $query );
		$cats = $database->loadObjectList();
		if ( $sel_cat ) {
			$categories[] = mosHTML::makeOption( '0', T_('- All Categories -') );
			if ($cats) $categories = array_merge($categories, $cats);
		} else {
			$categories = $cats ? $cats : array();
		}

		if ( count( $categories ) < 1 ) {
			mosRedirect( 'index.php?option=com_categories&section='. $section, T_('You must create a category first.') );
		}

		$category = mosHTML::selectList( $categories, $name, 'class="inputbox" size="'. $size .'" '. $javascript, 'value', 'text', $active );

		return $category;
	}

	/**
	* Select list of active sections
	*/
	public static function SelectSection( $name, $active=NULL, $javascript=NULL, $order='ordering' ) {
		$database = aliroDatabase::getInstance();
		$categories[] = mosHTML::makeOption( '0', T_('- All Sections -') );
		$query = "SELECT id AS value, title AS text"
		. "\n FROM #__sections"
		. "\n WHERE published = '1'"
		. "\n ORDER BY ". $order
		;
		$results = $database->setQuery( $query );
		if ($results) $categories = array_merge ($categories, $results);
		return mosHTML::selectList( $categories, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );
	}

	/**
	* Select list of menu items for a specific menu
	*/
	public static function Links2Menu( $type, $_and ) {
		$database = aliroCoreDatabase::getInstance();
		$query = "SELECT *"
		. "\n FROM #__menu"
		. "\n WHERE type = '". $type ."'"
		. "\n AND published = '1'"
		. $_and
		;
		$database->setQuery( $query );
		$menus = $database->loadObjectList();

		return $menus;
	}

	/**
	* Select list of menus
	*/
	public static function MenuSelect( $name='menuselect', $javascript=NULL ) {
		$database = aliroCoreDatabase::getInstance();
		$query = "SELECT params"
		. "\n FROM #__modules"
		. "\n WHERE module = 'mod_mainmenu'"
		;
		$database->setQuery( $query );
		$menus = $database->loadObjectList();
		$total = count( $menus );
		for( $i = 0; $i < $total; $i++ ) {
			$paramcode = $menus[$i]->params;
			$params = $paramcode ? unserialize ($paramcode) : array();
			foreach ($params as &$param) $param = base64_decode($param);
			if (isset($params['menutype'])) {
				$menuselect[$i]->value = $params['menutype'];
				$menuselect[$i]->text = $params['menutype'];
			}
		}
		// sort array of objects
		if (isset($menuselect)) {
			SortArrayObjects( $menuselect, 'text', 1 );
			$menus = mosHTML::selectList( $menuselect, $name, 'class="inputbox" size="10" '. $javascript, 'value', 'text' );
			return $menus;
		}
		else return '';
	}

	/**
	* Checks to see if an image exists in the current templates image directory
 	* if it does it loads this image.  Otherwise the default image is loaded.
	* Also can be used in conjunction with the menulist param to create the chosen image
	* load the default or use no image
	*/
	public static function ImageCheckAdmin( $file, $directory, $param=NULL, $param_directory='/administrator/images/', $alt=NULL, $name=NULL, $type=1, $align='middle' ) {
		$mosConfig_absolute_path = aliroCore::get('mosConfig_absolute_path');
		$mosConfig_admin_site = aliroCore::get('mosConfig_admin_site');
		$mosConfig_live_site = aliroCore::get('mosConfig_live_site');
		$mainframe = mosMainFrame::getInstance();
		$cur_template = $mainframe->getTemplate();

		if ( $param ) {
			$image = $mosConfig_live_site. $param_directory . $param;
			if ( $type ) {
				$image = '<img src="'. $image .'" align="'. $align .'" alt="'. $alt .'" name="'. $name .'" border="0" />';
			}
		} else if ( $param == -1 ) {
			$image = '';
		} else {
			if ( file_exists( $mainframe->getCfg ('admin_absolute_path') .'/templates/'. $cur_template .'/images/'. $file ) ) {
				$image = $mosConfig_admin_site .'/templates/'. $cur_template .'/images/'. $file;
			} else {
				$image = $mosConfig_live_site. $directory . $file;
			}

			// outputs actual html <img> tag
			if ( $type ) {
				$image = '<img src="'. $image .'" alt="'. $alt .'" align="'. $align .'" name="'. $name .'" border="0" />';
			}
		}

		return $image;
	}

	/**
	* Internal public static function to recursive scan the media manager directories
	* @param string Path to scan
	* @param string root path of this folder
	* @param array  Value array of all existing folders
	* @param array  Value array of all existing images
	*/
	public static function ReadImages( $imagePath, $folderPath, &$folders, &$images ) {
		$dir = new aliroDirectory ($imagePath);
		$imgFiles = $dir->listFiles ();

		foreach ($imgFiles as $file) {
			$ff_ 	= $folderPath . $file .'/';
			$ff 		= $folderPath . $file;
			$i_f 	= $imagePath .'/'. $file;

			if ( is_dir( $i_f ) && $file <> 'CVS' ) {
				$folders[] = mosHTML::makeOption( $ff_ );
				mosAdminMenus::ReadImages( $i_f, $ff_, $folders, $images );
			} else if ( eregi( "bmp|gif|jpg|png", $file ) && is_file( $i_f ) ) {
				// leading / we don't need
				$imageFile = substr( $ff, 1 );
				$images[$folderPath][] = mosHTML::makeOption( $imageFile, $file );
			}
		}
	}

	public static function GetImageFolders( &$folders, $path ) {
		$javascript 	= "onchange=\"changeDynaList( 'imagefiles', folderimages, document.adminForm.folders.options[document.adminForm.folders.selectedIndex].value, 0, 0);  previewImage( 'imagefiles', 'view_imagefiles', '$path/' );\"";
		$getfolders 	= mosHTML::selectList( $folders, 'folders', 'class="inputbox" size="1" '. $javascript, 'value', 'text', '/' );
		return $getfolders;
	}

	public static function GetImages( &$images, $path ) {
		if ( !isset($images['/'] ) ) {
			$images['/'][] = mosHTML::makeOption( '' );
		}

		//$javascript	= "onchange=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\" onfocus=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\"";
		$javascript	= "onchange=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\"";
		$getimages	= mosHTML::selectList( $images['/'], 'imagefiles', 'class="inputbox" size="10" multiple="multiple" '. $javascript , 'value', 'text', null );

		return $getimages;
	}

	public static function GetSavedImages( &$row, $path ) {
		$images2 = array();
		foreach( $row->images as $file ) {
			$temp = explode( '|', $file );
			if( strrchr($temp[0], '/') ) {
				$filename = substr( strrchr($temp[0], '/' ), 1 );
			} else {
				$filename = $temp[0];
			}
			$images2[] = mosHTML::makeOption( $file, $filename );
		}
		//$javascript	= "onchange=\"previewImage( 'imagelist', 'view_imagelist', '$path/' ); showImageProps( '$path/' ); \" onfocus=\"previewImage( 'imagelist', 'view_imagelist', '$path/' )\"";
		$javascript	= "onchange=\"previewImage( 'imagelist', 'view_imagelist', '$path/' ); showImageProps( '$path/' ); \"";
		$imagelist 	= mosHTML::selectList( $images2, 'imagelist', 'class="inputbox" size="10" '. $javascript, 'value', 'text' );

		return $imagelist;
	}

	public static function menutypes() {
		die ('mosAdminMenu::menutypes() has been withdrawn');
	}

	/*
	* loads files required for menu items
	*/
	public static function menuItem( $item ) {
		die ('mosAdminMenu::menuItem() has been withdrawn');
	}

	/**
	* @param string SQL with ordering As value and 'name field' AS text
	* @param integer The length of the truncated headline
	*/
	private static function mosGetOrderingList( $row, $sql, $chop='30' ) {
		$order = array();
		$database = $row->getDatabase();
		$database->setQuery( $sql );
		if (!($orders = $database->loadObjectList())) {
			if ($database->getErrorNum()) {
				echo $database->stderr();
				return false;
			} else {
				$order[] = mosHTML::makeOption( 1, 'first' );
				return $order;
			}
		}
		$order[] = mosHTML::makeOption( 0, '0 first' );
		for ($i=0, $n=count( $orders ); $i < $n; $i++) {

	        if (strlen($orders[$i]->text) > $chop) {
	        	$text = substr($orders[$i]->text,0,$chop)."...";
	        } else {
	        	$text = $orders[$i]->text;
	        }

			$order[] = mosHTML::makeOption( $orders[$i]->value, $orders[$i]->value.' ('.$text.')' );
		}
		$order[] = mosHTML::makeOption( $orders[$i-1]->value+1, ($orders[$i-1]->value+1).' last' );

		return $order;
	}

}

class mosToolBar {

	/**
	* Writes the start of the button bar table
	*/
	function startTable() {
		?>
		<table cellpadding="0" cellspacing="0" border="0" width="99%">
		<tr>
		<?php
	}

	/**
	* Writes a custom option and task button for the button bar
	* @param string The task to perform (picked up by the switch($task) blocks
	* @param string The image to display
	* @param string The image to display when moused over
	* @param string The alt text for the icon image
	* @param boolean True if required to check that a standard list item is checked
	*/
	function custom( $task='', $icon='', $iconOver='', $alt='', $listSelect=true ) {
		if ($listSelect) {
			$href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('".T_('Please make a selection from the list to') ." $alt');}else{submitbutton('$task')}";
		} else {
			$href = "javascript:submitbutton('$task')";
		}
		?>
		<td width="25" align="center">
		<a href="<?php echo $href;?>" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','images/<?php echo $iconOver;?>',1);">
		<img name="<?php echo $task;?>" src="images/<?php echo $icon;?>" alt="<?php echo $alt;?>" border="0" />
		</a>
		</td>
		<?php
	}

	/**
	* Writes the common 'new' icon for the button bar
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function addNew( $task='new', $alt=null ) {
	    if (is_null($alt)) $alt = T_('New');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'new.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'new_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('<?php echo $task;?>');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a common 'publish' button
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function publish( $task='publish', $alt=null ) {
	    if (is_null($alt)) $alt = T_('Published');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'publish.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'publish_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('<?php echo $task;?>');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a common 'publish' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function publishList( $task='publish', $alt=null ) {
	    if (is_null($alt)) $alt = T_('Published');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'publish.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'publish_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo T_('Please make a selection from the list to publish')?> '); } else {submitbutton('<?php echo $task;?>', '');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a common 'unpublish' button
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function unpublish( $task='unpublish', $alt=null ) {
		if (is_null($alt)) $alt = T_('Unpublished');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'unpublish.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'unpublish_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('<?php echo $task;?>');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);" >
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a common 'unpublish' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function unpublishList( $task='unpublish', $alt=null ) {
		if (is_null($alt)) $alt = T_('Unpublished');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'unpublish.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'unpublish_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php T_('Please make a selection from the list to unpublish') ?>'); } else {submitbutton('<?php echo $task;?>', '');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);" >
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a common 'archive' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function archiveList( $task='archive', $alt=null ) {
	    if (is_null($alt)) $alt = T_('Archive');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'archive.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'archive_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php T_('Please make a selection from the list to archive') ?>'); } else {submitbutton('<?php echo $task;?>', '');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes an unarchive button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function unarchiveList( $task='unarchive', $alt=null ) {
	    if (is_null($alt)) $alt = T_('Unarchive');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'unarchive.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'unarchive_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php T_('Please select a news story to unarchive') ?>'); } else {submitbutton('<?php echo $task;?>', '');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a common 'edit' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function editList( $task='edit', $alt=null ) {
	    if (is_null($alt)) $alt = T_('Edit');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'html.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'html_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php T_('Please select an item from the list to edit') ?>'); } else {submitbutton('<?php echo $task;?>', '');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a common 'edit' button for a template html
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function editHtml( $task='edit_source', $alt=null ) {
	    if (is_null($alt)) $alt =  T_('Edit HTML');
		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'html.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'html_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php T_('Please select an item from the list to edit') ?>'); } else {submitbutton('<?php echo $task;?>', '');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a common 'edit' button for a template css
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function editCss( $task='edit_css', $alt=null ) {
	    if (is_null($alt)) $alt = T_('Edit CSS');
		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'css.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'css_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php T_('Please select an item from the list to edit') ?>'); } else {submitbutton('<?php echo $task;?>', '');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a common 'delete' button for a list of records
	* @param string  Postscript for the 'are you sure' message
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function deleteList( $msg='', $task='remove', $alt=null ) {
	    if (is_null($alt)) $alt = T_('Delete');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'delete.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'delete_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php T_('Please make a selection from the list to delete') ?>'); } else if (confirm('<?php T_('Are you sure you want to delete selected items?') ?> <?php echo $msg;?>')){ submitbutton('<?php echo $task;?>');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a preview button for a given option (opens a popup window)
	* @param string The name of the popup file (excluding the file extension)
	*/
	function preview( $popup='' ) {
		die('Preview is not currently supported');
		$sql = "SELECT template FROM #__templates_menu WHERE client_id='0' AND menuid='0'";
		$database->setQuery( $sql );
		$cur_template = $database->loadResult();
		$image = mosAdminMenus::ImageCheck( 'preview.png', 'images/', NULL, NULL, T_('Preview'), 'preview' );
		$image2 = mosAdminMenus::ImageCheck( 'preview_f2.png', 'images/', NULL, NULL, T_('Preview'), 'preview', 0 );
		?>
		<td width="25" align="center">
		<a href="#" onclick="window.open('popups/<?php echo $popup;?>.php?t=<?php echo $cur_template; ?>', 'win1', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('preview','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a save button for a given option
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function save( $task='save', $alt=null ) {
	    if (is_null($alt)) $alt = T_('Save');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'save.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'save_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('<?php echo $task;?>');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2;?>',1);">
		<?php echo $image;?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a save button for a given option (NOTE this is being deprecated)
	*/
	function savenew() {
		$image = mosAdminMenus::ImageCheck( 'save.png', '/images/', NULL, NULL, T_('save'), 'save' );
		$image2 = mosAdminMenus::ImageCheck( 'save_f2.png', '/images/', NULL, NULL, T_('save'), 'save', 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('savenew');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('save','','<?php echo $image2;?>',1);">
		<?php echo $image;?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a save button for a given option (NOTE this is being deprecated)
	*/
	function saveedit() {
		$image = mosAdminMenus::ImageCheck( 'save.png', '/images/', NULL, NULL, T_('save'), 'save' );
		$image2 = mosAdminMenus::ImageCheck( 'save_f2.png', '/images/', NULL, NULL, T_('save'), 'save', 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('saveedit');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('save','','<?php echo $image2;?>',1);">
		<?php echo $image;?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a cancel button and invokes a cancel operation (eg a checkin)
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function cancel( $task='cancel', $alt=null ) {
		if (is_null($alt)) $alt = T_('Cancel');

		$mainframe = mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'cancel.png', '/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'cancel_f2.png', '/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('<?php echo $task;?>');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2;?>',1);">
		<?php echo $image;?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a cancel button that will go back to the previous page without doing
	* any other operation
	*/
	function back() {
		$image = mosAdminMenus::ImageCheck( 'back.png', '/images/', NULL, NULL, T_('back'), 'cancel' );
		$image2 = mosAdminMenus::ImageCheck( 'back_f2.png', '/images/', NULL, NULL, T_('back'), 'cancel', 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:window.history.back();" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('cancel','','images/<?php echo $image2;?>',1);">
		<?php echo $image;?>
		</a>
		</td>
		<?php
	}

	/**
	* Write a divider between menu buttons
	*/
	function divider() {
		$image = $mainframe->ImageCheck( 'menu_divider.png', '/images/' );
		?>
		<td width="25" align="center">
		<?php echo $image; ?>
		</td>
		<?php
	}

	/**
	* Writes a media_manager button
	* @param string The sub-drectory to upload the media to
	*/
	function media_manager( $directory = '' ) {
		$image = mosAdminMenus::ImageCheck( 'upload.png', '/images/', NULL, NULL, T_('Upload Image'), 'uploadPic' );
		$image2 = mosAdminMenus::ImageCheck( 'upload_f2.png', '/images/', NULL, NULL, T_('Upload Image'), 'uploadPic', 0 );
		?>
		<td width="25" align="center">
		<a href="#" onclick="popupWindow('popups/uploadimage.php?directory=<?php echo $directory; ?>','win1',250,100,'no');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('uploadPic','','<?php echo $image2; ?>',1);">
		<?php echo $image; ?>
		</a>
		</td>
		<?php
	}

	/**
	* Writes a spacer cell
	* @param string The width for the cell
	*/
	function spacer( $width='' )
	{
		if ($width != '') {
?>
		<td width="<?php echo $width;?>">&nbsp;</td>
<?php
		} else {
?>
		<td>&nbsp;</td>
<?php
		}
	}

	/**
	* Writes the end of the menu bar table
	*/
	function endTable() {
		?>
		</tr>
		</table>
		<?php
	}
}