<?php

function ChangeCustomerID($old_customer_id, $new_customer_id) {

  $query_update_address  = "UPDATE ADDRESS " .
                           "SET CUSTOMER_ID=$new_customer_id ".
                           "WHERE CUSTOMER_ID=$old_customer_id";
  $query_update_customer = "UPDATE CUSTOMER " .
                           "SET CUSTOMER_ID=$new_customer_id ".
                           "WHERE CUSTOMER_ID=$old_customer_id";

  $success = 0; // Success/error indicator

  // Server starts the transaction implicitly, 
  // if auto commit option is set to FALSE
  $dbh->autoCommit(false);

  $result = $dbh->query($query_update_address);
  if ($dbh->isError($result))
  {
     // display an error message and exit
     echo "Error updating addresses.";
  }
  else 
  {
    $result = $dbh->query($query_update_customer);
    if ($dbh->isError($result))
    {
       // display an error message and exit
       echo "Error updating customer.";
    }
    else 
    {
       // Indicate that all intended has been done
       $success = 1;
    }
  }

  if ($success == 1) 
  {
    // If no error happened, we commit the work
    $dbh->commit();
  }
  else
  {
    // Otherwise, we can undo a complete task leaving
    // the data in the state just as before we started
    $dbh->rollback();
  }

  // You might want to set auto commit option back to True
  // for other processes that do not use transactions
  $dbh->autoCommit(false);
  
  return $success;

}

?>