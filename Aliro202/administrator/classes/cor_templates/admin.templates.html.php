<?php

class listTemplatesHTML extends advancedAdminHTML {
	protected $DBname = 'aliroCoreDatabase';

	public function view ($rows) {
		echo $this->listHTML ('#__extensions', 'Aliro Current Themes', $rows, 'id', false);
	}

	public function list_admin ($admin) {
		return (2 == $admin) ? T_('Yes') : T_('No');
	}

	public function list_default_template ($default, $key) {
		$imagepath = $this->getCfg('admin_site').'/templates/'.$this->getTemplate().'/images';
		if ($default) return "<img src='$imagepath/tick.png' alt='tick' />";
		else return <<<NOT_DEFAULT
		<a href="$this->optionurl&amp;task=makedef&amp;id=$key">
		<img style="border:0" src="$imagepath/publish_x.png" alt="cross" />
		</a>
NOT_DEFAULT;
	}
	
	public function list_inner ($inner) {
		return (0 == $inner) ? T_('No') : T_('Yes');
	}

	public function editclass ($classdocs) {
		echo $this->editHTML ('#__extensions', 'Aliro template - edit', 'id', $classdocs);
	}

}