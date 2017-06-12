<?php

require_once 'Classes\Constants.inc';
require_once 'Classes\Error.inc';

class cErrorLog
{
   var $ouch;
   var $locking;
   var $errorStatus;
   var $retry; // maximum number of locking attempt
   var $pause; // wait in mirco second between attempts
   var $LogFileName;
   var $Display;
   var $PreError;
   var $PostError;
   var $File;
   var $Path;

   function cErrorLog ($fileName  = "LogFile"
					  ,$Path = "log/"
                      ,$display = true
                      ,$file = true
					  ,$PreErrorTag = "<font color=ff0000>"
					  ,$PostErrorTag = "</font>"
					  ,$locking = true
					  ,$retry = 10
					  ,$pause = 500)
   {
	  $this->LogFileName = $fileName;
	  $this->Path = $Path;
	  if (!file_exists($this->Path))
	     mkdir($this->Path);
      $this->Display = $display;
	  $this->File = $file;
      $this->ouch = new cSevereError;
	  $this->locking = $locking;
	  $this->errorStatus = NO_ERROR;
	  $this->retry = $retry;
	  $this->pause = $pause;
	  $this->PreError = $PreErrorTag;
	  $this->PostError = $PostErrorTag;
   }
   
   function logMsg ($msg, $level, $break=true)
   {
      if ($this->Display)
	     $this->pDisplay ($msg, $break);
	  if ($this->File)
	     $this->pWrite($msg, $level);
   }
   
// Private Methods   
   function pDisplay ($msg, $break=true) //Should be private
   {
      echo $this->PreError;
	  echo $msg;
	  echo $this->PostError;
	  if ($break)
	     echo  "<br>";
   }
   
   function pWrite($msg, $level)
   {
      $IPAddr = (string)$_ENV['REMOTE_ADDR'];
      $timeStamp = getdate();
      $file = $this->Path . $this->LogFileName;
      $line = $level . "\t"
	        . $msg . "\t"
            . $IPAddr . "\t"
            . $timeStamp["mday"] . " "
            . $timeStamp["month"] . " "
            . $timeStamp["year"] . " "
            . $timeStamp["hours"] . ":"
            . $timeStamp["minutes"] . ":"
            . $timeStamp["seconds"] . "\n";
      $theresult = $this->pWriteFile($file, $line);
	  if (!$theresult)
	  {
	     $this->pErrorLog();
	     $this->ouch->alert("Failed to write to the log");
	  }
   }
   
   function pWriteFile($file, $line)
   {
      $lineCount = $this->pReadCount($file . ".ctr");
      $lineCount++;
	  $line = $lineCount . "\t" . $line;
	  $theresult =  $this->pAppendLine($file . ".log", $line);
      $this->pWriteCount($file . ".ctr", $lineCount);
	  return $theresult;
   }
   
   function pReadCount($file)
   {
	  $handle = $this->pOpenFile($file,'r+'); // Open for read + write
      if ($handle==false)
         $this->errorLog();
      else
      {
         $line = $this->pReadFormat($handle, "%s\n"); // read in the counter
         list ($Counter) = $line;
		 fclose ($handle);
		 return $Counter;
	  }
   }
   
   function pWriteCount($file, $count)
   {
      $handle = $this->pOpenFile($file,'w',false); //Open file for write only
      if ($handle==false)
         $this->pErrorLog();
      else
      {
         $result = $this->pWriteToFile($handle, $count);
         fclose($handle);
         return $result;
      } // opened the counter file    
   }
   
   function pErrorLog()
   {
      echo "Error $this->errorStatus <br>";
   }

   function pAppendLine($file, $msg)
   {
      $handle = $this->pOpenFile($file);
	  if ($handle == false)
	     return false;
	  else
	     return $this->pWriteToFile($handle, $msg);
   }

   function pOpenFile ($file, $mode = 'a', $force = true)
   {
      if (file_exists($file))
	  {
	     $handle = @fopen($file, $mode);
		 if (!$handle)
		 {
		    $this->errorStatus = COULD_NOT_OPEN_FILE;
			return false;
		 }
		 else
		    return $handle;
	  }
	  else if ($force)
	  {
	     $handle = @fopen($file, 'a');
		 if (!$handle)
		 {
		    $this->errorStatus = COULD_NOT_CREATE_FILE;
			return false;
		 }
		 else
		    return $handle;
      }
	  else
	  {
		 $this->errorStatus = FILE_DOES_NOT_EXIST;
		 return false;
	  }
   }

   function pWriteToFile ($handle, $details)
   {
      if ($this->locking)
	  {
	     $locked = flock($handle,LOCK_EX+LOCK_NB);
		 $attempts = 0;
		 while (!$locked and $attempts <= $this->retry)
		 {
		    usleep($this->pause);
			$locked = flock($handle,LOCK_EX+LOCK_NB);
		    $attempts++;
		 }
		 if (!$locked)
		    return false;
	  }
	  $result = fwrite($handle, $details);
	  if ($this->locking)
	  {
         $locked = flock($handle,LOCK_UN+LOCK_NB);
		 $attempts = 0;
		 while (!$locked and $attempts <= $this->retry)
		 {
		    usleep($this->pause);
			$locked = flock($handle,LOCK_UN+LOCK_NB);
		    $attempts++;
		 }
		 if (!$locked)
		    $this->ouch->alert("Failed to release a file lock");
      }
	  if (!$result)
	     fclose($handle);
	  return $result;
   }

   function pReadFormat ($handle, $format= "%s\n")
   {
      if ($this->locking)
	  {
	     $locked = flock($handle,LOCK_SH+LOCK_NB);
		 $attempts = 0;
		 while (!$locked and $attempts <= $this->retry)
		 {
		    usleep($this->pause);
			$locked = flock($handle,LOCK_SH+LOCK_NB);
		    $attempts++;
		 }
		 if (!$locked)
		    return false;
	  }
	  $line = fscanf($handle, $format);
      if ($this->locking)
	  {
         $locked = flock($handle,LOCK_UN+LOCK_NB);
		 $attempts = 0;
		 while (!$locked and $attempts <= $this->retry)
		 {
		    usleep($this->pause);
			$locked = flock($handle,LOCK_UN+LOCK_NB);
		    $attempts++;
		 }
		 if (!$locked)
		    $this->ouch->alert("Failed to release a file lock");
      }
	  return $line;
   }

} //class cErrorLog

?>