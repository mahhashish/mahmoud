<?php

class aliroSelectors {
	private static $instance = __CLASS__;

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	/**
	* build the select list for menu parent item
	*/
	public function menuParent ($thismenu) {
	    $alirohtml = aliroHTML::getInstance();
		$mitems[] = $alirohtml->makeOption ('0', T_('Top'));
		$handler = aliroMenuHandler::getInstance();
		$menus = $handler->getByParentOrder($thismenu->menutype);
		foreach ($menus as $menu) {
			if ($menu->id == $thismenu->id) continue;
			$text = $menu->name;
			if ($menu->level) $text = '- '.$text;
			for ($i=0; $i<$menu->level; $i++) $text = '&nbsp;&nbsp;'.$text;
			$mitems[] = $alirohtml->makeOption($menu->id, $text);
		}
		return $alirohtml->selectList($mitems, 'parent', 'class="inputbox" size="1"', 'value', 'text', $thismenu->parent);
	}

	/**
	* build the select list for all menus parent items
	*/
	public function allMenuParent ($thismenu) {
	    $alirohtml = aliroHTML::getInstance();
		$handler = aliroMenuHandler::getInstance();
		$types = $handler->getMenutypeObjects();
		foreach ($types as $type) { 
			$mitems[] = $alirohtml->makeOption ($type->type, $type->name.' - '.T_('Top'));
			$menus = $handler->getByParentOrder($type->type);
			foreach ($menus as $menu) {
				if ($menu->id == $thismenu->id) continue;
				$text = $type->name.' - '.$menu->name;
				if ($menu->level) $text = '- '.$text;
				for ($i=0; $i<$menu->level; $i++) $text = '&nbsp;&nbsp;'.$text;
				$mitems[] = $alirohtml->makeOption($menu->id, $text);
			}
		}
		return $alirohtml->selectList($mitems, 'parent', 'class="inputbox" size="1"', 'value', 'text', $thismenu->parent);
	}

	/**
	* build the multiple select list for Menu Links/Pages
	*/
	public function menuLinks ($lookup, $all=false, $none=false) {
		$alirohtml = aliroHTML::getInstance();
		if ($all) {
			// prepare an array with 'all' as the first item
			$mitems[] = $alirohtml->makeOption(0, T_('All'));
			// adds space, in select box which is not saved
			$mitems[] = $alirohtml->makeOption("-999999", '----');
		}
		if ($none) {
			// prepare an array with 'none' as the first item
			$mitems[] = $alirohtml->makeOption("-999999", T_('None'), (0 == count($lookup)));
			// adds space, in select box which is not saved
			$mitems[] = $alirohtml->makeOption( "-999999", '----' );
		}
		$handler = aliroMenuHandler::getInstance();
		$types = $handler->getMenutypes();
		foreach ($types as $type) {
			$prefix = $type.' | ';
			$menus = $handler->getByParentOrder($type, 0);
			foreach ($menus as $menu) {
				$text = $menu->name;
				if ($menu->level) $text = '- '.$text;
				for ($i=0; $i<$menu->level; $i++) $text = '&nbsp;&nbsp;'.$text;
				$mitems[] = $alirohtml->makeOption((int) $menu->id, $prefix.$text);
			}
			// adds space, in select box which is not saved
			$mitems[] = $alirohtml->makeOption( "-999999", '----' );
		}
		// Special links - e.g. 404 error, offline
		foreach (aliroRequest::getInstance()->getSpecialItemid() as $specialid=>$title) {
			$mitems[] = $alirohtml->makeOption($specialid, $title);
		}
		$mitems[] = $alirohtml->makeOption( "-999999", '----' );
		// urilinks here
		$urilinks = aliroCoreDatabase::getInstance()->doSQLget("SELECT id, name FROM #__urilinks WHERE 0 = notemplate");
		foreach ($urilinks as $urilink) $mitems[] = $alirohtml->makeOption((int) 100000+$urilink->id, $urilink->name);
		$mitems[] = $alirohtml->makeOption( "-999999", '----' );
		return $alirohtml->selectList( $mitems, 'selections[]', 'class="inputbox" size="26" multiple="multiple"', 'value', 'text', $lookup );
	}

}