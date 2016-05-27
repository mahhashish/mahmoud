<?php

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
* @subpackage Users
*/

class listExtensionsHTML extends basicAdminHTML {

	public function __construct ($controller) {
		parent::__construct($controller);
	}

	public function view ($rows, $typelist, $subtitle) {
		
		// The first section of code builds the body of a page, listing out installed extensions
		// The extensions are in an array of objects, $rows
		$listhtml = '';
		$k = 0;
		foreach ($rows as $i=>$row) {
			if ($row->authoremail) $email = "<a href='mailto:$row->authoremail'>$row->authoremail</a>";
			else $email = '';
			if ($row->authorurl AND strpos($row->authorurl,'http') !== 0) $authorurl = 'http://'.$row->authorurl;
			else $authorurl = $row->authorurl;
			if ($authorurl) $url = "<a href='$authorurl'>$authorurl</a>";
			else $url = '';
			if ($row->application != $row->formalname) {
				$id_box = '';
				$part_of = $row->application;
			}
			else {
				$id_box = $this->html('idBox', $i, $row->formalname);
				$part_of = '';
			}

			$listhtml .= <<<LIST_HTML

			<tr class="row$k">
				<td>
					$id_box
				</td>
				<td>
					$row->name
				</td>
				<td>
					$row->formalname
				</td>
				<td>
					$part_of
				</td>
				<td>
					$row->author
				</td>
				<td>
					$row->version
				</td>
				<td>
					$row->date
				</td>
				<td>
					$email
				</td>
				<td>
					$url
				</td>
				<td>
					$row->description
				</td>
			</tr>
LIST_HTML;

			$k = 1 - $k;
		}
		// End of the creation of the body of the list
		
		// Now prepare to output the whole contents of the page
		$rowcount = count($rows);
		
		echo <<<COMPLETE_HTML

		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
			{$this->T_('Extension Manager')} <small><small>$subtitle</small></small>
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td align="center">
				$typelist
			</td>
		</tr>
		<tr>
			<td></td>
		</tr>
		</tbody>
		</table>

		<table class="adminlist">
		<thead>
		<tr>
			<th width="3%" class="title">
			<input type="checkbox" id="toggle" name="toggle" value="" />
			</th>
			<th width="12%" class="title">
			{$this->T_('Name')}
			</th>
			<th width="8%" class="title">
			{$this->T_('Formal Name')}
			</th>
			<th width="8%" class="title">
			{$this->T_('Part of')}
			</th>
			<th width="8%" class="title">
			{$this->T_('Author')}
			</th>
			<th width="5%" class="title">
			{$this->T_('Version')}
			</th>
			<th width="7%" class="title" >
			{$this->T_('Date')}
			</th>
			<th width="10%" class="title">
			{$this->T_('Author email')}
			</th>
			<th width="10%" class="title">
			{$this->T_('Author web')}
			</th>
			<th width="20%" class="title">
			{$this->T_('Description')}
			</th>
		</tr>
		</thead>
		<tbody>
			$listhtml
		</tbody>
		</table>
		{$this->pageNav->getListFooter()}
		<input type="hidden" name="core" value="cor_extensions" />
		<input type="hidden" id="task" name="task" value="" />
		<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
		<input type="hidden" id="hidemainmenu" name="hidemainmenu" value="0" />

COMPLETE_HTML;

	}

}