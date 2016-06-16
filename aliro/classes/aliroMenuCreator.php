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
 * aliroMenuCreator is a singleton class that handles the logic of menu creation.
 * It complements the aliroMenuHandler (cached singleton) class which knows about
 * all the menu entries for the system.  It is used by menu modules in conjunction
 * with the aliroMenuHandler to obtain the raw material for building a menu.  The
 * aim is to provide all the logic for menus in the Aliro core, while leaving the
 * construction of XHTML (and maybe CSS) to add-on modules.
 *
 */

class aliroMenuCreator {
	private static $instance = __CLASS__;
	private $config = null;
	private $handler = null;
	private $currlink = '';

	private function __construct () {
		$this->config = aliroCore::getInstance();
		$this->handler = aliroMenuHandler::getInstance();
		$itemid = aliroRequest::getInstance()->getItemid();
		if ($itemid) {
			$currmenu = $this->handler->getMenuByID($itemid);
			if ($currmenu) $this->currlink = $currmenu->link;
		}
	}

	private function __clone () {
		// Null function - private to enforce singleton
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

    private function makeMenuLink($mitem, $params, $maxindent, $subactive) {
        $newlink = new aliroMenuLink ();
        $newlink->id = $mitem->id;
        $newlink->name = $mitem->name;
		$newlink->level = min($mitem->level,$maxindent);
        $newlink->link = aliroSEF::getInstance()->sefRelToAbs($mitem->link);
        // Active Menu highlighting
        $newlink->active = ($this->currlink == $mitem->link);
	$newlink->subactive = $subactive;
        // Set menu link class
        $newlink->opener = $mitem->browserNav;
        if ( $params->get('menu_images', 0)) {
            $menu_params = new aliroParameters($mitem->params);
            $menu_image = $menu_params->def( 'menu_image', -1 );
            if ($menu_image AND $menu_image <> '-1') {
            	$newlink->image = aliroCore::get('mosConfig_live_site').'/images/stories/'.$menu_image;
            	$newlink->image_last = $params->get('menu_images_align', 0);
            }
        }
        return $newlink;
    }

    /**
	* Get images for menu indentation
	*/
    public function getIndents( $params ) {
        $base = aliroCore::getInstance()->getCfg('live_site');
        $imgpath = $base.'/templates/'. aliroRequest::getInstance()->getTemplate() .'/images';

        for ( $i = 1; $i < 7; $i++ ) {
	        switch ($params->get( 'indent_image', 0 )) {

	            case '1':
	            // Default images
                $img[$i] = array("$base/images/M_images/indent$i.png", "Indent $i");
				break;

	            case '2':
	            // Use Params
                $img[$i] =  ('-1' == $params->get('indent_image'.$i, 0)) ? array (NULL, NULL) : array("$base/images/M_images/$parm", "Indent $i");
	            break;

	            case '3':
	            // None
            	$img[$i] = array(NULL,NULL);
            	break;

            	default:
            	// Template
                $img[$i] = array("$imgpath/indent$i.png", "Indent $i");
	            break;
    	    }
        }
        return $img;
    }

    /**
	* Construct a menu
	*/
    public function getMenuData ($params, $maxindent) {
		$menutype = $params->get('menutype', 'mainmenu');
		$rows = $this->handler->getByParentOrder($menutype, true);
		$entries = $subactive = array();
		if (empty($rows)) return $entries;
		foreach ($rows as $i=>$row) $links[$row->id] = $i;
		$show = array(0);
		foreach ($rows as $row) {
			if (!in_array($row->parent, $show)) array_unshift($show, $row->parent);
			elseif (!$row->parent == $show[0]) array_shift($show);
			if ($this->currlink == $row->link) {
				array_push($show, $row->id);
				$parent = $row->parent;
				while ($parent) {
					$subactive[$parent] = 1;
					$parent = $rows[$links[$parent]]->parent;
				}
				break;
			}
		}
		foreach ($rows as $row) if (in_array($row->parent, $show)) {
			$entries[] = $this->makeMenuLink($row, $params, $maxindent, isset($subactive[$row->id]));
		}
        return $entries;
    }
    
}