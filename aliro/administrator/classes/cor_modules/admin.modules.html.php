<?php
/**
* Aliro Box Manager HTML
*/

class HTML_modules extends basicAdminHTML {

	// Required because gettext does not find T_('abc') inside heredoc
	public function __construct ($controller) {
		parent::__construct($controller);
		$this->translations['Box Manager'] = T_('Box Manager');
		$this->translations['Filter:'] = T_('Filter:');
		$this->translations['Module Name'] = T_('Module Name');
		$this->translations['Published'] = T_('Published');
		$this->translations['Reorder'] = T_('Reorder');
		$this->translations['Order'] = T_('Order');
		$this->translations['Position'] = T_('Position');
		$this->translations['Pages'] = T_('Pages');
		$this->translations['ID'] = T_('ID');
		$this->translations['Type'] = T_('Type');
		$this->translations['Select custom module, or module type'] = T_('Select custom module, or module type');
		$this->translations['Module must have a title'] = T_('Module must have a title');
		$this->translations['Module:'] = T_('Module:');
		$this->translations['Details'] = T_('Details');
		$this->translations['Title:'] = T_('Title');
		$this->translations['Show title:'] = T_('Show title:');
		$this->translations['Position:'] = T_('Position:');
		$this->translations['Restrict to roles:'] = T_('Restrict to roles:');
		$this->translations['Published:'] = T_('Published:');
		$this->translations['ID:'] = T_('ID:');
		$this->translations['Description:'] = T_('Description:');
		$this->translations['Parameters'] = T_('Parameters');
		$this->translations['Pages / Items'] = T_('Pages / Items');
		$this->translations['Menu Item Link(s):'] = T_('Menu Item Link(s):');
	}
	/**
	* Writes a list of the defined modules
	* @param array An array of category objects
	*/
	public function showModules (&$rows, $myid, $client, &$lists, $search, $basicurl) {

		$this->requestOverlib();

		$rowcount = count($rows);
		$detail_lines = '';
		$k = 0;
		foreach ($rows as $i=>$row) {
			$prev_position = isset($rows[$i-1]) ? $rows[$i-1]->position : null;
			$next_position = isset($rows[$i+1]) ? $rows[$i+1]->position : null;

			$link = "$this->optionurl&amp;client=$client&amp;task=editA&amp;hidemainmenu=1&amp;id=$row->id";

			$title = ($row->checked_out AND ( $row->checked_out != $myid )) ? $row->title : "<a href=\"$link\">$row->title</a>";

			$module = $row->module ? $row->module : T_("User");

			$detail_lines .= <<<DETAIL_LINE
			<tr class="row$k">
				<td align="right">
					{$this->pageNav->rowNumber($i)}
				</td>
				<td>
					{$this->html('checkedOutProcessing', $row, $i)}
				</td>
				<td>
					$title
				</td>
				<td align="center">
					{$this->html('publishedProcessing', $row, $i)}
				</td>
				<td>
					{$this->pageNav->noJavaOrderUpIcon( $i, ($row->position === $prev_position), $basicurl."&amp;task=orderup&amp;id=$row->id" )}
				</td>
				<td>
					{$this->pageNav->noJavaOrderDownIcon( $i, $rowcount, ($row->position === $next_position), $basicurl."&amp;task=orderdown&amp;id=$row->id" )}
				</td>
				<td align="center">
					<input type="text" name="order[$row->id]" size="5" value="$row->ordering" class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					$row->position
				</td>
				<td align="center">
					$row->pages
				</td>
				<td align="center">
					$row->id
				</td>
				<td align="left">
					$module
				</td>
			</tr>
DETAIL_LINE;

			$k = 1 - $k;
		}

		$subtitle = $client == 'admin' ? T_('Administrator') : T_('Site');

		$html = <<<MODULE_LIST
		<table class="adminheading">
		<tr>
			<th class="modules" rowspan="2">
			{$this->T_('Box Manager')} <small><small>[ $subtitle ]</small></small>
			</th>
			<td width="right">
				{$lists['position']}
			</td>
			<td width="right">
				{$lists['type']}
			</td>
		</tr>
		<tr>
			<td align="right">
				{$this->T_('Filter:')}
			</td>
			<td>
			<input type="text" name="search" value="$search" class="text_area" onchange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<thead>
		<tr>
			<th width="20px">#</th>
			<th width="20px">
			<input type="checkbox" name="toggle" value="" onclick="checkAll($rowcount);" />
			</th>
			<th class="title">
				{$this->T_('Module Name')}
			</th>
			<th nowrap="nowrap" width="10%">
				{$this->T_('Published')}
			</th>
			<th colspan="2" align="center" width="5%">
				{$this->T_('Reorder')}
			</th>
			<th width="2%">
				{$this->T_('Order')}
			</th>
			<th nowrap="nowrap" width="7%">
				{$this->T_('Position')}
			</th>
			<th nowrap="nowrap" width="5%">
				{$this->T_('Pages')}
			</th>
			<th nowrap="nowrap" width="5%">
				{$this->T_('ID')}
			</th>
			<th nowrap="nowrap" width="10%" align="left">
				{$this->T_('Type')}
			</th>
		</tr>
		</thead>
		<tbody>
			$detail_lines
		</tbody>
		</table>

		{$this->pageNav->getListFooter()}

		<div>
			$this->optionline
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="client" value="$client" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
		</div>

MODULE_LIST;

		echo $html;
	}

