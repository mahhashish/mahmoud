<?php

/**
* Aliro Menu Manager HTML
*/

class listMenusHTML extends basicAdminHTML {

	// Required because gettext does not find T_('abc') inside heredoc
	public function __construct ($controller) {
		parent::__construct($controller);
		$this->translations['New menu name: '] = T_('New menu name: ');
		$this->translations['Menu Manager'] = T_('Menu Manager');
		$this->translations['Name'] = T_('Name');
		$this->translations['Level'] = T_('Level');
		$this->translations['Reorder'] = T_('Reorder');
		$this->translations['Order'] = T_('Order');
		$this->translations['Type'] = T_('Type');
		$this->translations['ID'] = T_('ID');
		$this->translations['Published'] = T_('Published');
		$this->translations['Item must have a name'] = T_('Item must have a name');
		$this->translations['Details'] = T_('Details');
		$this->translations['Part of menu:'] = T_('Part of menu:');
		$this->translations['Name:'] = T_('Name:');
		$this->translations['Url:'] = T_('Url:');
		$this->translations['Parent Item:'] = T_('Parent Item:');
		$this->translations['Restrict to roles:'] = T_('Restrict to roles:');
		$this->translations['Published:'] = T_('Published:');
		$this->translations['Yes'] = T_('Yes');
		$this->translations['No'] = T_('No');
		$this->translations['Parameters'] = T_('Parameters');
		// Note that this message is only represented in greatly abbreviated form in the heredoc
		$this->translations['URL menu link'] = T_('For a URL menu link on THIS site, omit the actual site link,
					for example write index.php?option=com_example&task=edit.
					Do not use the SEF version for a link on this site, only the basic one, similar to the example.
					For all other sites, include the full URL, for example http://somedomain.com/somewhere');
	}
	
	public function view ($rows, $menutype, $myid, $basicurl) {

		$rowcount = count($rows);
		if ($menutype) {
			$newmenutype = '';
			$hiddenmenutype = "<input type='hidden' name='menutype' value='$menutype' />";
		}
		else {
			$newmenutype = <<<END_NEW_MENU
			<tr>
				<td>
					<label for='newmenuname'>{$this->T_('New menu name: ')}</label>
					<input id='newmenuname' type='text' name='menutype' size='40' /
				</td>
			</tr>
END_NEW_MENU;

			$hiddenmenutype = '';
		}

		$html = <<<END_OF_HEADER_HTML

		<form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
			{$this->T_('Menu Manager')} [$menutype]
			</th>
		</tr>
		$newmenutype
		</thead>
		<tbody><tr><td></td></tr></tbody>
		</table>

		<table class="adminlist">
		<thead>
		<tr>
			<th width="3%" class="title">
			<input type="checkbox" name="toggle" value="" onclick="checkAll($rowcount);" />
			</th>
			<th width="40%" class="title">
				{$this->T_('Name')}
			</th>
			<th align="left">
				{$this->T_('Level')}
			</th>
			<th colspan="2" align="center" width="5%">
				{$this->T_('Reorder')}
			</th>
			<th>
				{$this->T_('Order')}
			</th>
			<th align="left">
				{$this->T_('Type')}
			</th>
			<th align="left">
				{$this->T_('ID')}
			</th>
			<th align="left">
				{$this->T_('Published')}
			</th>
		</tr>
		</thead>
		<tbody>
END_OF_HEADER_HTML;

		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$link = "index.php?core=cor_menus&amp;task=edit&amp;id=$row->id";
			$title = ($row->checked_out AND ( $row->checked_out != $myid )) ? $row->name : "<a href=\"$link\">$row->name</a>";
			// $checked = aliroHTML::getInstance()->checkedOutProcessing( $row, $i );
			$prefix = '';
			for ($j=0; $j<$row->level; $j++) $prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;';

			$html .= <<<END_OF_BODY_HTML

			<tr class="row$k">
				<td>
				{$this->html('idBox', $i, $row->id)}
				</td>
				<td>
					$prefix $title
				</td>
				<td>
					$row->level
				</td>
				<td>
					{$this->pageNav->noJavaOrderUpIcon( $i, $row->upok, $basicurl."&amp;task=orderup&amp;id=$row->id" )}
				</td>
				<td>
					{$this->pageNav->noJavaOrderDownIcon( $i, $n, $row->downok, $basicurl."&amp;task=orderdown&amp;id=$row->id" )}
				</td>
				<td align="center">
					<input type="text" name="order[$row->id]" size="5" value="$row->ordering" class="text_area" style="text-align: center" />
				</td>
				<td>
					$row->type
				</td>
				<td>
					$row->id
				</td>
				<td>
					{$this->html('publishedProcessing', $row, $i )}
				</td>
			</tr>
END_OF_BODY_HTML;

			$k = 1 - $k;
		}
		$pagenavtext = $this->pageNav->getListFooter();

		$html .= <<<END_OF_FINAL_HTML

		</tbody>
		</table>
		$pagenavtext
		<input type="hidden" name="task" value="" />
		$hiddenmenutype
		$this->optionline
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
END_OF_FINAL_HTML;

		echo $html;

	}


