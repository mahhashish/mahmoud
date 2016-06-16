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
 * aliroHTML is progressively taking over from mosHTML.  It is a singleton rather
 * than a set of static methods, for both style and efficiency reasons.  The
 * mosHTML interface still exists, but makes calls to aliroHTML.
 *
 */

class aliroHTML {
	private static $instance = __CLASS__;

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance());
	}

	public function makeOption ($value, $text='', $selected=false, $valuename='value', $textname='text') {
		$obj = new stdClass;
		$obj->$valuename = $value;
		$obj->$textname = trim($text) ? $text : $value;
		$obj->selected = $selected;
		return $obj;
	}

	// Takes an array of objects and uses it to create a select list
	public function selectList ($selections, $tag_name, $tag_attribs='', $key='value', $text='text', $selected=NULL ) {
		if (!is_array($selections)) return '';
		$selectproperties = array();
		if (is_array($selected)) foreach ($selected as $select) {
			if (is_object($select)) $selectproperties[] = $select->$key;
			else $selectproperties[] = $select;
		}
		else $selectproperties = array($selected);
		$selecthtml = '';
		foreach ($selections as $selection) {
			$select = ((isset($selection->selected) AND $selection->selected) OR in_array($selection->$key, $selectproperties, true)) ? 'selected="selected"' : '';
			$selecthtml .= <<<AN_OPTION
			<option value="{$selection->$key}" $select>
				{$selection->$text}
			</option>
AN_OPTION;
		}
		return <<<THE_SELECT
		<select name="$tag_name" $tag_attribs>
			$selecthtml
		</select>
THE_SELECT;
	}

	public function radioList ($arr, $tag_name, $tag_attribs, $selected=null, $key='value', $text='text') {
		$html = '';
		foreach ($arr as $choice) {
			$extra = @$choice->id ? " id=\"$choice->id\"" : '';
			if (is_array($selected)) foreach ($selected as $obj) {
				if ($choice->$key == $obj->$key) {
					$extra .= ' selected="selected"';
					break;
				}
			}
			else $extra .= ($choice->$key == $selected ? " checked=\"checked\"" : '');
			$html .= <<<RADIO
			<input type="radio" name="$tag_name" value="{$choice->$key}" $extra $tag_attribs />
			{$choice->$text}
RADIO;
		}
		return $html;
	}

	public function yesnoRadioList ($tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO ) {
		$arr = array(
		$this->makeOption( '0', $no, true ),
		$this->makeOption( '1', $yes, true )
		);
		return $this->radioList ($arr, $tag_name, $tag_attribs, $selected);
	}

	public function idBox ($rowNum, $recId, $checkedOut=false, $name='cid') {
		return $checkedOut ? '' : <<<IDBOX
		<input type="checkbox" id="cb$rowNum" name="{$name}[]" value="$recId" onclick="isChecked(this.checked);" />
IDBOX;
	}

	public function toolTip ($tooltip, $title='', $width='', $image='tooltip.png', $text='', $href='#') {
		if ($width) $width = ', WIDTH, \''.$width .'\'';
		if ($title) $title = ', CAPTION, \''.$title .'\'';
		if (!$text) {
			$image = aliroCore::getInstance()->getCfg('live_site').'/includes/js/ThemeOffice/'.$image;
			$text = '<img src="'.$image.'" alt="Tool Tip" />';
		}
		// $style = $href ? '' : 'style="text-decoration: none; color: #333;"';
		return <<<CTOOLTIP
		<a href="$href"> $text <span class="tooltip">$tooltip</span></a>
CTOOLTIP;
		return <<<TOOLTIP
		<a href="$href" onmouseover="return overlib('$tooltip' $title, BELOW, RIGHT $width );" onmouseout="return nd();" $style > $text </a>
TOOLTIP;
	}

	private function checkedOut($row, $overlib=1) {
		if ($overlib) {
			if (!isset($row->editor)) {
				$user = new mosUser();
				$user->load($row->checked_out);
				$row->editor = $user->name;
			}
			$date = $this->formatDate( $row->checked_out_time, '%A, %d %B %Y' );
			$time = $this->formatDate( $row->checked_out_time, '%H:%M' );
			$checked_out_text 	= <<<CHECKED_OUT
<table><tr><td>$row->editor</td></tr><tr><td>$date</td></tr><tr><td>$time</td></tr></table>
CHECKED_OUT;
			$hover = 'onmouseover="return overlib(\''. $checked_out_text .'\', CAPTION, \'Checked Out\', BELOW, RIGHT);" onMouseOut="return nd();"';
		}
		else $hover = '';
		return '<img src="images/checked_out.png" '. $hover .' alt="Checked Out"/>';
	}

	public function formatDate ($date, $format='', $offset=''){
	    $core = aliroCore::getInstance();
		// Format was originally set to %Y-%m-%d %H:%M:%S
		if (!$offset) $offset = $core->getCfg('offset');
		if ($date AND ereg( "([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs ) ) {
		    $date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
			$date = $date > -1 ? aliroLanguage::getInstance()->getDate($format, $date + ($offset*60*60)) : '-';
		}
		return $date;
	}

	public function checkedOutProcessing ($row, $i) {
		if ($row->checked_out) $checked = $this->checkedOut ($row);
		else $checked = $this->idBox ($i, $row->id, ($row->checked_out AND $row->checked_out != aliroUser::getInstance()->id));
		return $checked;
	}

	public function publishedProcessing ($row, $i) {
		$img 	= $row->published ? 'publish_g.png' : 'publish_x.png';
		$task 	= $row->published ? 'unpublish' : 'publish';
		$alt 	= $row->published ? T_('Published') : T_('Unpublished');
		$action	= $row->published ? T_('Unpublish Item') : T_('Publish item');
		return <<<PUBLISH_LINK
		<a href="javascript: void(0);" onclick="return listItemTask('cb$i','$task')" title="$action">
		<img src="images/$img" border="0" alt="$alt" />
		</a>
PUBLISH_LINK;

	}

	public function loadCalendar() {
		$live_site = aliroCore::getInstance()->getCfg('live_site');
		$tags = <<<END_TAGS
		<link rel="stylesheet" type="text/css" media="all" href="$live_site/extclasses/js/calendar/calendar-mos.css" title="green" />
		<!-- import the calendar script -->
		<script type="text/javascript" src="$live_site/extclasses/js/calendar/calendar.js"></script>
		<!-- import the language module -->
		<script type="text/javascript" src="$live_site/extclasses/js/calendar/lang/calendar-en.js"></script>
END_TAGS;
		aliroRequest::getInstance()->addCustomHeadTag ($tags);
	}

}