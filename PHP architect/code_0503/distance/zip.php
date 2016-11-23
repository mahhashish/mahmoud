<?php
  $dbUsername = "root";
  $dbPassword = "";
  $dbHostname = "localhost";
  $dbDatabase = "vinay";

  $db = mysql_connect($dbHostname, $dbUsername,$dbPassword) or die("Could not connect");
  mysql_select_db($dbDatabase) or die("Could not select database");

    function distance($zipOne,$zipTwo)
    {
       $query = "SELECT * FROM zipData WHERE zipcode = '$zipOne'";
       $result = mysql_query($query);

       if(mysql_num_rows($result) < 1) {
           return "First Zip Code not found";
       } else {
           $row = mysql_fetch_array($result, MYSQL_ASSOC);
           $lat1 = $row["lat"];
           $lon1 = $row["lon"];
       }

       $query = "SELECT * FROM zipData WHERE zipcode = '$zipTwo'";
       $result = mysql_query($query);

       if(mysql_num_rows($result) < 1) {
           return "Second Zip Code not found";
       }else{
           $row = mysql_fetch_array($result, MYSQL_ASSOC);
           $lat2 = $row["lat"];
           $lon2 = $row["lon"];
       }

       /* Convert all the degrees to radians */
       $lat1 = $lat1 * M_PI/180.0;
       $lon1 = $lon1 * M_PI/180.0;
       $lat2 = $lat2 * M_PI/180.0;
       $lon2 = $lon2 * M_PI/180.0;

       /* Find the deltas */
       $delta_lat = $lat2 - $lat1;
       $delta_lon = $lon2 - $lon1;

       /* Find the Great Circle distance */
       $temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);

       $EARTH_RADIUS = 3956;
       $distance = $EARTH_RADIUS * 2 * atan2(sqrt($temp),sqrt(1-$temp));

       $distance = acos(sin($lat1)*sin($lat2)+cos($lat1)*cos($lat2)*cos($lon2-$lon1)) * $EARTH_RADIUS ;

       return $distance;

    } // end func

    function inradius($zip,$radius)
    {
        $query="SELECT * FROM zipData WHERE zipcode='$zip'";
        $result = mysql_query($query);

        if(mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $lat=$row["lat"];
            $lon=$row["lon"];
            $query="SELECT zipCode FROM zipData WHERE (POW((69.1*(lon-\"$lon\")*cos($lat/57.3)),\"2\")+POW((69.1*(lat-\"$lat\")),\"2\"))<($radius*$radius) ";
            $result = mysql_query($query);
            if(mysql_num_rows($result) > 0) {
                while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    $zipArray[]=$row["zipCode"];
                }
			return $zipArray;                
            }
        } else {
            return "Zip Code not found";
        }        
    } // end func


$zipOne = '00210';  //   74.0580  	42.83326  	SCHENECTADY  	NY
$zipTwo = '00606';  //   76.0390  	36.74659  	VIRGINIA BEACH  	VA

$distance = distance($zipOne,$zipTwo);

echo "The distance between $zipOne and $zipTwo is $distance Miles<br>";

$radius = 20;
$zipArray = inradius($zipOne,$radius);

echo "There are ",count($zipArray)." Zip codes within $radius Miles of $zipOne";

?>
