<?php

class listConfigHTML extends basicAdminHTML {

	public function showConfig ($config, $fields, $corecredentials, $credentials) {
		$filehtml = <<<FILE_HTML
		<fieldset>
			<legend>File Names</legend>
			<p><label for="gencfgfile">General configuration file</label>
			<input type="text" id="gencfgfile" readonly="readonly" size="50" value="{$config['configfilename']}" /></p>
			<p><label for="genDBfile">General database credentials </label>
			<input type="text" id="genDBfile" readonly="readonly" size="50" value="{$credentials['configfilename']}" /></p>
			<p><label for="coreDBfile">Core database credentials</label>
			<input type="text" id="coreDBfile" readonly="readonly" size="50" value="{$corecredentials['configfilename']}" /></p>
		</fieldset>
FILE_HTML;

		$htmlset = '';
		foreach ($fields as $fieldset=>$fgroup) {
			$pretty = strtolower($fieldset);
			$pretty[0] = strtoupper($pretty[0]);
			$htmlfield = '';
			foreach ($fgroup as $field) {
				$prettyfield = strtolower($field);
				$prettyfield[0] = strtoupper($prettyfield[0]);
				$value = empty($config[$field]) ? '' : $config[$field];
				$htmlfield .= <<<FIELD_HTML
				<p><label for="$field">$prettyfield:</label><br />
				<input type="text" id="$field" name="$field" size="100" value="{$value}" /></p>
FIELD_HTML;
			}

			$htmlset .= <<<SET_HTML
			<fieldset>
				<legend>$pretty</legend>
				$htmlfield
			</fieldset>
SET_HTML;

		}

		$fresh = new aliroInstall();
		$html = <<<ALL_HTML
			$filehtml
			$htmlset
			{$fresh->makeDBForm('Core Database details', 'core', $corecredentials)}
			{$fresh->makeDBForm('General Database details', 'gen', $credentials)}
			<input type="hidden" name="task" value="" />
			$this->optionline

ALL_HTML;

		echo $html;
	}
	
	public function acceptManifest () {
		$title = T_('Please upload a manifest XML file for review');
		echo <<<ACCEPT_MANIFEST
		
		<h3>$title</h3>
		<div>
			<input class="inputbox" name="manifest" type="file" size="70" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="core" value="cor_config" />
		</div>
		
ACCEPT_MANIFEST;

	}

	public function installManifest () {
		$title = T_('Please upload an upgrade ZIP file');
		echo <<<ACCEPT_UPGRADE
		
		<h3>$title</h3>
		<div>
			<input class="inputbox" name="manifest" type="file" size="70" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="core" value="cor_config" />
		</div>
		
ACCEPT_UPGRADE;

	}
	
	public function reportManifest ($reports) {
	
	}

}