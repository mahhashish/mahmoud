CREATE TRIGGER before_new_customer
BEFORE INSERT ON customer_id
REFERENCING NEW AS new_customer_row
FOR EACH ROW
BEGIN
   new_customer_row.CUSTOMER_ID = 
      (SELECT MAX(CUSTOMER_ID)+1 FROM CUSTOMER);
END

