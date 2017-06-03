<?php
function addTestCases($log)
{
   echo "<br>Adding a few errorlogs<br>";
   $log->logMsg("FirstTest","Warning");
   $log->logMsg("Next Test","Error");
   $log->logMsg("Last Test, for now","Severe");
   echo "<br><hr><br>";
}
?>