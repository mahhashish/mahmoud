<?php
  $dbUsername = "root";
  $dbPassword = "";
  $dbHostname = "localhost";
  $dbDatabase = "abcd";

  $db = mysql_connect($dbHostname, $dbUsername,$dbPassword) or die("Could not connect");
  mysql_select_db($dbDatabase) or die("Could not select database");

	function inradius($zip,$radius)
    {
        $query="SELECT * FROM zipData WHERE zipcode='$zip'";
        $result = mysql_query($query);

        if(mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $lat=$row["lat"];
            $lon=$row["lon"];
            $query="SELECT * FROM zipData WHERE (POW((69.1*(lon-\"$lon\")*cos($lat/57.3)),\"2\")+POW((69.1*(lat-\"$lat\")),\"2\"))<($radius*$radius) ";
            $result = mysql_query($query);
            if(mysql_num_rows($result) > 0) {
                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    $zipArray[]=$row;
                }
			return $zipArray;                
            }
        } else {
            return "Zip Code not found";
        }
    } // end func

$zipCode = $HTTP_POST_VARS["zipCode"];
$radius = $HTTP_POST_VARS["radius"];

$zipArray = inRadius($zipCode,$radius);

print "<h2>There are ".count($zipArray)." Zip codes within $radius Miles of $zipCode</h2>";
foreach($zipArray as $row) {
   print "<br>ZipCode:$row[zipcode] Lon:$row[lon] Lat:$row[lat] City: $row[city]";
   
}
?>
