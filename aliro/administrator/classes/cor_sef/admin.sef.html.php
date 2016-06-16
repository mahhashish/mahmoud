<?php

class sefAdminHTML {

	function sefNotes () {
		return <<<SEF_NOTES

<form action="index2.php" method="post" name="adminForm">
<table class="adminheading">
	<thead>
	<tr>
		<th>ReMOSef SEO component</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>
		ReMOSef component provides an advanced search engine optimisation framework.  It is
		designed particularly to work with sef_ext.php files - the interface it uses
		to these files is an extended version of that used by SEF Advance.  Any sef_ext.php
		file that works with one should work with the other, although those that do not have
		the Remosef extended interface will not provide full functionality.  That will affect
		the ability to translate task/function codes, and the forcing of unique ID codes.
		There are suitable files
		at http://www.remository.com for a number of standard components, written specifically
		to work with Remosef.  Support for sef_ext.php files is the responsibility of their
		respective authors!
		</td>
	</tr>
	<tr>
		<td>
		Remosef provides the SEF framework, but the individual sef_ext files carry out
		the translation of a URI between the basic form and the SEF form, as this work
		is specific to each component.  Although there are similarities, reliable SEF
		requires knowledge of how a component works.
		</td>
	</tr>
	<tr>
		<td>
			<tr>
		<td>
		Note that Remosef on its own provides only basic SEF (similar to Joomla) for
		all modules - only when sef_ext.php files are installed will advanced SEF
		come into action.  This applies even to the content component and to Remository.
		The content component must NOT be included in the list of components - it will
		be given basic SEF by default, and advanced SEF if its sef_ext.php file (from
		http://www.remository.com) is installed.
		</td>
	</tr>

		</td>
	</tr>
	<tr>
		<td>
		Where there are lists of similar items below, a number of empty boxes provide for
		additions.  When you save additions, new empty boxes will be provided, so there is
		no limit on the total number of items - provided you save additions a few at a time.
		</td>
	</tr>
	</tbody>
</table>

SEF_NOTES;

	}

	function listuris ($uris, $pageNav, $controller) {
		$urihtml = '';
		if ($uris) {
			$total = count($uris);
			$i = 0;
			foreach ($uris as $uri) {
				$showage = time() - $uri->refreshed;
				$uritext = htmlspecialchars($uri->uri, ENT_QUOTES, 'UTF-8');
				$seftext = htmlspecialchars($uri->sef, ENT_QUOTES, 'UTF-8');
				$urihtml .= <<<URI_LINE

			<tr>
				<td>
					<input type="checkbox" id="cb$i" name="cid[]" value="$uri->id" onclick="isChecked(this.checked);" />
				</td>
				<td>
					$showage
				</td>
				<td>
					$uritext
				</td>
				<td>
					$seftext
				</td>
			</tr>

URI_LINE;

				$i++;
			}
		}
		else $total = 0;
		echo <<<URI_LIST

	<h3>Standard Links Converted to SEF</h3>
	<form action="index2.php" method="post" name="adminForm">
		<div id="urifilters" style="padding-bottom:5px">
			<label for="origuri">Filter on raw URI:</label>
			<input type="text" name="origuri" id="origuri" value="{$controller->filters['origuri']}" size="40" class="inputbox" onChange="document.adminForm.submit();" />
			<label for="sefuri">Filter on SEF URI:</label>
			<input type="text" name="sefuri" id="sefuri" value="{$controller->filters['sefuri']}" size="40" class="inputbox" onChange="document.adminForm.submit();" />
		</div>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onClick="checkAll($total);" />
					</th>
					<th align="left">Age (secs)</th>
					<th align="left">Joomla URI</th>
					<th align="left">SEF URI</th>
				</tr>
			</thead>
			<tbody>
				$urihtml
			</tbody>
		</table>
		{$pageNav->getListFooter()}
		<input type="hidden" name="core" value="cor_sef" />
		<input type="hidden" name="act" value="uri" />
		<input type="hidden" name="boxchecked" value="0" />
	</form>
	<!-- End of code from Remosef -->

URI_LIST;


	}

	function listmeta ($metas, $pageNav, $controller) {
		$metahtml = '';
		if ($metas) {
			$total = count($metas);
			$i = 0;
			foreach ($metas as $meta) {
				$typename = ('listuri' == $meta->type) ? 'Listed URIs' : 'Config spec';
				$link = "index.php?core=cor_sef&act=listmeta&task=metadata&type=$meta->type&cid=$meta->id";
				$uritext = htmlspecialchars($meta->uri, ENT_QUOTES, 'UTF-8');
				$sef = ('config' == $meta->type) ? $meta->modified : $meta->sef;
				$seftext = htmlspecialchars($sef, ENT_QUOTES, 'UTF-8');
				$metahtml .= <<<META_LINE

			<tr>
				<td>
					<input type="checkbox" id="cb$i" name="cid[]" value="$meta->id" onclick="isChecked(this.checked);" />
				</td>
				<td>
					$typename
				</td>
				<td>
					<a href="$link">$uritext</a>
				</td>
				<td>
					$seftext
				</td>
				<td>
					$meta->htmltitle
				</td>
				<td>
					$meta->robots
				</td>
			</tr>

META_LINE;

				$i++;
			}
		}
		else $total = 0;
		echo <<<META_LIST

	<h3>Metadata overrides</h3>
	<form action="index2.php" method="post" name="adminForm">
		<div id="urifilters" style="padding-bottom:5px">
			<label for="origuri">Filter on raw URI:</label>
			<input type="text" name="origuri" id="origuri" value="{$controller->filters['origuri']}" size="40" class="inputbox" onChange="document.adminForm.submit();" />
			<label for="sefuri">Filter on SEF URI:</label>
			<input type="text" name="sefuri" id="sefuri" value="{$controller->filters['sefuri']}" size="40" class="inputbox" onChange="document.adminForm.submit();" />
		</div>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onClick="checkAll($total);" />
					</th>
					<th align="left">Type</th>
					<th align="left">Joomla URI</th>
					<th align="left">SEF URI</th>
					<th align="left">Title</th>
					<th align="left">Robots</th>
				</tr>
			</thead>
			<tbody>
				$metahtml
			</tbody>
		</table>
		{$pageNav->getListFooter()}
		<input type="hidden" name="core" value="cor_sef" />
		<input type="hidden" name="act" value="listmeta" />
		<input type="hidden" name="boxchecked" value="0" />
	</form>
	<!-- End of code from Remosef -->

META_LIST;


	}

	function editMetaData ($type, $id, $metadata, $controller) {
		$act = mosGetParam($_REQUEST, 'act');
		$act = ('listuri' == $act) ? 'listuri' : 'config';
		$css = <<<META_CSS

		<style type="text/css" media="screen">
		#sefmetadata div {
			padding: 10px 0;
		}
		#sefmetadata label {
			display: block;
			width: 25%;
			float: left;
			clear: left;
			margin: 5px 0 0 0;
			text-align: right;
		}
		#sefmetadata div input, #sefmetadata div textarea {
			float: left;
			/* display: inline; inline display must not be set or will hide submit buttons in IE 5x mac */
			margin:5px 0 0 10px; /* set margin on left of form elements rather than right of
                              		label aligns textarea better in IE */
			text-align: left;
		}
		#unsefd {
			color: green;
			font-size: 14px;
		}
		</style>

