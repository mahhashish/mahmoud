<?
//includes the class pet for use

require_once('class_pet.eng.inc.php');


//creates a new template object and assigns an existing file
//which is read and used as tempalte

$template = new pet;
$template->read_file('example.tpl.html');


//content for single content tags

$template->add_content(date("d"), 'day');
$template->add_content(date("m"), 'month');
$template->add_content(date("Y"), 'year');


//10 md5 hashes for the loop 'md5_hashes'

for($i=1; $i<=10; $i++)
   {
   $dataset['row'] = $i;
   $dataset['md5'] = md5(microtime());
   
   $datasets[] = $dataset;
   }   

$template->add_content($datasets, 'md5_hashes');

   
// 10 with 10 md5 hashes  each for the interlocked
// loops 'counter' und 'subcounter'

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


//parse the template and display the result

$template->parse();
$template->output();
?>