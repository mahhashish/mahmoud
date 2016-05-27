<?php

class listConfigHTML extends basicAdminHTML {
	protected $cachemethods = array();

	public function showConfig ($config, $fields, $cachemethods, $corecredentials, $credentials) {
		$this->cachemethods = $cachemethods;
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
				$method = $field.'Display';
				if (method_exists($this, $method)) $htmlfield .= $this->$method($field, $value);
				else $htmlfield .= $this->defaultDisplay($field, $value);
			}

			$htmlset .= <<<SET_HTML
			<fieldset>
				<legend>$pretty</legend>
				$htmlfield
			</fieldset>
SET_HTML;

		}

		$fresh = new aliroInstall();
		$sitename = $this->getCfg('sitename');
		if (strlen($sitename) > 40) $sitename = substr($sitename,0,40).'...';
		$title = T_('Configure ').$sitename;
		$dbhead = T_('Commands to set up databases');
		$html = <<<ALL_HTML
		<table class="adminheading">
		<thead>
		<tr>

			<th class="user">
			$title
			</th>
		</tr>
		</thead>
		<tbody><tr><td></td></tr></tbody>
		</table>

			$filehtml
			$htmlset
			{$fresh->makeDBForm('Core Database details', 'core', $corecredentials)}
			{$fresh->makeDBForm('General Database details', 'gen', $credentials)}
		<h3>$dbhead</h3>
		<p>
			{$this->commandsForDB($corecredentials)}
			{$this->commandsForDB($credentials)}
		</p>
			<input type="hidden" id="task" name="task" value="" />
			$this->optionline

ALL_HTML;

		echo $html;
	}
	
	private function defaultDisplay ($field, $current) {
		$prettyfield = strtolower($field);
		$prettyfield[0] = strtoupper($prettyfield[0]);
		return <<<FIELD_HTML
				<p><label for="$field">$prettyfield:</label><br />
				<input type="text" id="$field" name="$field" size="100" value="{$current}" /></p>
FIELD_HTML;
		
	} 
	
	private function timezoneDisplay ($field, $current) {
		$optionhtml = '';
		$zones = DateTimeZone::listIdentifiers();
		foreach ($zones as $zone) {
			$selected = ($zone == $current) ? 'selected="selected"' : '';
			$optionhtml .= <<<TIME_ZONE

				<option value="$zone" $selected>$zone</option>
		
TIME_ZONE;

		}
		return $this->selectFromOptions($field, 'Time Zone', $optionhtml);
	}
	
	private function cachetypeDisplay ($field, $current) {
		$optionhtml = '';
		foreach ($this->cachemethods as $method) {
			$selected = ($method == $current) ? 'selected="selected"' : '';
			$optionhtml .= <<<CACHE_METHOD

				<option value="$method" $selected>$method</option>
		
CACHE_METHOD;

		}
		return $this->selectFromOptions($field, 'Cache Mechanism', $optionhtml);
	}
	
	protected function selectFromOptions ($field, $title, $optionhtml) {
		return <<<SELECT_OPTION
		
			<p><label for="$field">$title:</label><br />
				<select name="$field" id="$field">
					$optionhtml
				</select>
			</p>
		
SELECT_OPTION;
		
	}
	
	public function acceptManifest () {
		$title = T_('Please upload a manifest XML file for review');
		echo <<<ACCEPT_MANIFEST
		
		<h3>$title</h3>
		<div>
			<input class="inputbox" name="manifest" type="file" size="70" />
			<input type="hidden" id="task" name="task" value="" />
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
			<input type="hidden" id="task" name="task" value="" />
			<input type="hidden" name="core" value="cor_config" />
		</div>
		
ACCEPT_UPGRADE;

	}
	
	public function reportManifest ($reports) {
	
	}
	
	private function commandsForDB ($credentials) {
		return <<<COMMANDS

create database `{$credentials['dbname']}`;<br />
grant all on `{$credentials['dbname']}`.* to {$credentials['dbusername']}@localhost 
	identified by '{$credentials['dbpassword']}';<br />
		
COMMANDS;
		
	}

}