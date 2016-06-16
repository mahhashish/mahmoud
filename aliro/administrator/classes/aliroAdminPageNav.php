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
 * aliroAdminPageNav is the class that creates page navigation of lists for the
 * admin side.  It extends the basic abstract page navigation class.
 *
 */

class aliroAdminPageNav extends aliroAbstractPageNav  {
	protected $isAdmin = true;

	/**
	* Makes the html for selection of number of lines per page
	*/
	function getLimitBox () {
		$legend = T_('Refresh page');
		$html = aliroHTML::getInstance()->selectList($this->makeLimitSteps(), 'limit', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $this->limit);
		$html .= <<<PER_PAGE
		
		<input type="hidden" name="limitstart" value="$this->limitstart" />
		<noscript><input type="submit" class="button" value="$legend" /></noscript>
		
PER_PAGE;
		return $html;
	}

	function writeLimitBox () {
		echo $this->getLimitBox();
	}

	function writePagesCounter() {
		echo $this->getPagesCounter();
	}

	/**
	* Writes the html for the pages counter, eg, Results 1-10 of x
	*/
	function writePagesLinks($link='') {
	    echo $this->getPagesLinks($link);
	}
	/**
	* The html links for pages, eg, previous, next, 1 2 3 ... x
	*/
	public function getPagesLinks($link='') {
		if (!$link) $link = aliroAdminRequest::getInstance()->simpleURL();
		return parent::writePagesLinks ($link);
	}

	function getListFooter() {
		$display = T_('Display #');
		$html = <<<LIST_FOOTER

		<table class="adminlist">
			<thead>
				<tr>
					<th colspan="3">
						{$this->getPagesLinks()}
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td nowrap="nowrap" width="48%" class="right">
						$display
					</td>
					<td>
						{$this->getLimitBox()}
					</td>
					<td nowrap="nowrap" width="48%">
						{$this->getPagesCounter()}
					</td>
				</tr>
			</tbody>
		</table>

LIST_FOOTER;

  		return $html;
	}
/**
* @param int The row index
* @return int
*/
	public function rowNumber( $i ) {
		return $i + 1 + $this->limitstart;
	}
/**
* @param int The row index
* @param string The task to fire
* @param string The alt text for the icon
* @return string
*/
	public function orderUpIcon( $i, $condition=true, $task='orderup', $alt='Move Up' ) {
		if (($i > 0 || ($i+$this->limitstart > 0)) && $condition) {
		    return '<a href="#reorder" onclick="return listItemTask(\'cb'.$i.'\',\''.$task.'\')" title="'.$alt.'">
				<img src="images/uparrow.png" width="12" height="12" border="0" alt="'.$alt.'" />
			</a>';
  		} else {
  		    return '&nbsp;';
		}
	}

	public function noJavaOrderUpIcon( $i, $condition=true, $url ) {
		if (($i > 0 OR ($i+$this->limitstart > 0)) AND $condition) {
			$alt = T_('Move up');
		    return <<<ORDERUP
		    <a href="$url" title="$alt">
				<img src="images/uparrow.png" width="12" height="12" border="0" alt="$alt" />
			</a>
ORDERUP;
  		} else {
  		    return '&nbsp;';
		}
	}

/**
* @param int The row index
* @param int The number of items in the list
* @param string The task to fire
* @param string The alt text for the icon
* @return string
*/
	public function orderDownIcon( $i, $n, $condition=true, $task='orderdown', $alt='Move Down' ) {
		if (($i < $n-1 || $i+$this->limitstart < $this->total-1) && $condition) {
			return '<a href="#reorder" onclick="return listItemTask(\'cb'.$i.'\',\''.$task.'\')" title="'.$alt.'">
				<img src="images/downarrow.png" width="12" height="12" border="0" alt="'.$alt.'" />
			</a>';
  		} else {
  		    return '&nbsp;';
		}
	}

	public function noJavaOrderDownIcon( $i, $n, $condition=true, $url ) {
		if (($i < $n-1 OR $i+$this->limitstart < $this->total-1) AND $condition) {
			$alt = T_('Move down');
			return <<<ORDERDOWN
			<a href="$url" title="$alt">
				<img src="images/downarrow.png" width="12" height="12" border="0" alt="$alt" />
			</a>
ORDERDOWN;
  		} else {
  		    return '&nbsp;';
		}
	}

}
?>