<?php

// Should be run as a cron job so that plugins can be time triggered

ob_start();
if (!empty($argv[0])) {
	define ('_ALIRO_LOCAL_PROCESSING', 1);
	$_REQUEST['option'] = 'commandline';
	if (empty($argv[1])) $argv[1] = 'aliroMambotHandler';
	if (empty($argv[2])) $argv[2] = 'trigger';
	if (empty($argv[3])) $argv[3] = 'onCronTrigger';
	require('index.php');
}
