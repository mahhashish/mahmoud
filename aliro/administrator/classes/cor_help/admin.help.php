<?php

class helpAdminHelp extends aliroComponentAdminControllers {

	private static $instance = __CLASS__;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	protected $function_exclude = array ('new', 'remove', 'edit', 'save', 'apply');

	function listTask () {

		$title = T_('Aliro Help');

		echo <<<ALIRO_HELP

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

		<table class="adminlist">
			<thead>
				<tr>
					<th width="3%" class="title">
						$title
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						Sorry, there isn't any online help yet - would you like to contribute to creating some?
					</td>
				</tr>
			</tbody>
		</table>

ALIRO_HELP;
	}

}