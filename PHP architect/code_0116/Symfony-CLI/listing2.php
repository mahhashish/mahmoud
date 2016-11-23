<?php
// src/Command/SayCommand.php
namespace PHPArch\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SayCommand extends Command
{
   protected function configure()
   {
      $this->setName('say')
         ->setDescription('Write a short quote.')
         ->addArgument(
            'phrase',
            InputArgument::OPTIONAL,
            'The phrase you want to say',
            'I don\'t have anything to say'
         );
   }

   public function execute(
      InputInterface $input,
      OutputInterface $output)
   {
      $phrase = $input->getArgument('phrase');
      $output->writeln($phrase);
   }
}