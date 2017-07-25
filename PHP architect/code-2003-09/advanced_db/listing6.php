<?php

function NewCustomer( $first_name, 
                      $last_name,
                      $aditional_info,
                      $gender ) 
{

  $query_last_id         = "SELECT MAX(CUSTOMER_ID) AS LAST_ID ".
                           "FROM CUSTOMER";

  $query_insert_customer = "INSERT INTO CUSTOMER " .
                           "(CUSTOMER_ID, FIRST_NAME, ".
                           " LAST_NAME, ADDITIONAL_INFO, ".
                           " GENDER) ".
                           "VALUES ".
                           "($customer_id, '$first_name', ".
                           " '$last_name', '$additional_info', ".
                           " '$gender') ";

  // Get new ID
  $result = $dbh->query($query_last_id);

  // Now incease it's value
  $new_id = $result('LAST_ID') + 1;

  // Let's insert the data
  $result = $dbh->query($query_insert_customer);
  if ($dbh->isError($result))
  {
     // display an error message and exit
     echo "Error adding customer.";
     return 0;
  }
  else return 1;

}

?>
