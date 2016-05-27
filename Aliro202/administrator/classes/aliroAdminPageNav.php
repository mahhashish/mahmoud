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

	public function __construct ( $total, $limitstart, $limit ) {
		$this->js_enabled = aliroRequest::getInstance()->getStickyAliroParam($_POST, 'alironoscript') ? false : true;
		parent::__construct($total, $limitstart, $limit);
	}
	
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

	public function getListFooter($link='') {
		$display = T_('Display #');
		$html = <<<LIST_FOOTER

		<table class="adminlist">
			<thead>
				<tr>
					<th colspan="3">
						{$this->getPagesLinks($link)}
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
		return $this->askTemplate('orderUpIcon', $i, ($i+$this->limitstart > 0), $condition, $task, $alt);
	}

	public function noJavaOrderUpIcon( $i, $condition=true, $url ) {
		return $this->askTemplate('noJavaOrderUpIcon', $i, ($i+$this->limitstart > 0), $condition, $url);
	}

	public function orderDownIcon( $i, $n, $condition=true, $task='orderdown', $alt='Move Down' ) {
		return $this->askTemplate('orderDownIcon', $i, ($i+$this->limitstart < $this->total-1), $n, $condition, $task, $alt);
	}

	public function noJavaOrderDownIcon( $i, $n, $condition=true, $url ) {
		return $this->askTemplate('noJavaOrderDownIcon', $i, ($i+$this->limitstart < $this->total-1), $n, $condition, $url);
	}
	
	private function askTemplate () {
		$parms = func_get_args();
		$method = array_shift($parms);
		$template = aliroRequest::getInstance()->getTemplateObject();
		return call_user_func_array(array($template, $method), $parms);
	}

}