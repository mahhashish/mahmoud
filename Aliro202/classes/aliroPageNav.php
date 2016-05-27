<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more
 * information on this, please see the index.php file or visit
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * aliroPageNav is a container that actually instantiates either aliroUserPageNav
 * or aliroAdminPageNav, depending on whether we are user side or admin side.
 * All calls are then passed to the instantiated class.
 *
 * aliroAbstractPageNav provides a base and does much of the work for both the
 * admin and user side classes.
 *
 * aliroUserPageNav is the actual class for user side page navigation.
 *
 */

class aliroPageNav {
	protected $realPageNav = null;

	public function __construct ($total, $limitstart, $limit) {
		if (_ALIRO_IS_ADMIN) $this->realPageNav = new aliroAdminPageNav ($total, $limitstart, $limit);
		else $this->realPageNav = new aliroUserPageNav ($total, $limitstart, $limit);
	}

	public function __get ($property) {
		return $this->realPageNav->$property;
	}

	public function __call ($method, $args) {
		return call_user_func_array(array($this->realPageNav, $method), $args);
	}

}

class mosPageNav extends aliroPageNav {
	//  Actually just an alias for backwards compatibility
}

abstract class aliroAbstractPageNav {
	public $limitstart = null;
	public $limit = null;
	public $total = null;
	protected $js_enabled = false;

	public function __construct ( $total, $limitstart, $limit ) {
		$this->total = max($total, 0);
		$this->limit = max($limit, 1);
		if ($this->limit > $this->total) $this->limitstart = 0;
		else {
			while ($limitstart > $this->total) $limitstart -= $this->limit;
			$this->limitstart = max($limitstart, 0);
		}
	}

	protected function makeLimitSteps () {
		$steps = array ('5', '10', '15', '20', '25', '30', '50', '100');
		$alirohtml = aliroHTML::getInstance();
		foreach ($steps as $step) $limits[] = $alirohtml->makeOption($step, $step, $step == $this->limit);
		return $limits;
	}

	// SEF is not used admin side
	protected function fixLink ($link) {
		return $this->isAdmin ? $link : aliroSEF::getInstance()->sefRelToAbs($link);
	}

	/**
	* The html for the pages counter, eg, Results 1-10 of x
	*/
	public function getPagesCounter() {
	    $html = '';
		$to_result = min ($this->limitstart + $this->limit, $this->total);
		if ($this->total) $html .= sprintf(T_("Results %d to %d of %d"), $this->limitstart+1, $to_result, $this->total);
		else $html .= T_('No records found.');
		return $html;
	}

	/**
	* The html for the Start - Previous - numbers - Next - End links
	*/
	// The link parameter is actually mandatory but has to be shown as if optional for compatibility with admin version
	public function writePagesLinks($link='') {
		$html = '';
		$total_pages = ceil( $this->total / $this->limit );
		$this_page = ceil( ($this->limitstart+1) / $this->limit );
		$start_loop = (floor(($this_page-1)/_ALIRO_PAGE_NAV_DISPLAY_PAGES))*_ALIRO_PAGE_NAV_DISPLAY_PAGES+1;
		$stop_loop = min($total_pages, $start_loop + _ALIRO_PAGE_NAV_DISPLAY_PAGES - 1);
		$link .= '&amp;limit='. $this->limit;
		if ($this_page > 1) {
			$page = ($this_page - 2) * $this->limit;
			$html .= $this->makePageLink($link, 0, T_('first page'), '&lt;&lt; '.T_('Start'));
			$html .= $this->makePageLink($link, $page, T_('previous page'), '&lt; '.T_('Previous'));
		} else {
			$html .= "\n".'<span class="pagenav">&lt;&lt; '. T_('Start') .'</span> ';
			$html .= "\n".'<span class="pagenav">&lt; '. T_('Previous') .'</span> ';
		}
		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $this->limit;
			if ($i == $this_page) $html .= '<span class="pagenav">'. $i .'</span> ';
			else $html .= $this->makePageLink($link, $page, T_('page ').$page, $i, true);
		}
		if ($this_page < $total_pages) {
			$page = $this_page * $this->limit;
			$end_page = ($total_pages-1) * $this->limit;
			$html .= $this->makePageLink($link, $page, T_('next page'), T_('Next').' &gt;');
			$html .= $this->makePageLink($link, $end_page, T_('end page'), T_('End').' &gt;&gt;');
		} else {
			$html .= "\n".'<span class="pagenav">'. T_('Next') .' &gt;</span> ';
			$html .= "\n".'<span class="pagenav">'. T_('End') .' &gt;&gt;</span>';
		}
		return $html;
	}
	
	protected function makePageLink ($link, $page, $title, $text, $isStrong=false) {
		return $this->js_enabled ? $this->makeJSlink($page, $title, $text, $isStrong) : $this->makeHTMLlink($link, $page, $title, $text, $isStrong);
	}
	
	protected function makeHTMLlink ($link, $page, $title, $text, $isStrong) {
		if ($isStrong) $text = '<strong>'.$text.'</strong>';
		return "\n".'<a href="'.$this->fixLink ($link.'&amp;limitstart='.$page).'" class="pagenav" title="'.$title.'">'.$text.'</a> ';
	}
	
	protected function makeJSlink ($page, $title, $text, $isStrong) {
		if ($isStrong) $text = '<strong>'.$text.'</strong>';
		return "\n".'<a href="#" class="pagenav" title="'.$title.'" onclick="javascript:document.adminForm.limitstart.value='.$page.'; YUI.ALIRO.CORE.submitform(); return false;">'.$text.'</a> ';
	}

}

