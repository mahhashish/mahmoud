<?php

/**
* Aliro Menu Manager HTML
*/

class listMenusHTML extends basicAdminHTML {

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
			<input type="checkbox" id="toggle" name="toggle" value="" />
			</th>
			<th width="5%" class="title">
				{$this->T_('ID')}
			</th>
			<th width="40%" class="title">
				{$this->T_('Name')}
			</th>
			<th align="left">
				{$this->T_('Home')}
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
					$row->id
				</td>
				<td>
					$prefix $title
				</td>
				<td>
					{$this->imageIfHome($row)}
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
		<input type="hidden" id="task" name="task" value="" />
		$hiddenmenutype
		$this->optionline
		<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
		<input type="hidden" id="hidemainmenu" name="hidemainmenu" value="0" />

END_OF_FINAL_HTML;

		echo $html;

	}

	private function imageIfHome ($row) {
		if ($row->home) return <<<STAR_IMAGE
		
		<img src="{$this->getCfg('admin_site')}/images/star.gif" height="16" width="16" alt="{$this->T_('Home Page Mark')}" />
		
STAR_IMAGE;

	}

	public function selectorMenu ($legend, $components) {
		$urladvice = T_('For a URI menu link on THIS site, omit the actual site link,
					for example write index.php?option=com_example&task=edit.
					Do not use the SEF version for a link on this site, only the basic one, similar to the example.
					For all other sites, include the full URI, for example http://somedomain.com/somewhere');
		$html = <<<START_HTML

		<table class="adminheading">
			<tr>
				<th>
				{$this->show(T_('Add Menu Item :: ').$legend)}
				</th>
			</tr>
		</table>
		<table class="adminform">
			<tr>
				<td width="15%"></td>
				<td>
					<input type="radio" name="menuselect" value="-1" />
					{$this->T_('Null entry')}

				</td>
			</tr>
			<tr>
				<td width="15%"></td>
				<td>
					<input type="radio" name="menuselect" value="0" checked="checked" />URI
					<input type="text" name="menuurl" size="50" /><br />
					$urladvice

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

			<tr><td>
				<input type="hidden" id="task" name="task" value="" />
				$this->optionline
				<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
				<input type="hidden" id="hidemainmenu" name="hidemainmenu" value="0" />
			</td></tr>
		</table>

END_SELECTOR_2;

		echo $html;
	}

	public function edit ($menu, $componentname, $roles, $params) {
        aliroRequest::getInstance()->requestTooltip();
		$title = sprintf(T_('Edit Menu Item :: Component <small><small>%s</small></small>'), $componentname);
		$script = <<<SCRIPT_1
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton === 'cancel') {
				YUI.ALIRO.CORE.submitform( pressbutton );
				return;
			}
			if ( YUI.ALIRO.CORE.trim( form.name.value ) === "" ){
				alert( "{$this->T_('Item must have a name')}" );
			} else {
				YUI.ALIRO.CORE.submitform( pressbutton );
			}
		}
		</script>
SCRIPT_1;

		$text_component = ('component' == $menu->type) ? T_('Component:') : T_('User specified -');
		$value_name = $menu->name;
		$value_parent = aliroSelectors::getInstance()->allMenuParent ($menu);
		if ('component' == $menu->type) $input_url = $menu->link;
		else $input_url = <<<URL_HTML
		<input class="inputbox" type="text" name="link" size="50" value="$menu->link" />
URL_HTML;

		$html = <<<EDIT_HTML

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
					<td width="10%" align="right">{$this->T_('URI:')}</td>
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
					<td valign="top" align="right">{$this->T_('Grant to new role:')}</td>
					<td>
						<input name="newrole" class="inputbox" type="text" size="50" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">{$this->T_('Published:')}</td>
					<td>
						<input type="radio" name="published" value="1" checked="checked" />{$this->T_('Yes')}
						<input type="radio" name="published" value="0" />{$this->T_('No')}
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

		<div>
			$this->optionline
			<input type="hidden" name="id" value="$menu->id" />
			<input type="hidden" name="menutype" value="$menu->menutype" />
			<input type="hidden" name="type" value="$menu->type" />
			<input type="hidden" id="task" name="task" value="" />
			<input type="hidden" id="hidemainmenu" name="hidemainmenu" value="0" />
		</div>

EDIT_HTML;

		echo $script;
		echo $html;
	}

	public function outputForm ($corehtml) {
		$html = <<<FORM_HTML

		<form action="index.php" method="post" id="adminForm" name="adminForm">
		$corehtml
		<input type="hidden" id="task" name="task" value="" />
		$this->optionline
		<input type="hidden" id="hidemainmenu" name="hidemainmenu" value="0" />
		</form>

FORM_HTML;

		echo $html;
	}

}