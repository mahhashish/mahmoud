<?php
// src/FileLoader.php
namespace PHPArch;

use Psr\Log\LoggerInterface;

class FileLoader
{
   private $logger;

   public function __construct(LoggerInterface $logger) {
      $this->logger = $logger;
   }

   public function load($file) {
      $this->logger->info("About to load file: $file");
      $dir = __DIR__.'/Resources';

      if(!file_exists("$dir/$file")) {
         $this->logger->error("Invalid $file");
         $file = false;
      } else {
         $file = file_get_contents("$dir/$file");
      }

      return $file;
   }
}