#!/usr/bin/php -q
<?PHP
define ('STDIN', fopen ('php://stdin', 'r'));
define ('STDOUT', fopen ('php://stdout', 'w'));

$PassPhrase = trim (fgets (STDIN, 256));
if ($PassPhrase != "PHP|ARCH")
        exit ("Invalid argument");

$Domain = trim (fgets (STDIN, 256));
$ServicePlan = trim (fgets (STDIN, 256));
$Email = trim (fgets (STDIN, 256));
$Username = trim (fgets (STDIN, 256));
$Password = trim (fgets (STDIN, 256));

$Command = "./CreateAccount -d $Domain -p $ServicePlan -e $Email -u $Username -p $Password";
$Output = shell_exec ($Command);

fputs (STDOUT, $Output);

fclose (STDIN);
fclose (STDOUT);
exit ();
?>
