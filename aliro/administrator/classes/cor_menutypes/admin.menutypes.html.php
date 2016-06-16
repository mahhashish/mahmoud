<?php

/**
* Aliro Menu Manager HTML
*/

class listMenutypesHTML extends basicAdminHTML {

	function view ($types) {

		$html = <<<END_OF_HEADER_HTML

		<form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
			Menus Manager
			</th>
		</tr>
		</thead>
		<tbody><tr><td></td></tr></tbody>
		</table>

		<table class="adminlist">
		<thead>
		<tr>
			<th width="3%" class="title">
			<input type="checkbox" name="toggle" value="" onclick="checkAll({$this->pageNav->limit});" />
			</th>
			<th width="40%" class="title">
			Name
			</th>
		</tr>
		</thead>
		<tbody>
END_OF_HEADER_HTML;

		$k = 0;
		foreach ($types as $type) {
			$idbox = aliroHTML::getInstance()->idBox($type, $type);
			$menulink = "<a href='index.php?core=cor_menus&amp;task=list&amp;menutype=$type'>$type</a>";

			$html .= <<<END_OF_BODY_HTML

			<tr class="row$k">
				<td>
				$idbox
				</td>
				<td>
					$menulink
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
		<input type="hidden" name="core" value="cor_menutypes" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
END_OF_FINAL_HTML;

		echo $html;

	}

}

?>