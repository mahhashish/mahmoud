<?php

class listTemplatesHTML extends advancedAdminHTML {
	protected $DBname = 'aliroCoreDatabase';

	public function view ($rows) {
		echo $this->listHTML ('#__extensions', 'Aliro Current Themes', $rows, 'id', false);
	}

	public function list_admin ($admin) {
		if (2 == $admin) return T_('Yes');
		else return T_('No');
	}

	public function list_default_template ($default, $key) {
		$admin_site = $this->getCfg('admin_site');
		if ($default) return "<img src='$admin_site/images/tick.png' alt='tick' />";
		else return <<<NOT_DEFAULT
		<a href="$this->optionurl&amp;task=makedef&amp;id=$key">
		<img style="border:0" src="$admin_site/images/publish_x.png" alt="cross" />
		</a>
NOT_DEFAULT;
	}

	public function editclass ($classdocs) {
		echo $this->editHTML ('#__extensions', 'Aliro template - edit', 'id', $classdocs);
	}

}