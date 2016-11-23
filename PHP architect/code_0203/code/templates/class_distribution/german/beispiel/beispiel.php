<?
//inkludiert die Klasse pet zur weiteren vVrwendung

require_once('class_pet.ger.inc.php');


//erstellt ein neues Template-Objekt und weist eine bereits
//vorhandene Datei zu, welche als Tempalte eingelesen wird.

$template = new pet;
$template->read_file('beispiel.tpl.html');


// Content für einzelne Content-Tags

$template->add_content(date("d"), 'tag');
$template->add_content(date("m"), 'monat');
$template->add_content(date("Y"), 'jahr');


// 10 md5 hashes für den Loop 'md5_hashes'

for($i=1; $i<=10; $i++)
   {
   $dataset['row'] = $i;
   $dataset['md5'] = md5(microtime());
   
   $datasets[] = $dataset;
   }   

$template->add_content($datasets, 'md5_hashes');

   
// 10 Blöcke à 10 md5 hashes für die
// verschachtelten Loops 'counter' und 'subcounter'

for($i=1; $i<=10; $i++)
   {
   unset($counter['subcounter']);
   
   $counter['blockcounter'] = $i;
   
   for($j=1; $j<=10; $j++)
      {
	  $subarray['hashcounter'] = $j;
	  $subarray['md5'] = md5(microtime());
	  
	  $counter['subcounter'][] = $subarray;
      }

   $counters[] = $counter;
   }

$template->add_content($counters, 'counter');


// parse das Template und gib das Ergebnis aus.

$template->parse();
$template->output();
?>
