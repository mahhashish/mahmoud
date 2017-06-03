#!/usr/bin/php -q

<?PHP

define ('STDOUT', fopen ('php://stdout', 'w'));
fputs (STDOUT, "Account created successfully!\n");

?>

