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
 * Backwards compatibility HTML class
 * 
 */

class mosHTML {

	static function makeOption( $value, $text='' ) {
		return aliroHTML::getInstance()->makeOption ($value, $text);
	}

	static function writableCell( $folder ) {
	 	echo '<tr>';
	  	echo '<td class="item">' . $folder . '/</td>';
	  	echo '<td align="left">';
	  	echo is_writable( "../$folder" ) ? '<b><span class="green">'.T_('Writeable').'</span></b>' : '<b><span class="red">'.T_('Unwriteable').'</span></b>' . '</td>';
	  	echo '</tr>';
	}

	/**
	* Generates an HTML select list
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @param mixed The key that is selected
	* @returns string HTML for the select list
	*/
	static function selectList ( $arr, $tag_name, $tag_attribs, $key, $text, $selected=NULL ) {
		return aliroHTML::getInstance()->selectList($arr, $tag_name, $tag_attribs, $key, $text, $selected);
	}

	/**
	* Writes a select list of integers
	* @param int The start integer
	* @param int The end integer
	* @param int The increment
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @param string The printf format to be applied to the number
	* @returns string HTML for the select list
	*/
	static function integerSelectList( $start, $end, $inc, $tag_name, $tag_attribs, $selected, $format="" ) {
		$start = intval( $start );
		$end = intval( $end );
		$inc = intval( $inc );
		$arr = array();
		for ($i=$start; $i <= $end; $i+=$inc) {
			$fi = $format ? sprintf( "$format", $i ) : "$i";
			$arr[] = mosHTML::makeOption( $fi, $fi );
		}

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Writes a select list of month names based on Language settings
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the select list values
	*/
	static function monthSelectList( $tag_name, $tag_attribs, $selected ) {
		$arr = array(
		mosHTML::makeOption( '01', _JAN ),
		mosHTML::makeOption( '02', _FEB ),
		mosHTML::makeOption( '03', _MAR ),
		mosHTML::makeOption( '04', _APR ),
		mosHTML::makeOption( '05', _MAY ),
		mosHTML::makeOption( '06', _JUN ),
		mosHTML::makeOption( '07', _JUL ),
		mosHTML::makeOption( '08', _AUG ),
		mosHTML::makeOption( '09', _SEP ),
		mosHTML::makeOption( '10', _OCT ),
		mosHTML::makeOption( '11', _NOV ),
		mosHTML::makeOption( '12', _DEC )
		);

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Writes a yes/no select list
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the select list values
	*/
	static function yesnoSelectList( $tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO ) {
		$arr = array(
		mosHTML::makeOption( '0', $no ),
		mosHTML::makeOption( '1', $yes ),
		);

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Generates an HTML radio list
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @returns string HTML for the select list
	*/
	static function radioList( $arr, $tag_name, $tag_attribs, $selected=null, $key='value', $text='text' ) {
		return aliroHTML::getInstance()->radioList($arr, $tag_name, $tag_attribs, $selected, $key, $text);
	}

	/**
	* Writes a yes/no radio list
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	static function yesnoRadioList( $tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO ) {
		return aliroHTML::getInstance()->yesnoRadioList($tag_name, $tag_attribs, $selected, $yes, $no);
	}

	/**
	* @param int The row index
	* @param int The record id
	* @param boolean
	* @param string The name of the form element
	* @return string
	*/
	static function idBox( $rowNum, $recId, $checkedOut=false, $name='cid' ) {
		return aliroHTML::getInstance()->idBox($rowNum, $recId, $checkedOut, $name);
	}

	static function sortIcon( $base_href, $field, $state='none' ) {
		$config = aliroCore::getInstance();
		$alts = array(
		'none' => _CMN_SORT_NONE,
		'asc' => _CMN_SORT_ASC,
		'desc' => _CMN_SORT_DESC,
		);
		$next_state = 'asc';
		if ($state == 'asc') {
			$next_state = 'desc';
		} else if ($state == 'desc') {
			$next_state = 'none';
		}

		$html = "<a href=\"$base_href&field=$field&order=$next_state\">"
		. "<img src=\"{$config->getCfg('live_site')}/images/M_images/sort_$state.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"{$alts[$next_state]}\" />"
		. "</a>";
		return $html;
	}

	/**
	* Writes Close Button
	*/
	static function CloseButton ( &$params, $hide_js=NULL ) {
		// displays close button in Pop-up window
		if ( $params->get( 'popup' ) && !$hide_js ) {
			?>
			<div align="center" style="margin-top: 30px; margin-bottom: 30px;">
			<a href='javascript:window.close();'>
			<span class="small">
			<?php echo _PROMPT_CLOSE;?>
			</span>
			</a>
			</div>
			<?php
		}
	}

	/**
	* Writes Back Button
	*/
	static function BackButton ( &$params, $hide_js=NULL ) {
		// Back Button
		if ( $params->get( 'back_button' ) && !$params->get( 'popup' ) && !$hide_js) {
			?>
			<div class="back_button">
			<a href='javascript:history.go(-1)'>
			<?php echo _BACK; ?>
			</a>
			</div>
			<?php
		}
	}

	/**
	* Cleans text of all formating and scripting code
	*/
	static function cleanText ( &$text ) {
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = strip_tags( $text );
		$text = htmlspecialchars( $text );
		return $text;
	}

	/**
	* Writes Print icon
	*/
	static function PrintIcon( &$row, &$params, $hide_js, $link, $status=NULL ) {
		if ( $params->get( 'print' )  && !$hide_js ) {
			// use default settings if none declared
			if ( !$status ) {
				$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			}

			// checks template image directory for image, if non found default are loaded
			if ( $params->get( 'icons' ) ) {
				$mainframe = mosMainFrame::getInstance();
				$image = $mainframe->ImageCheck( 'printButton.png', '/images/M_images/', NULL, NULL, _CMN_PRINT );
			} else {
				$image = _ICON_SEP .'&nbsp;'. _CMN_PRINT. '&nbsp;'. _ICON_SEP;
			}

			if ( $params->get( 'popup' ) && !$hide_js ) {
				// Print Preview button - used when viewing page
				?>
				<td align="right" width="100%" class="buttonheading">
				<a href="#" onclick="javascript:window.print(); return false" title="<?php echo _CMN_PRINT;?>">
				<?php echo $image;?>
				</a>
				</td>
				<?php
			} else {
				// Print Button - used in pop-up window
				?>
				<td align="right" width="100%" class="buttonheading">
				<a href="javascript:void window.open('<?php echo $link; ?>', 'win2', '<?php echo $status; ?>');" title="<?php echo _CMN_PRINT;?>">
				<?php echo $image;?>
				</a>
				</td>
				<?php
			}
		}
	}

	/**
	* simple Javascript Cloaking
	* email cloacking
 	* by default replaces an email with a mailto link with email cloacked
	*/
	static function emailCloaking( $mail, $mailto=1, $text='', $email=1 ) {
		// convert text
		$mail 		= mosHTML::encoding_converter( $mail );
		// split email by @ symbol
		$mail		= explode( '@', $mail );
		$mail_parts	= explode( '.', $mail[1] );
		// random number
		$rand	= rand( 1, 100000 );

		$replacement 	= "\n<script language='JavaScript' type='text/javascript'> \n";
		$replacement 	.= "<!-- \n";
		$replacement 	.= "var prefix = '&#109;a' + 'i&#108;' + '&#116;o'; \n";
		$replacement 	.= "var path = 'hr' + 'ef' + '='; \n";
		$replacement 	.= "var addy". $rand ." = '". @$mail[0] ."' + '&#64;' + '". implode( "' + '&#46;' + '", $mail_parts ) ."'; \n";
		if ( $mailto ) {
			// special handling when mail text is different from mail addy
			if ( $text ) {
				if ( $email ) {
					// convert text
					$text 	= mosHTML::encoding_converter( $text );
					// split email by @ symbol
					$text 	= explode( '@', $text );
					$text_parts	= explode( '.', $text[1] );
					$replacement 	.= "var addy_text". $rand ." = '". @$text[0] ."' + '&#64;' + '". implode( "' + '&#46;' + '", @$text_parts ) ."'; \n";
				} else {
					$text 	= mosHTML::encoding_converter( $text );
					$replacement 	.= "var addy_text". $rand ." = '". $text ."';\n";
				}
				$replacement 	.= "document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' ); \n";
				$replacement 	.= "document.write( addy_text". $rand ." ); \n";
				$replacement 	.= "document.write( '<\/a>' ); \n";
			} else {
				$replacement 	.= "document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' ); \n";
				$replacement 	.= "document.write( addy". $rand ." ); \n";
				$replacement 	.= "document.write( '<\/a>' ); \n";
			}
		} else {
			$replacement 	.= "document.write( addy". $rand ." ); \n";
		}
		$replacement 	.= "//--> \n";
		$replacement 	.= "</script> \n";
		$replacement 	.= "<noscript> \n";
		$replacement 	.= _CLOAKING;
		$replacement 	.= "\n</noscript> \n";

		return $replacement;
	}

	static function encoding_converter( $text ) {
		// replace vowels with character encoding
		$text 	= str_replace( 'a', '&#97;', $text );
		$text 	= str_replace( 'e', '&#101;', $text );
		$text 	= str_replace( 'i', '&#105;', $text );
		$text 	= str_replace( 'o', '&#111;', $text );
		$text	= str_replace( 'u', '&#117;', $text );

		return $text;
	}

	/**
	* Utility function to provide ToolTips
	* @param string ToolTip text
	* @param string Box title
	* @returns HTML code for ToolTip
	*/
	public static function mosToolTip( $tooltip, $title='', $width='', $image='tooltip.png', $text='', $href='#' ) {
	    return aliroHTML::getInstance()->toolTip ($tooltip, $image, $text, $href);
	}
	
	/**
	* Utility function to provide ToolTips
	* @param string ToolTip text
	* @param string Box title
	* @returns HTML code for ToolTip
	*/
	public static function mosWarning( $tooltip, $title='', $width='', $image='tooltip.png', $text='', $href='#' ) {
	    return aliroHTML::getInstance()->toolTip ($tooltip, $image, $text, $href);
	}

}