<?php
// src/Command/ElephantFortuneCommand.php
namespace PHPArch\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ElephantFortuneCommand extends Command
{
   protected function configure() {
      $this->setName('elephant:fortune')
         ->setDescription('Make the Elephant say a fortune.');
   }

   public function execute(InputInterface $input,
                           OutputInterface $output) {
      $fortuneCommand = $this->getApplication()
         ->find('fortune');
      $arrayInput = new ArrayInput([
         'command' => 'fortunes',
      ]);
      $fortuneCommand->run($arrayInput, $output);
      $output->writeln(
         file_get_contents(__DIR__.'/../Resources/elephant')
      );
   }
}