META_CSS;

		$controller->addCustomHeadTag($css);
		$uritext = htmlspecialchars($metadata->uri, ENT_QUOTES, 'UTF-8');
		$sef = ('config' == $type) ? $metadata->modified : $metadata->sef;
		$seftext = htmlspecialchars($sef, ENT_QUOTES, 'UTF-8');
		echo <<<META_EDIT

	<table class="adminheading">
		<tr>
			<th>
				$seftext <span id="unsefd">($uritext)</span>
			</th>
		</tr>
	</table>
	<table class="adminlist">
			<tr>
				<th>
					<h3>Metadata</h3>
				</th>
			</tr>
	</table>

	<form action="index2.php" method="post" name="adminForm">
	<div id="sefmetadata">
		<div>
			<label for="htmltitle">HTML Title:</label>
			<input type="text" name="htmltitle" id="htmltitle" class="inputbox" size="60" value="$metadata->htmltitle" />
		</div>
		<div>
			<label for="robots">Robots:</label>
			<input type="text" name="robots" id="robots" class="inputbox" size="60" value="$metadata->robots" />
		</div>
		<div>
			<label for="description">Description:</label>
			<textarea name="description" id="description" rows="5" cols="60">$metadata->description</textarea>
		</div>
		<div>
			<label for="keywords">Keywords:</label>
			<textarea name="keywords" id="keywords" rows="5" cols="60">$metadata->keywords</textarea>
		</div>
		<div>
			<input type="hidden" name="core" value="cor_sef" />
			<input type="hidden" name="act" value="$type" />
			<input type="hidden" name="metatype" value="$metadata->type" />
			<input type="hidden" name="id" value="$id" />
			<input type="hidden" name="boxchecked" value="0" />
		</div>
	</div>
	</form>

META_EDIT;

	}

}