class aliroUserPageNav extends aliroAbstractPageNav {
	protected $isAdmin = false;
	protected $request = null;
	protected $option = '';
	
	public function __construct ($total) {
		$this->request = aliroRequest::getInstance();
		$this->option = $this->request->getOption();
		$defaultlimit = isset($_SESSION[$this->option.'_paging_limit']) ? $_SESSION[$this->option.'_paging_limit'] : $this->request->getCfg('list_limit');
		$defaultlimit = max(5,intval($defaultlimit));
		$limit = $this->request->getParam($_REQUEST, 'limit', $defaultlimit);
		$_SESSION[$this->option.'_paging_limit'] = $limit;
		$page = $this->getPage();
		parent::__construct ($total, $limit * ($page - 1), $limit);
	}
	
	public function getPage () {
		$defaultpage = isset($_SESSION[$this->option.'_current_page']) ? $_SESSION[$this->option.'_current_page'] : 1;
		return $_SESSION[$this->option.'_current_page'] = max(1, $this->request->getParam($_REQUEST, 'page', $defaultpage));
	}
	/**
	* Returns the html limit # input box
	* @param string The basic link to include in the href
	* @return string
	*/
	public function getLimitBox ($link) {
		// build the html select list
		$link = $this->request->getCfg('live_site').'/'.($link.'&amp;limit=\' + this.options[selectedIndex].value + \'&amp;limitstart='.$this->limitstart);
		return aliroHTML::getInstance()->selectList($this->makeLimitSteps(), 'limit',
		'class="inputbox" size="1" onchange="document.location.href=\''.$link.'\';"',
		'value', 'text', $this->limit);
	}
	/**
	* Writes the html limit # input box
	* @param string The basic link to include in the href
	*/
	public function writeLimitBox ( $link ) {
		echo $this->getLimitBox( $link );
	}
	/**
	* Writes the html for the pages counter, eg, Results 1-10 of x
	*/
	public function writePagesCounter() {
		return parent::getPagesCounter();
	}

	/**
	* Writes the html for the leafs counter, eg, Page 1 of x
	*/
	public function writeLeafsCounter() {
		$txt = '';
		$page = $this->limitstart+1;
		if ($this->total > 0) {
			$txt .= sprintf(T_('Page %d of %d'), $page, $this->total);
		}
		return $txt;
	}

	/**
	* The html for the Start - Previous - numbers - Next - End links
	*/
	// The link parameter is actually mandatory but has to be shown as if optional for compatibility with admin version
	public function writePagesLinks($link='') {
		$html = '';
		$total_pages = ceil( $this->total / $this->limit );
		if (2 > $total_pages) return '';
		$this_page = ceil( ($this->limitstart+1) / $this->limit );
		$start_loop = (floor(($this_page-1)/_ALIRO_PAGE_NAV_DISPLAY_PAGES))*_ALIRO_PAGE_NAV_DISPLAY_PAGES+1;
		$stop_loop = min($total_pages, $start_loop + _ALIRO_PAGE_NAV_DISPLAY_PAGES - 1);
		// $link .= '&amp;limit='. $this->limit;
		if ($this_page > 1) {
			$html .= $this->makePageLink($link, 1, T_('first page'), '&lt;&lt; '.T_('Start'));
			$html .= $this->makePageLink($link, $this_page - 1, T_('previous page'), '&lt; '.T_('Previous'));
		} else {
			$html .= "\n".'<span class="pagenav">&lt;&lt; '. T_('Start') .'</span> ';
			$html .= "\n".'<span class="pagenav">&lt; '. T_('Previous') .'</span> ';
		}
		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			if ($i == $this_page) $html .= '<span class="pagenav">'. $i .'</span> ';
			else $html .= $this->makePageLink($link, $i, T_('page ').$i, $i, true);
		}
		if ($this_page < $total_pages) {
			$html .= $this->makePageLink($link, $this_page+1, T_('next page'), T_('Next').' &gt;');
			$html .= $this->makePageLink($link, $total_pages, T_('end page'), T_('End').' &gt;&gt;');
		} else {
			$html .= "\n".'<span class="pagenav">'. T_('Next') .' &gt;</span> ';
			$html .= "\n".'<span class="pagenav">'. T_('End') .' &gt;&gt;</span>';
		}
		return $html;
	}
	
	protected function makePageLink ($link, $page, $title, $text, $isStrong=false) {
		return $this->js_enabled ? $this->makeJSlink($page, $title, $text, $isStrong) : $this->makeHTMLlink($link, $page, $title, $text, $isStrong);
	}
	
	protected function makeHTMLlink ($link, $page, $title, $text, $isStrong) {
		if ($isStrong) $text = '<strong>'.$text.'</strong>';
		return "\n".'<a href="'.$this->fixLink ($link.'&amp;page='.$page).'" class="pagenav" title="'.$title.'">'.$text.'</a> ';
	}
	
	protected function makeJSlink ($page, $title, $text, $isStrong) {
		if ($isStrong) $text = '<strong>'.$text.'</strong>';
		return "\n".'<a href="#" class="pagenav" title="'.$title.'" onclick="javascript:document.adminForm.page.value='.$page.'; YUI.ALIRO.CORE.submitform(); return false;">'.$text.'</a> ';
	}
}