	public function selectorMenu ($legend, $components) {
		$html = <<<START_HTML

		<form action="index.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>
				{$this->show(T_('Add Menu Item :: ').$legend)}
				</th>
			</tr>
		</table>
		<table class="adminform">
			<thead>
				<tr>
					<th colspan="2">
						Details
					</th>
				</tr>
			</thead>
			<tbody>
			<tr>
				<td width="15%"></td>
				<td>
					<input type="radio" name="menuselect" value="0" checked="checked" />URL
					<input type="text" name="menuurl" size="50" /><br />
					{$this->T_('URL menu link')}

				</td>
			</tr>

START_HTML;

		foreach ($components as $component) {
			$html .= <<<NEW_MENU_CHOICES

			<tr>
				<td width="15%"> </td><td><input type="radio" name="menuselect" value="$component->id" />$component->name</td>
			</tr>

NEW_MENU_CHOICES;

		}

		$html .= <<<END_SELECTOR_2

		</tbody>
			<input type="hidden" name="task" value="" />
			$this->optionline
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="1" />
		</table>
		</form>

END_SELECTOR_2;

		echo $html;
	}

	public function edit ($menu, $componentname, $roles, $params) {

		$title = sprintf(T_('Edit Menu Item :: Component <small><small>%s</small></small>'), $componentname);
		$script = <<<SCRIPT_1
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if ( trim( form.name.value ) == "" ){
				alert( "{$this->T_('Item must have a name')}" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
SCRIPT_1;

		$text_component = ('component' == $menu->type) ? T_('Component:') : T_('User specified -');
		$value_name = $menu->name;
		$value_parent = aliroSelectors::getInstance()->menuParent ($menu);
		if ('component' == $menu->type) $input_url = $menu->link;
		else $input_url = <<<URL_HTML
		<input class="inputbox" type="text" name="link" size="50" value="$menu->link" />
URL_HTML;

		$html = <<<EDIT_HTML

		<form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th>
				$title
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr valign="top">
			<td width="60%">
				<table class="adminform">
				<tr>
					<th colspan="2">
						{$this->T_('Details')}
					</th>
				</tr>
				<tr>
					<td width="10%" align="right">{$this->T_('Part of menu:')}</td>
					<td width="80%">
						$menu->menutype
					</td>
				</tr>
				<tr>
					<td width="10%" align="right">{$this->T_('Name:')}</td>
					<td width="80%">
					<input class="inputbox" type="text" name="name" size="50" maxlength="100" value="$value_name" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">$text_component</td>
					<td>
						$componentname
					</td>
				</tr>
				<tr>
					<td width="10%" align="right">{$this->T_('Url:')}</td>
					<td width="80%">
						$input_url
					</td>
				</tr>
				<tr>
					<td align="right">{$this->T_('Parent Item:')}</td>
					<td>
						$value_parent
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">{$this->T_('Restrict to roles:')}</td>
					<td>
						$roles
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">{$this->T_('Published:')}</td>
					<td>
						<input type='radio' name='published' value='1' checked='checked' />{$this->T_('Yes')}
						<input type='radio' name='published' value='0' />{$this->T_('No')}
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>
			</td>
			<td width="40%">
				<table class="adminform">
				<tr>
					<th>
						{$this->T_('Parameters')}
					</th>
				</tr>
				<tr>
					<td>
						{$params->render()}
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		$this->optionline;
		<input type="hidden" name="id" value="$menu->id" />
		<input type="hidden" name="menutype" value="$menu->menutype" />
		<input type="hidden" name="type" value="$menu->type" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="1" />
		</form>
		<script type="text/javascript" src="{$this->getCfg('live_site')}/includes/js/overlib_mini.js"></script>
EDIT_HTML;

		echo $script;
		echo $html;
	}

	public function outputForm ($corehtml) {
		$html = <<<FORM_HTML

		<form action="index.php" method="post" name="adminForm">
		$corehtml
		<input type="hidden" name="task" value="" />
		$this->optionline
		<input type="hidden" name="hidemainmenu" value="1" />
		</form>

FORM_HTML;

		echo $html;
	}

}

?>