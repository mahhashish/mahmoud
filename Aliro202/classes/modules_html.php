<?php

// This is being kept only for reference.  Custom modules are now implemented
// as a specific module.  The same treatment needs to be given to RSS.

/**
* @version $Id: frontend.html.php,v 1.3 2005/07/22 03:36:09 eddieajau Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
*/
class modules_html {

	function module( &$module, &$params, $Itemid, $style=0 ) {
		global $mosConfig_live_site, $mosConfig_sitename, $mosConfig_lang;
		$mosConfig_absolute_path = aliroCore::get('mosConfig_absolute_path');
		// custom module params
		$rssurl 			= $params->get( 'rssurl' );
		$rssitems 			= $params->get( 'rssitems', 5 );
		$rssdesc 			= $params->get( 'rssdesc', 1 );
		$rssimage 			= $params->get( 'rssimage', 1 );
		$rssitemdesc		= $params->get( 'rssitemdesc', 1 );
		$moduleclass_sfx 	= $params->get( 'moduleclass_sfx' );
		$words 				= $params->def( 'word_count', 0 );

		if ($style == -1 && !$rssurl) {
			echo $module->content;
			return;
		} else {
			?>
			<table cellpadding="0" cellspacing="0" class="moduletable<?php echo $moduleclass_sfx; ?>">
			<?php
			if ( $module->showtitle != 0 ) {
				?>
				<tr>
					<th valign="top">
					<?php echo $module->title; ?>
					</th>
				</tr>
				<?php
			}

			if ( $module->content ) {
				?>
				<tr>
					<td>
					<?php echo $module->content; ?>
					</td>
				</tr>
				<?php
			}
		}
		// feed output
		if ( $rssurl ) {
			if (!defined('MAGPIE_CACHE_DIR')) define ('MAGPIE_CACHE_DIR', aliroCore::get('mosConfig_absolute_path').'/includes/magpie_cache');
			require_once (aliroCore::get('mosConfig_absolute_path').'/includes/magpierss/rss_fetch.php');
			$rss = fetch_rss($rssurl);
			if (isset($rss->image['title'])) $iTitle = $rss->image['title'];
			if (isset($rss->image['url'])) $iUrl = $rss->image['url'];
			// feed title
			?>
			<tr>
				<td>
				<strong>
				<a href="<?php echo $rss->channel['link']; ?>" target="_blank">
				<?php echo $rss->channel['title']; ?>
				</a>
				</strong>
				</td>
			</tr>
			<?php
			// feed description
			if ( $rssdesc ) {
				?>
				<tr>
					<td>
					<?php echo $rss->channel['description']; ?>
					</td>
				</tr>
				<?php
			}
			// feed image
			if ( $rssimage AND isset($iUrl) ) {
				?>
				<tr>
					<td align="center">
					<image src="<?php echo $iUrl; ?>" alt="<?php echo $iTitle; ?>"/>
					</td>
				</tr>
				<?php
			}
			$itemnumber = 1;
			?>
			<tr>
				<td>
				<ul class="newsfeed<?php echo $moduleclass_sfx; ?>">
			<?php
			foreach ($rss->items as $item) {
				if ($itemnumber > $rssitems) break;
				$itemnumber++;
				// item title
				?>
				<li class="newsfeed<?php echo $moduleclass_sfx; ?>">
				<strong>
				<a href="<?php echo $item['link']; ?>" target="_blank">
				<?php echo $item['title']; ?>
				</a>
				</strong>
				<?php
				// item description
				if ( $rssitemdesc ) {
					// item description
					$text = html_entity_decode( $item['description'] );
						// word limit check
					if ( $words ) {
						$texts = explode( ' ', $text );
						$count = count( $texts );
						if ( $count > $words ) {
							$text = '';
							for( $i=0; $i < $words; $i++ ) {
								$text .= ' '. $texts[$i];
							}
							$text .= '...';
						}
					}
					?>
					<div>
					<?php echo $text; ?>
					</div>
					<?php
				}
				?>
				</li>
				<?php
			}
			?>
			</ul>
			</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}

}