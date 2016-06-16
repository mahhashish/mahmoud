<?php
/**
* @package Mambo Open Source
* @subpackage Installer
* @copyright (C) 2005 - 2006 Mambo Foundation Inc.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* Mambo was originally developed by Miro (www.miro.com.au) in 2000. Miro assigned the copyright in Mambo to The Mambo Foundation in 2005 to ensure
* that Mambo remained free Open Source software owned and managed by the community.
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
*/
class HTML_installer extends basicAdminHTML {

	// Required because gettext does not find T_('abc') inside heredoc
	public function __construct ($controller) {
		parent::__construct($controller);
		$this->translations['Upload Package File'] = T_('Upload Package File');
		$this->translations['Please select a directory'] = T_('Please select a directory');
		$this->translations['Package File:'] = T_('Package File:');
		$this->translations['Tick if upgrading'] = T_('Tick if upgrading');
		$this->translations['Upload File &amp; Install'] = T_('Upload File &amp; Install');
		$this->translations['Install from directory'] = T_('Install from directory');
		$this->translations['Install directory:'] = T_('Install directory:');
		$this->translations['Tick if upgrading'] = T_('Tick if upgrading');
		$this->translations['Install'] = T_('Install');
		$this->translations['Install from HTTP URL'] = T_('Install from HTTP URL');
		$this->translations['Install HTTP URL:'] = T_('Install HTTP URL:');
		$this->translations['Upload URL &amp; Install'] = T_('Upload URL &amp; Install');
	}
	
	public function showInstallForm( $title, $element, $client = "", $p_startdir = "", $backLink="" ) {
		$testing = $this->T_('Upload Package File');
//die('about to show install form');
		echo <<<INSTALL_FORM

		<script language="javascript" type="text/javascript">
		function submitbutton3(pressbutton) {
			var form = document.adminForm_dir;

			// do field validation
			if (form.userfile.value == ""){
				alert('{$this->T_('Please select a directory')}');
			} else {
				form.submit();
			}
		}
		</script>

		<table class="adminheading">
		<thead>
			<tr>
				<th class="install">
					$title
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="right" nowrap="nowrap">
					$backLink
				</td>
			</tr>
		</tbody>
		</table>

		<div id="installforms">
		<form action="index.php" method="post" name="adminForm_dir">
		<table>
		<thead>
		<tr>
			<th>
				{$this-> T_('Upload Package File')}
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td align="left">
				{$this->T_('Package File:')}
				<input class="text_area" name="userfile" type="file" size="70" />
				<label for="upgrade1">{$this->T_('Tick if upgrading')}</label>
				<input id="upgrade1" name="upgrade" type="checkbox" value="1" />
				<input class="button" type="submit" value="{$this->T_('Upload File &amp; Install')}" />
			</td>
		</tr>
		</tbody>
		</table>
		<div>
			<input type="hidden" name="task" value="uploadfile" />
			<input type="hidden" name="core" value="{$this->getParam($_REQUEST, 'core')}" />
			<input type="hidden" name="element" value="$element" />
			<input type="hidden" name="client" value="$client" />
		</div>
		</form>

		{$this->showUrlForm('http://', $element, $client)}

		<form action="index.php" method="post" name="adminForm_dir">
		<table>
		<thead>
		<tr>
			<th>
				{$this->T_('Install from directory')}
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td align="left">
				{$this->T_('Install directory:')}&nbsp;
				<input type="text" name="userfile" class="text_area" size="65" value="$p_startdir"/>&nbsp;
				<label for="upgrade2">{$this->T_('Tick if upgrading')}</label>
				<input id="upgrade2" name="upgrade" type="checkbox" value="1" />
				<input type="button" class="button" value="{$this->T_('Install')}" onclick="submitbutton3()" />
			</td>
		</tr>
		<tr>
			<td width='95%'>
			<table class='content' align='center'>
			{$this->writableCell( 'media' )}
			{$this->writableCell( 'administrator/components' )}
			{$this->writableCell( 'components' )}
			{$this->writableCell( 'administrator/modules' )}
			{$this->writableCell( 'modules' )}
			{$this->writableCell( 'mambots' )}
			{$this->writableCell( 'administrator/templates' )}
			{$this->writableCell( 'templates' )}
			{$this->writableCell( 'includes' )}
			{$this->writableCell( 'administrator/includes' )}
			{$this->writableCell( 'parameters' )}
			{$this->writableCell( 'images/stories' )}
			</table>
			</td>
		</tr>
		</tbody>
		</table>
		<div>
			<input type="hidden" name="task" value="installfromdir" />
			<input type="hidden" name="core" value="{$this->getParam($_REQUEST, 'core')}" />
			<input type="hidden" name="element" value="$element" />
			<input type="hidden" name="client" value="$client" />
		</div>
		</form>
		</div>
INSTALL_FORM;

	}

	public function writableCell ($folder) {
		$writable = is_writable(criticalInfo::getInstance()->absolute_path.'/'.$folder) ? '<b><span class="green">'.T_('Writeable').'</span></b>' : '<b><span class="red">'.T_('Unwriteable').'</span></b>';
		return <<<WRITABLE_CELL

				<tr>
					<td class="item">
						$folder/
					</td>
					<td align="left">
						$writable
					</td>
				</tr>

WRITABLE_CELL;

	}

	private function showUrlForm ($prompt, $element, $client) {
		if (!ini_get('allow_url_fopen')) return '';
		$html = <<<URL_FORM

		<form action="index.php" method="post" name="adminForm_url">
		<table>
		<thead>
		<tr>
			<th>
				{$this->T_('Install from HTTP URL')}
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td align="left">
				{$this->T_('Install HTTP URL:')}&nbsp;
				<input type="text" name="userurl" class="text_area" size="65" value="$prompt" />&nbsp;
				<label for="upgrade3">{$this->T_('Tick if upgrading')}</label>
				<input id="upgrade3" name="upgrade" type="checkbox" value="1" />
				<input type="submit" class="button" value="{$this->T_('Upload URL &amp; Install')}" />
			</td>
		</tr>
		</tbody>
		</table>
		<div>
			<input type="hidden" name="task" value="installfromurl" />
			<input type="hidden" name="core" value="{$this->getParam($_REQUEST, 'core')}" />
			<input type="hidden" name="element" value="$element" />
			<input type="hidden" name="client" value="$client" />
		</div>
		</form>

URL_FORM;

		return $html;
	}

}

?>