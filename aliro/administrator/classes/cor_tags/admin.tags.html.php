<?php

/** Tags component - admin side view classes
/*  Author: Martin Brampton
/*  Date: December 2007
/*  Copyright (c) Martin Brampton 2007
/*  */

class tagsAdminHTML extends basicAdminHTML {
	protected $clist = '';

	public function __construct ($controller, $clist) {
		parent::__construct($controller);
		$this->clist = $clist;
		$this->translations['Type'] = T_('Type');
		$this->translations['Name'] = T_('Name');
		$this->translations['Frequency'] = T_('Frequency');
		$this->translations['Published'] = T_('Published');
		$this->translations['Hidden'] = T_('Hidden');
		$this->translations['Yes'] = T_('Yes');
		$this->translations['No'] = T_('No');
		$this->translations['Tag Manager'] = T_('Tag Manager');
		$this->translations['Filter by type:'] = T_('Filter by type:');
		$this->translations['Search:'] = T_('Search:');
		$this->translations['Edit'] = T_('Edit');
		$this->translations['New'] = T_('New');
		$this->translations['Details'] = T_('Details');
		$this->translations['Type:'] = T_('Type:');
		$this->translations['Name:'] = T_('Name:');
		$this->translations['Frequency:'] = T_('Frequency:');
		$this->translations['Published:'] = T_('Published:');
		$this->translations['Hidden:'] = T_('Hidden:');
		$this->translations['Description:'] = T_('Description:');
	}

}

class listTagsHTML extends tagsAdminHTML {

	private function columnHeads ($rowcount) {
		return <<<COLUMN_HEADS

		<thead>
			<tr>
				<th width="3%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll($rowcount);" />
				</th>
				<th class="title">
					{$this->T_('Type')}
				</th>
				<th class="title">
					{$this->T_('Name')}
				</th>
				<th class="title">
					{$this->T_('Frequency')}
				</th>
				<th class="title">
					{$this->T_('Published')}
				</th>
				<th class="title">
					{$this->T_('Hidden')}
				</th>
			</tr>
		</thead>
				
COLUMN_HEADS;

	}

	private function listLine ($tag, $i, $k, $n) {
		$hidden = $tag->hidden ? T_('Yes') : T_('No');
		return <<<TAG_LINE
		
			<tr class="row$k"">
				<td width="5">
					{$this->html('idBox', $i, $tag->id)}
				</td>
				<td>
					$tag->type
				</td>
				<td align="left">
					<a href="index.php?core=cor_tags&amp;task=edit&amp;cid=$tag->id">
						$tag->name
					</a>
				</td>
				<td>
					$tag->frequency
				</td>
				<td>
					{$this->html('publishedProcessing', $tag, $i )}
				</td>
				<td>
					$hidden
				</td>
			</tr>
			
TAG_LINE;
		
	}

	public function view ($tags, $types, $type, $search='')  {
		$helphtml = aliroHTML::getInstance();
		$choices[] = $helphtml->makeOption('', T_('All types'), ('' == $type));
		foreach ($types as $atype) $choices[] = $helphtml->makeOption($atype, $atype, ($atype == $type));
		$filterselect = $helphtml->selectList ($choices, 'filtype', 'class="inputbox" onchange="document.adminForm.submit( );"');
		$n = count($tags);
		$k = 0;
		$taglist = '';
		foreach ($tags as $i=>$tag) {
			$taglist .= $this->listLine($tag, $i, $k, $n);
			$k = 1 - $k;
		}
		echo <<<TAG_LIST
		
		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
				{$this->T_('Tag Manager')}
			</th>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					{$this->T_('Filter by type:')}
					$filterselect
				</td>
				<td>
					{$this->T_('Search:')}
					<input type="text" size="20" name="search" value="$search" />
				</td>
			</tr>
		</tbody>
		</table>

		<table class="adminlist">
		{$this->columnHeads($n)}
		$taglist
		</table>
		<div>
			$this->optionline
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
		</div>
		
TAG_LIST;

	}
}

class editTagsHTML extends tagsAdminHTML {

	public function view ($tag)
	{
		$function = $tag->id ? T_('Edit') : T_('New');
		$title = sprintf(T_('%s Tag :: %s'), $function, $tag->name);

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
					<td width="10%" align="right">{$this->T_('Type:')}</td>
					<td width="80%">
						<input class="inputbox" type="text" name="type" size="50" maxlength="50" value="$tag->type" />
					</td>
				</tr>
				<tr>
					<td width="10%" align="right">{$this->T_('Name:')}</td>
					<td width="80%">
					<input class="inputbox" type="text" name="name" size="50" maxlength="100" value="$tag->name" />
					</td>
				</tr>
				<tr>
					<td width="10%" align="right">{$this->T_('Frequency:')}</td>
					<td width="80%">
						<input class="inputbox" type="text" name="frequency" size="50" maxlength="6" value="$tag->frequency" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">{$this->T_('Published:')}</td>
					<td>
						<input type='radio' name='published' value='1' {$this->checkedIfTrue($tag->published)} />{$this->T_('Yes')}
						<input type='radio' name='published' value='0' {$this->checkedIfTrue(!$tag->published)} />{$this->T_('No')}
					</td>
				</tr>
				<tr>
					<td width="10%" align="right">{$this->T_('Hidden:')}</td>
					<td width="80%">
						<input type='radio' name='hidden' value='1' {$this->checkedIfTrue($tag->hidden)} />{$this->T_('Yes')}
						<input type='radio' name='hidden' value='0' {$this->checkedIfTrue(!$tag->hidden)} />{$this->T_('No')}
					</td>
				</tr>
				<tr>
					<td width="10%" align="right">{$this->T_('Description:')}</td>
					<td width="80%">
						<textarea name="description" rows="5" cols="55">$tag->description</textarea>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		$this->optionline;
		<input type="hidden" name="id" value="$tag->id" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<script type="text/javascript" src="{$this->getCfg('live_site')}/includes/js/overlib_mini.js"></script>
EDIT_HTML;

		echo $html;
	}
}