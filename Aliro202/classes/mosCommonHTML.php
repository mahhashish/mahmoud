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
 * mosCommonHTML is largely unchanged from Mambo 4.6.x and most methods here are
 * deprecated.  The first few are available from aliroHTML, and the remainder
 * seem insuffiently general to be part of the core.
 *
 */

class mosCommonHTML {


	static function mosFormatDate ($date, $format="", $offset="") {
		return aliroHTML::getInstance()->formatDate ($date, $format, $offset);
	}

	/*
	* Loads all necessary files for JS Overlib tooltips
	*/
	static function loadOverlib() {
		aliroRequest::getInstance()->requestTooltip();
	}

	static function CheckedOutProcessing ($row, $i) {
		return aliroHTML::getInstance()->checkedOutProcessing ($row, $i);
	}

	static function PublishedProcessing ($row, $i) {
		return aliroHTML::getInstance()->publishedProcessing ($row, $i);
	}

	/*
	* Loads all necessary files for JS Calendar
	*/
	static function loadCalendar() {
		aliroHTML::getInstance()->loadCalendar();
	}

	/****************************************************************************
	*
	* The following are deprecated - use similar non-static methods in aliroHTML
	* or consider writing your own code for the long routines that seem presently
	* to be the preserve of content related components and not sufficiently general
	* to warrant inclusion in the core.
	*
	*/

	static function checkedOut( &$row, $tooltip=1 ) {
		$hover = '';
		if ( $tooltip ) {
			$date 				= mosCommonHTML::mosFormatDate( $row->checked_out_time, '%A, %d %B %Y' );
			$time				= mosCommonHTML::mosFormatDate( $row->checked_out_time, '%H:%M' );
			$checked_out_text 	= '<table>';
			$checked_out_text 	.= '<tr><td>'. $row->editor .'</td></tr>';
			$checked_out_text 	.= '<tr><td>'. $date .'</td></tr>';
			$checked_out_text 	.= '<tr><td>'. $time .'</td></tr>';
			$checked_out_text 	.= '</table>';
			//$hover = 'onmouseover="return overlib(\''. $checked_out_text .'\', CAPTION, \'Checked Out\', BELOW, RIGHT);" onmouseout="return nd();"';
			$hover = "onmouseover=\"YUI.ALIRO.COREUI.tooltip.displayAdvTooltip.call.call(this, '$checked_out_text', 'Checked Out', null, 'TL', 'TR');\"";
		}
		$checked	 		= '<img src="images/checked_out.png" '. $hover .'/>';

		return $checked;
	}

	static function ContentLegend( ) {
		?>
		<table cellspacing="0" cellpadding="4" border="0" align="center">
		<tr align="center">
			<td>
			<img src="images/publish_y.png" width="12" height="12" border="0" alt="Pending" />
			</td>
			<td>
			Published, but is <u>Pending</u> |
			</td>
			<td>
			<img src="images/publish_g.png" width="12" height="12" border="0" alt="Visible" />
			</td>
			<td>
			Published and is <u>Current</u> |
			</td>
			<td>
			<img src="images/publish_r.png" width="12" height="12" border="0" alt="Finished" />
			</td>
			<td>
			Published, but has <u>Expired</u> |
			</td>
			<td>
			<img src="images/publish_x.png" width="12" height="12" border="0" alt="Finished" />
			</td>
			<td>
			Not Published
			</td>
		</tr>
		<tr>
			<td colspan="8" align="center">
			Click on icon to toggle state.
			</td>
		</tr>
		</table>
		<?php
	}

	static function menuLinksContent( &$menus ) {
		?>
		<script type="text/javascript">
		    static function go2( pressbutton, menu, id ) {
    			var form = document.adminForm;

    			if (pressbutton === 'go2menu') {
    				form.menu.value = menu;
    				YUI.ALIRO.CORE.submitform( pressbutton );
    				return;
    			}

    			if (pressbutton === 'go2menuitem') {
    				form.menu.value 	= menu;
    				form.menuid.value 	= id;
    				YUI.ALIRO.CORE.submitform( pressbutton );
    				return;
    			}
    		}
		</script>
		<?php
		foreach( $menus as $menu ) {
			?>
			<tr>
				<td colspan="2">
				<hr />
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Menu
				</td>
				<td>
				<a href="javascript:go2( 'go2menu', '<?php echo $menu->menutype; ?>' );" title="Go to Menu">
				<?php echo $menu->menutype; ?>
				</a>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Link Name
				</td>
				<td>
				<strong>
				<a href="javascript:go2( 'go2menuitem', '<?php echo $menu->menutype; ?>', '<?php echo $menu->id; ?>' );" title="Go to Menu Item">
				<?php echo $menu->name; ?>
				</a>
				</strong>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				State
				</td>
				<td>
				<?php
				switch ( $menu->published ) {
					case -2:
						echo '<font color="red">Trashed</font>';
						break;
					case 0:
						echo 'UnPublished';
						break;
					case 1:
					default:
						echo '<font color="green">Published</font>';
						break;
				}
				?>
				</td>
			</tr>
			<?php
		}
		?>
		<input type="hidden" name="menu" value="" />
		<input type="hidden" name="menuid" value="" />
		<?php
	}

	static function menuLinksSecCat( &$menus ) {
		?>
		<script type="text/javascript">
		static function go2( pressbutton, menu, id ) {
			var form = document.adminForm;

			if (pressbutton === 'go2menu') {
				form.menu.value = menu;
				YUI.ALIRO.CORE.submitform( pressbutton );
				return;
			}

			if (pressbutton === 'go2menuitem') {
				form.menu.value 	= menu;
				form.menuid.value 	= id;
				YUI.ALIRO.CORE.submitform( pressbutton );
				return;
			}
		}
		</script>
		<?php
		foreach( $menus as $menu ) {
			?>
			<tr>
				<td colspan="2">
				<hr/>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Menu
				</td>
				<td>
				<a href="javascript:go2( 'go2menu', '<?php echo $menu->menutype; ?>' );" title="Go to Menu">
				<?php echo $menu->menutype; ?>
				</a>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Type
				</td>
				<td>
				<?php echo $menu->type; ?>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Item Name
				</td>
				<td>
				<strong>
				<a href="javascript:go2( 'go2menuitem', '<?php echo $menu->menutype; ?>', '<?php echo $menu->id; ?>' );" title="Go to Menu Item">
				<?php echo $menu->name; ?>
				</a>
				</strong>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				State
				</td>
				<td>
				<?php
				switch ( $menu->published ) {
					case -2:
						echo '<font color="red">Trashed</font>';
						break;
					case 0:
						echo 'UnPublished';
						break;
					case 1:
					default:
						echo '<font color="green">Published</font>';
						break;
				}
				?>
				</td>
			</tr>
			<?php
		}
		?>
		<input type="hidden" name="menu" value="" />
		<input type="hidden" name="menuid" value="" />
		<?php
	}

	static function AccessProcessing( &$row, $i ) {
		if ( !$row->access ) {
			$color_access = 'style="color: green;"';
			$task_access = 'accessregistered';
		} else if ( $row->access == 1 ) {
			$color_access = 'style="color: red;"';
			$task_access = 'accessspecial';
		} else {
			$color_access = 'style="color: black;"';
			$task_access = 'accesspublic';
		}

		$href = '
		<a href="javascript: void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task_access .'\')" '. $color_access .'>
		'. $row->groupname .'
		</a>'
		;

		return $href;
	}

}