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
		$info = criticalInfo::getInstance();
		if ($info->isAdmin) $this->realPageNav = new aliroAdminPageNav ($total, $limitstart, $limit);
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

	public function __construct ( $total, $limitstart, $limit ) {
		$this->total = max($total, 0);
		$this->limit = max($limit, 1);
		if ($this->limit > $this->total) $this->limitstart = '0';
		else {
			while ($limitstart > $this->total) $limitstart -= $this->limit;
			$this->limitstart = ($limitstart > 0) ? $limitstart : '0';
		}
	}

	protected function makeLimitSteps () {
		$steps = array ('5', '10', '15', '20', '25', '30', '50');
		$alirohtml = aliroHTML::getInstance();
		foreach ($steps as $step) $limits[] = $alirohtml->makeOption($step);
		return $limits;
	}

	// SEF is not used admin side
	protected function fixLink ($link) {
		if ($this->isAdmin) return $link;
		$sef = aliroSEF::getInstance();
		return $sef->sefRelToAbs($link);
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
			$html .= '<a href="'.$this->fixLink ($link.'&amp;limitstart=0').'" class="pagenav" title="'.T_('first page').'">&lt;&lt; '. T_('Start') .'</a> ';
			$html .= '<a href="'.$this->fixLink ($link.'&amp;limitstart='.$page).'" class="pagenav" title="'.T_('previous page').'">&lt; '. T_('Previous') .'</a> ';
		} else {
			$html .= '<span class="pagenav">&lt;&lt; '. T_('Start') .'</span> ';
			$html .= '<span class="pagenav">&lt; '. T_('Previous') .'</span> ';
		}
		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $this->limit;
			if ($i == $this_page) $html .= '<span class="pagenav">'. $i .'</span> ';
			else $html .= '<a href="'.$this->fixLink ($link.'&amp;limitstart='.$page).'" class="pagenav"><strong>'. $i .'</strong></a> ';
		}
		if ($this_page < $total_pages) {
			$page = $this_page * $this->limit;
			$end_page = ($total_pages-1) * $this->limit;
			$html .= '<a href="'.$this->fixLink ($link.'&amp;limitstart='.$page).' " class="pagenav" title="'.T_('next page').'">'. T_('Next') .' &gt;</a> ';
			$html .= '<a href="'.$this->fixLink ($link.'&amp;limitstart='.$end_page).' " class="pagenav" title="'.T_('end page').'">'. T_('End') .' &gt;&gt;</a>';
		} else {
			$html .= '<span class="pagenav">'. T_('Next') .' &gt;</span> ';
			$html .= '<span class="pagenav">'. T_('End') .' &gt;&gt;</span>';
		}
		return $html;
	}

}

class aliroUserPageNav extends aliroAbstractPageNav {
	protected $isAdmin = false;
	/**
	* Returns the html limit # input box
	* @param string The basic link to include in the href
	* @return string
	*/
	public function getLimitBox ($link) {
		// build the html select list
		$link = aliroSEF::getInstance()->sefRelToAbs($link.'&limit=\' + this.options[selectedIndex].value + \'&limitstart='.$this->limitstart);
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

}