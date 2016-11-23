<?php
namespace PHPArch\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GreetMeCommand extends Command
{
   protected function configure()
   {
      $this->setName('greet:me')
         ->setDescription('This command will greet me');
   }

   public function execute(
      InputInterface $input,
      OutputInterface $output)
   {
      $helper = $this->getHelper('question');
      $question = new Question('Your Name: ', 'Stranger');

      $name = $helper->ask($input, $output, $question);

      $question = new ConfirmationQuestion(
         "Are you sure your name is $name? ",
         false
      );

      if ($helper->ask($input, $output, $question)) {
         $output->writeln('Hello '.$name);
      } else {
         $output->writeln('Hello Stranger');
      }
   }
}
