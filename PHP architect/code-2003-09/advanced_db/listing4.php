<?php

function DeleteCustomer($customer_id) {

  $query_delete_address  = "DELETE FROM ADDRESS " .
                           "WHERE CUSTOMER_ID=$customer_id";
  $query_delete_customer = "DELETE FROM CUSTOMER " .
                           "WHERE CUSTOMER_ID=$customer_id";

  $result = $dbh->query($query_delete_address);
  if ($dbh->isError($result))
  {
     // display an error message and exit
     echo "Error deleting addresses.";
     return 0;
  }
  else 
  {
    $result = $dbh->query($query_delete_customer);
    if ($dbh->isError($result))
    {
       // display an error message and exit
       echo "Error deleting customer.";
       return 0;
    }
    else return 1;
  }

}

?>
