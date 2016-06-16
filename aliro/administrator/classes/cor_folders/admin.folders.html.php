<?php

/** Folder component - admin side view classes
/*  Author: Martin Brampton
/*  Date: December 2006
/*  Copyright (c) Martin Brampton 2006
/*  */

class foldersAdminHTML extends widgetAdminHTML {
	protected $clist = '';

	function __construct ($controller, $clist) {
		parent::__construct($controller);
		$this->clist = $clist;
	}

	function displayIcons ($object, $iconList) {
		if (is_object($object)) $icon = $object->icon;
		else $icon = '';
		?>
		<script type="text/javascript">
		function paste_strinL(strinL){
			var input=document.forms["adminForm"].elements["icon"];
			input.value='';
			input.value=strinL;
		}
		</script>
		<tr>
			<td width="30%" valign="top" align="right">
				<b><?php echo T_('Icon'); ?></b>&nbsp;
			</td>
			<td valign="top">
				<input class="inputbox" type="text" name="icon" size="25" value="<?php echo $icon; ?>" />
				<table>
					<tr>
						<td>
							<?php echo $iconList; ?>
						</td>
					</tr>
				</table>
			</td>
  		</tr>
  		<?php
	}

	function listHeader ($descendants, $search) {
		?>
		<tr>
    		<td align="left"><?php echo T_('Display number').$this->pageNav->writeLimitBox(); ?>
			</td>
			<td align="left"><?php echo T_('Search:'); ?><input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onchange="document.adminForm.submit();" />
    		</td>
			<td align="left"><?php echo T_('Show Descendants?'); ?><input type="checkbox" name="descendants" value="1" <?php if ($descendants) echo 'checked="checked"'; ?> onchange="document.adminForm.submit();" />
			</td>
		</tr>
		<tr>
		<?php
		if ($this->clist<>'') {
			echo '<td align="left" colspan="3">'.$this->clist.'</td>';
		}
		echo '</tr>';
	}

	function folderSelectBox () {
		?>
		<tr>
			<td width="30%" valign="top" align="right">
				<b><?php echo T_('Parent folder'); ?></b>&nbsp;
			</td>
			<td valign="top">
				<?php echo $this->clist; ?>
			</td>
		</tr>
		<?php
	}

	function startEditHeader ($title) {
		?>
		<form method="post" name="adminForm" action="index.php">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
   		<tr>
			<td width="100%" colspan="4">
			<div class="title">
			<img src="<?php echo aliroCore::get('mosConfig_live_site').'/administrator/images/asterisk.png'; ?>" alt="<?php echo $title; ?>" />
			<span class="sectionname">&nbsp;<?php echo T_('Mambo '); echo $title; ?></span>
			</div>
			</td>
    	</tr>
		<?php
		$this->blankRow();
		$this->folderSelectBox();
	}

	function publishedBox (&$object) {
		?>
				<tr>
					<td width="30%" align="right">
				  	<b><?php echo T_('Published'); ?></b>&nbsp;
				  </td>
				  	<?php $this->tickBox($object, 'published'); ?>
				</tr>
		<?php
	}

	function editLink ($id, $folderid=0) {
		$url = "index.php?core=cor_folders&amp;act=$this->act&amp;task=edit&amp;cid=$id";
		if ($folderid) $url .= "&amp;folderid=$folderid";
		return $url;
	}

	function legalTypeList ($current) {
		$alternatives = explode(',',_REMOS_LEGAL_TYPES);
		foreach ($alternatives as $one) {
			if ($one == $current) $mark = 'selected=\'selected\'';
			else $mark = '';
			echo "<option $mark value='$one'>$one</option>";
		}
	}

}

class listFoldersHTML extends foldersAdminHTML {

	function __construct ($controller, $clist) {
		parent::__construct($controller, $clist);
	}

	function columnHeads ($folders, $descendants) {
		$this->listHeadingStart(count($folders));
		$this->headingItem('30%', T_('Title'));
		if ($this->clist) {
			$this->headingItem('5%', 'ID');
			if (!$descendants) {
				$this->headingItem('5%', T_('Reorder'), 2);
				$this->headingItem('2%', T_('Order'));
				?>
				<th width="1%">
				<a href="javascript: saveorder( <?php echo count( $folders )-1; ?> )"><img src="images/filesave.png" border="0" width="16" height="16" alt="<?php echo T_('Save Order') ?>" /></a>
				</th>
				<?php
			}
			$this->headingItem('25%', T_('Top level folder'));
			$this->headingItem('25%', T_('Immediate folder'));
		}
		$this->headingItem('7%', T_('Published'));
		echo '</tr>';
	}

