<?php

class sefAdminHTML extends basicAdminHTML {

	function sefNotes () {
		return <<<SEF_NOTES

<form action="index2.php" method="post" id="adminForm" name="adminForm">
<table class="adminheading">
	<thead>
	<tr>
		<th>Aliro SEO Manager</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>
		Aliro provides an advanced search engine optimisation framework.  It is
		designed particularly to work with sef_ext.php files for URI transformation - the 
		interface it uses to these files is an extended version of that used by SEF Advance.  
		Any sef_ext.php file that works with one should work with the other, although those 
		that do not have the Aliro extended interface will not provide full functionality.  
		That will affect the ability to translate task/function codes, and the forcing of 
		unique ID codes.  Support for sef_ext.php files is the responsibility of their 
		respective authors!
		</td>
	</tr>
	<tr>
		<td>
		Aliro provides the SEO framework for URI transformation, but the individual sef_ext 
		files carry out the translation of a URI from the basic form to the SEO form, as this 
		work is specific to each application.  Although there are similarities, reliable SEO
		requires detailed knowledge of how a component works.  The translation back from the
		SEO version of the URI to the basic form with query string is normally handled by
		Aliro, using the database and a substantial cache.  But if the reverse transformation
		is unsuccessful, Aliro will ask the component sef_ext to convert back.
		</td>
	</tr>
	<tr>
		<td>
		In addition to the transformation of URIs, Aliro SEF also provides control of metadata.
		For any URI that has been recorded by Aliro, it is possible to configure the page title,
		description, keywords and also the robots information that influences the behaviour of 
		search engine crawlers.
		</td>
	</tr>

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
	<form action="index2.php" method="post" id="adminForm" name="adminForm">
		<div id="urifilters" style="padding-bottom:5px">
			<label for="origuri">Filter on raw URI:</label>
			<input type="text" name="origuri" id="origuri" value="{$controller->filters['origuri']}" size="40" class="inputbox" />
			<label for="sefuri">Filter on SEF URI:</label>
			<input type="text" name="sefuri" id="sefuri" value="{$controller->filters['sefuri']}" size="40" class="inputbox" />
		</div>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20">
						<input type="checkbox" id="toggle" name="toggle" value="" />
					</th>
					<th align="left">Age (secs)</th>
					<th align="left">Raw URI</th>
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
		<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
	</form>
	<!-- End of code from Remosef -->

URI_LIST;
        
        $scriptText = <<<JSTAG
            YUI().use('*', function(Y) {
                 Y.on("change", function(e) { 
                      Y.one('#adminForm').submit();
                  }, "#origuri", Y);
                  
                  Y.on("change", function(e) { 
                       Y.one('#adminForm').submit();
                   }, "#sefuri", Y);
             });
JSTAG;

        $this->addScriptText($scriptText, 'late', true);

	}

	function listmeta ($metas, $pageNav, $controller) {
		$metahtml = '';
		if ($metas) {
			$total = count($metas);
			$i = 0;
			foreach ($metas as $meta) {
				$typename = ('listuri' == $meta->type) ? 'Listed URIs' : 'Config spec';
				$link = "index.php?core=cor_sef&act=metadata&task=metadata&type=$meta->type&cid=$meta->id";
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
	<form action="index2.php" method="post" id="adminForm" name="adminForm">
		<div id="urifilters" style="padding-bottom:5px">
			<label for="origuri">Filter on raw URI:</label>
			<input type="text" name="origuri" id="origuri" value="{$controller->filters['origuri']}" size="40" class="inputbox" />
			<label for="sefuri">Filter on SEF URI:</label>
			<input type="text" name="sefuri" id="sefuri" value="{$controller->filters['sefuri']}" size="40" class="inputbox" />
		</div>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20">
						<input type="checkbox" id="toggle" name="toggle" value="" />
					</th>
					<th align="left">Type</th>
					<th align="left">Raw URI</th>
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
		<input type="hidden" name="act" value="metadata" />
		<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
	</form>
	<!-- End of code from Remosef -->

META_LIST;
        
        $scriptText = <<<JSTAG
            YUI().use('*', function(Y) {
                 Y.on("change", function(e) { 
                      Y.one('#adminForm').submit();
                  }, "#origuri", Y);
                  
                  Y.on("change", function(e) { 
                       Y.one('#adminForm').submit();
                   }, "#sefuri", Y);
             });      
JSTAG;

        $this->addScriptText($scriptText, 'late', true);

	}

	function editMetaData ($act, $id, $metadata, $controller) {
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
		$seftext = T_('Edit Metadata');
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

	<form action="index2.php" method="post" id="adminForm" name="adminForm">
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
			<input type="hidden" name="act" value="$act" />
			<input type="hidden" name="metatype" value="$metadata->type" />
			<input type="hidden" name="id" value="$id" />
			<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
		</div>
	</div>
	</form>

META_EDIT;

	}

}