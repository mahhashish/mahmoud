<?php

class sysinfoAdminSysinfo extends aliroComponentAdminControllers {

	private static $instance = __CLASS__;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance($manager));
	}

	protected $function_exclude = array ('new', 'remove', 'edit', 'save', 'apply');

	function listTask () {

		$title = T_('Aliro System Information');
		ob_start();
		phpinfo();
		$info = ob_get_contents();
		ob_end_clean();
		$style = <<<INFO_STYLE

<style type="text/css">
#phpinfo pre {margin: 0px; font-family: monospace;}
#phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
#phpinfo a:hover {text-decoration: underline;}
#phpinfo table {border-collapse: collapse;}
.center {text-align: center;}
.center table { margin-left: auto; margin-right: auto; text-align: left;}
.center th { text-align: center !important; }
#phpinfo td, #phpinfo th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
#phpinfo h1 {font-size: 150%;}
#phpinfo h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccccff; font-weight: bold; color: #000000;}
.h {background-color: #9999cc; font-weight: bold; color: #000000;}
.v {background-color: #cccccc; color: #000000;}
.vr {background-color: #cccccc; text-align: right; color: #000000;}
#phpinfo img {float: right; border: 0px;}
#phpinfo hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
</style>

INFO_STYLE;

		// $st1 = strpos($info, '<style');
		// $st2 = strpos($info, '</style');
		// $this->addCustomHeadTag(substr($info, $st1, $st2-$st1+8));
		$this->addCustomHeadTag($style);
		$st1 = strpos($info, '<body>');
		$st2 = strpos($info, '</body>');
		$maininfo = substr($info, $st1+6, $st2-$st1-6);
		$purifier = new aliroPurifier;
		$maininfo = $purifier->purify($maininfo);

		echo <<<SYSINFO

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
						<div id="phpinfo">
							$maininfo
						</div>
					</td>
				</tr>
			</tbody>
		</table>

SYSINFO;
	}

}