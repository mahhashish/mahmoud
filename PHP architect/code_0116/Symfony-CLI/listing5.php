<?php
// src/Command/FortuneCommand.php
namespace PHPArch\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FortuneCommand extends Command
{
   protected function configure() {
      $this->setName('fortune')
         ->setDescription(
            'A command to display random quotes.'
         )->addOption(
            'path-to-fortunes',
            'p',
            InputOption::VALUE_REQUIRED,
            'Location of the fortunes archive',
            __DIR__.'/../Resources/fortunes'
         );
   }

   public function execute(
      InputInterface $input,
      OutputInterface $output) {
      $pathToFortunes = $input->getOption('path-to-fortunes');
      $fortunes = explode('%',
         file_get_contents($pathToFortunes));
      $count = count($fortunes)-1;
      $rand = rand(0, $count);
      $quote = $fortunes[$rand];

      $sayCommand = $this->getApplication()->find('say');
      $arrayInput = new ArrayInput(['phrase' => $quote]);
      $sayCommand->run($arrayInput, $output);
   }
}