<?php

function DeleteCustomersWithoutADdress() {

  $query_customers        = "SELECT * FROM CUSTOMER";

  // SQL statements are not complete - we will add parameters later
  $query_check_address    = "SELECT COUNT(*) AS CNT_ADDRESS ".
                            "FROM ADDRESS ".
                            "WHERE CUSTOMER_ID=";

  $query_delete_customer  = "DELETE FROM CUSTOMER " .
                            "WHERE CUSTOMER_ID=";

  // We must chech ALL customers
  $result_customers = $dbh->query($query_customers);

  // OK, process each row in result set
  while ($row_customer = $result_customers->fetchRow()) 
  {

    // Get customer ID
    $customer_id = $row_customer['CUSTOMER_ID']
    // Count addresses
    $result = $dbh->query($query_check_address."'$customer_id’");
    $row = $result->fetchRow());

    // Get number of addresses
    $count = $row['CNT_ADDRESS'];
    if ($count < 1) 
    {
     // No addresses, delete the customer
     $dbh->query($query_delete_customer."'$customer_id’");
    }

  }

}

?>