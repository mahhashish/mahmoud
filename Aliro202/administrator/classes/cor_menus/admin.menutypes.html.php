<?php

/**
* Aliro Menu Manager HTML
*/

class listMenutypesHTML extends basicAdminHTML {

	public function view ($menutypes) {

		$this->requestTooltip();
		
		$rowcount = count($menutypes);
		$this->html('toggleManyScript', $rowcount);
		$typelist = '';
		$k = 0;
		foreach ($menutypes as $i=>$type) {
			$editlink = 'index.php?core=cor_menus&act=type&task=edit&cid='.$type->id;
			$typelist .= <<<ONE_TYPE
		
			<tr class="row$k">
				<td>
				{$this->html('idBox', $i, $type->id)}
				</td>
				<td>
					$type->id
				</td>
				<td>
					<a href="$editlink">
						$type->type
					</a>
				</td>
				<td>
					$type->name
				</td>
				<td>
					$type->ordering
				</td>
			</tr>
		
ONE_TYPE;

			$k = 1 - $k;
		}
		
		echo <<<END_OF_HEADER_HTML

		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
				{$this->T_('Menu Type Manager')}
			</th>
		</tr>
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
			<th width="40%" align="left" class="title">
				{$this->T_('Type')}
			</th>
			<th width="40%" align="left" class="title">
				{$this->T_('Name')}
			</th>
			<th>
				{$this->T_('Order')}
			</th>
		</tr>
		</thead>
		<tbody>
			$typelist
		</tbody>
		</table>
		<input type="hidden" name="act" value="type" />
		<input type="hidden" id="task" name="task" value="" />
		$this->optionline
		<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
		<input type="hidden" id="hidemainmenu" name="hidemainmenu" value="0" />
		
END_OF_HEADER_HTML;

	}
	
	public function edit ($menutype) {
		$idname = $menutype->id ? $menutype->id : T_('New');
		echo <<<EDIT_HTML
		
			<table class="adminheading">
				<tr>
					<th class="user">
						{$this->T_('Menu Type Manager')} [$idname]
					</th>
				</tr>
			</table>

			<table class="adminform">
				<tr>
					<td width="10%" align="right">{$this->T_('Type:')}</td>
					<td width="80%">
					<input class="inputbox" type="text" name="type" size="50" maxlength="25" value="$menutype->type" />
					</td>
				</tr>
				<tr>
					<td width="10%" align="right">{$this->T_('Name:')}</td>
					<td width="80%">
					<input class="inputbox" type="text" name="name" size="50" maxlength="255" value="$menutype->name" />
					</td>
				</tr>
				<tr>
					<td width="10%" align="right">{$this->T_('Ordering:')}</td>
					<td width="80%">
					<input class="inputbox" type="text" name="ordering" size="50" maxlength="10" value="$menutype->ordering" />
					</td>
				</tr>
			</table>

			<div>
				$this->optionline
				<input type="hidden" name="id" value="$menutype->id" />
				<input type="hidden" name="act" value="type" />
				<input type="hidden" id="task" name="task" value="" />
				<input type="hidden" id="hidemainmenu" name="hidemainmenu" value="0" />
			</div>

EDIT_HTML;

	}

}