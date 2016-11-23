$dir = __DIR__.'/../Resources/';
if ($output->isVerbose()) {
   $output->writeln(file_get_contents($dir.'v_elephant'));
}
if ($output->isVeryVerbose()) {
   $output->writeln(file_get_contents($dir.'vv_elephant'));
}
if ($output->isDebug()) {
   $output->writeln(file_get_contents($dir.'vvv_elephant'));
}