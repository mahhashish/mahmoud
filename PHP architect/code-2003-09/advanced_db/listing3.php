<?php

function DeleteCustomer($customer_id) {

  $query_check_address    = "SELECT COUNT(*) AS CNT_ADDRESS ".
                            "FROM ADDRESS " .
                            "WHERE CUSTOMER_ID=$customer_id";
  $query_delete_customer  = "DELETE FROM CUSTOMER " .
                            "WHERE CUSTOMER_ID=$customer_id";

  $result = $dbh->query($query_check_address);
  $row = $result->fetchRow());
  $count = $row['CNT_ADDRESS'];
  if ($count > 0) 
  {
     // display an error
     echo "There are addresses linked to this customer!";
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
  }
  else return 1;

}

?>