	public function showNew ($options) {
		$html = <<<NEW_HTML

		<div class="adminheadbar">{$this->T_('Select custom module, or module type')}</div>
		<p>{$this->html('selectList', $options, 'codebase', '', 'value', 'text')}</p>
		$this->optionline
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="1" />

NEW_HTML;
		echo $html;
	}

	/**
	* Writes the edit form for new and existing module
	*
	*/
	public function editModule( $row, $lists, $params, $option ) {

		$row->titleA = '';
		if ( $row->id ) {
			$row->titleA = '<small><small>[ '. $row->title .' ]</small></small>';
		}

		$this->requestOverlib();
		$editortext = ($row->module == "") ? aliroEditor::getInstance()->getEditorContents( 'editor1', 'content' ) : '';
		// The following code, to the end of the comment, was located in the script just before the vars
		$i = 0;
		/*
		foreach ($orders2 as $k=>$items) {
			foreach ($items as $v) {
				echo "\n	orders[".$i++."] = new Array( \"$k\",\"$v->value\",\"$v->text\" );";
			}
		}
		*/

		echo <<<EDIT_SCRIPT

<!--//--><![CDATA[//><!--
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			if ( ( pressbutton == 'save' ) && ( document.adminForm.title.value == "" ) ) {
				alert("{$this->T_('Module must have a title')}");
			} else {
				$editortext
				submitform(pressbutton);
			}
			submitform(pressbutton);
		}
		<!--
		var originalOrder = '$row->ordering';
		var originalPos = '$row->position';
		var orders = new Array();	// array in the format [key,value,text]
//--><!]]></script>

EDIT_SCRIPT;

		$title = $lists['client_id'] ? T_('Administrator') : T_('Site');
		$oldnew = $row->id ? T_('Edit') : T_('New');

		echo <<<EDIT_HTML
		<table class="adminheading">
		<tr>
			<th class="modules">
				$title
				{$this->T_('Module:')}
			<small>
				$oldnew
			</small>
				$row->titleA
			</th>
		</tr>
		</table>

		<table cellspacing="0" cellpadding="0" width="100%">
		<tr valign="top">
			<td width="60%">
				<table class="adminform">
				<tr>
					<th colspan="2">
						{$this->T_('Details')}
					</th>
				</tr>
				<tr>
					<td width="100" align="left">
						{$this->T_('Title:')}
					</td>
					<td>
						<input class="text_area" type="text" name="title" size="35" value="$row->title" />
					</td>
				</tr>
				<!-- START selectable pages -->
				<tr>
					<td width="100" align="left">
						{$this->T_('Show title:')}
					</td>
					<td>
						{$lists['showtitle']}
					</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						{$this->T_('Position:')}
					</td>
					<td>
						{$lists['position']}
					</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						{$this->T_('Restrict to roles:')}
					</td>
					<td>
						{$lists['access']}
					</td>
				</tr>
				<tr>
					<td valign="top">
						{$this->T_('Published:')}
					</td>
					<td>
						{$lists['published']}
					</td>
				</tr>
				<tr>
					<td colspan="2">
					</td>
				</tr>
				<tr>
					<td valign="top">
						{$this->T_('ID:')}
					</td>
					<td>
						$row->id
					</td>
				</tr>
				<tr>
					<td valign="top">
						{$this->T_('Description:')}
					</td>
					<td>
						$row->description
					</td>
				</tr>
				</table>

				<table class="adminform">
				<tr>
					<th >
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
			<td width="40%" >
				<table width="100%" class="adminform">
				<tr>
					<th>
						{$this->T_('Pages / Items')}
					</th>
				</tr>
				<tr>
					<td>
						{$this->T_('Menu Item Link(s):')}
					<br />
						{$lists['selections']}
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<div>
			$this->optionline
			<input type="hidden" name="id" value="$row->id" />
			<input type="hidden" name="original" value="$row->ordering" />
			<input type="hidden" name="module" value="$row->module" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="client_id" value="{$lists['client_id']}" />
		</div>

EDIT_HTML;

		if ( ($row->admin & 2) OR $lists['client_id'] ) {
			echo '<input type="hidden" name="client" value="admin" />';
		}
	}

}