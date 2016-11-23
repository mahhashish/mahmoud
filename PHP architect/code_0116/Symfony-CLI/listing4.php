<?php
// test/TestSayCommand.php
namespace PHPArch\Test;

use PHPArch\Command\SayCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SayCommandTest extends \PHPUnit_Framework_TestCase
{
   public function testSayNothing()
   {
      $application = new Application();
      $application->add(new SayCommand());

      $command = $application->find('say');
      $tester = new CommandTester($command);
      $tester->execute(['command' => $command->getName()]);
      $this->assertRegExp(
         '/I don\'t have anything to say/',
         $tester->getDisplay());
   }
}