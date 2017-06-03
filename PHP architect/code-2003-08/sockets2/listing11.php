#!/usr/bin/php -q
<?PHP
define ('STDIN', fopen ('php://stdin', 'r'));
define ('STDOUT', fopen ('php://stdout', 'w'));

$ServerLoad = explode (': ', trim (shell_exec ('uptime')));
$ServerLoad = explode (', ', $ServerLoad [1]);
$CurrentServerLoad = $ServerLoad [0];

fputs (STDOUT, $CurrentServerLoad);

fclose (STDIN);
fclose (STDOUT);
exit ();
?>
