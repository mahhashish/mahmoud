<?php
//app
require_once __DIR__.'/vendor/autoload.php';

use PHPArch\Command\SayCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new SayCommand());
$application->run();