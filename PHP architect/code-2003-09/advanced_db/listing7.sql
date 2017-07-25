PROCEDURE NEW_CUSTOMER (FIRST_NAME, LAST_NAME, ADDITIONAL_INFO, GENDER)
BEGIN
  GET_NEW_CUSTOMER_ID; -- an SQL code or function to get new customer ID
  INSERT_NEW_CUSTOMER; -- an SQL code to insert data into table CUSTOMER
END