	function filecount ($folder) {
		if ($folder->filecount) {
			$link = "<a href='index.php?core=cor_folders&amp;act=files&amp;task=list&amp;folderid=$folder->id'>";
			$link .= $folder->filecount;
			$link .= '</a>';
			return $link;
		}
		else return '0';
	}

	function listLine ($folder, $descendants, $i, $k, $n, $basicurl) {
		global $mosConfig_live_site;
		?>
				<tr class="<?php echo "row$k"; ?>">
					<td width="5">
						<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $folder->id; ?>" onclick="isChecked(this.checked);" />
					</td>
					<td width="30%" align="left">
							<a href="<?php echo $this->editLink($folder->id); ?>">
							<?php echo $folder->name; ?>
						</a>
					</td>
					<?php if ($this->clist) { ?>
					<td width="5%" align="left"><?php echo $folder->id; ?></td>
					<?php if (!$descendants) { echo '<td>'.$this->pageNav->noJavaOrderUpIcon( $i, $folder->upok, $basicurl."&amp;task=orderup&amp;id=$folder->id" ); ?>
					</td>
					<td>
					<?php echo $this->pageNav->noJavaOrderDownIcon( $i, $n, $folder->downok, $basicurl."&amp;task=orderdown&amp;id=$folder->id" ); ?>
					</td>
					<td align="center" colspan="2">
					<input type="text" name="order[<?php echo $folder->id; ?>]" size="5" value="<?php echo $folder->ordering; ?>" class="text_area" style="text-align: center" />
					<?php echo '</td>'; } ?>
					<td width="25%" align="left"><?php echo $folder->getCategoryName();?></td>
					<td width="25%" align="left"><?php echo $folder->getFamilyNames();?></td>
					<?php }
					if ($folder->published==1) { ?>
					<td width="7%" align="center"><img src="<?php echo $mosConfig_live_site; ?>/administrator/images/publish_g.png" border="0" alt="Published" />
					</td>
					<?php } else { ?>
					<td width="7%" align="center"><img src="<?php echo $mosConfig_live_site; ?>/administrator/images/publish_x.png" border="0" alt="Published" />
					</td>
					<?php } ?>
				</tr>
		<?php
	}

	function view (&$folders, $descendants, $basicurl, $search='')  {
		$this->formStart(T_('Folders'), aliroCore::get('mosConfig_live_site').'/administrator/images/asterisk.png');
		$this->blankRow();
		$this->listHeader($descendants, $search);
		echo '</table>';
		$this->columnHeads($folders, $descendants);
		$n = count($folders);
		$k = 0;
		foreach ($folders as $i=>$folder) {
			$this->listLine($folder, $descendants, $i, $k, $n, $basicurl);
			$k = 1 - $k;
		}
		$this->listFormEnd();
	}
}

class editFoldersHTML extends foldersAdminHTML {

	function __construct ($controller, $clist) {
		parent::__construct($controller, $clist);
	}

	function selectList ($title, $selector, $redstar) {
		$this->inputTop ($title, $redstar);
		?>
			<td valign="top">
				<?php echo $selector; ?>
			</td>
		</tr>
		<?php
	}

	function permission ($title,$folder,$updown,$name) {
		$this->inputTop($title, true);
		?>
					<td valign="top">
					<?php
					for ($i=0; $i<4; $i++) {
						echo '<input type="radio" name="'.$name.'" value="'.$i;
						if ($folder->$name == $i) echo '" checked="checked" />';
						else echo '" />';
						echo $updown[$i];
					}
					?>
				    </td>
			</tr>
		<?php
	}

	function groupOptions ($object, $property) {
		?>
		<td valign="top">
			<select NAME="<?php echo $property; ?>" class="inputbox">
				<option value="0"><?php echo _GLOBAL; ?></option>
				<option value="1" <?php if ($object->$property) echo 'selected="selected"'; echo '>'._YES; ?></option>
			</select>
		</td>
		<?php
	}

	function view (&$folder)
	{
		$iconList = aliroFolder::getIcons ();
		$this->commonScripts('description');
		echo '<br/>';
		$this->startEditHeader(T_('Edit Folder details'));
		$this->publishedBox($folder);
		$this->fileInputBox(T_('Folder name'), 'name', $folder->name, 50);
		$this->fileInputArea(T_('Description'), T_('Up to 500 characters'), 'description', $folder->description, 50, 100, true);
		$this->fileInputBox(T_('Keywords'),'keywords',$folder->keywords,50);
		$this->fileInputBox(T_('Window title'),'windowtitle',$folder->windowtitle,50);
		$this->displayIcons($folder, $iconList);
		$this->editFormEnd ($folder->id);
	}
}

?>