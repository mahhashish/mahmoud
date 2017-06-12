<?php
// functions.php
function check_txnid($tnxid)
{
    global $conn,$t_payments;
   // return true;
    $valid_txnid = true;
    $sql         = $conn->query("SELECT * FROM $t_payments WHERE txn_id = '$tnxid'")->fetchColumn();


if($sql != 0){
      $valid_txnid = false;
}

 
    return $valid_txnid;
}
function check_price($price, $id)
{
    $valid_price = false;
    //you could use the below to check whether the correct price has been paid for the product
    
    /* 
    $sql = mysql_query("SELECT amount FROM `products` WHERE id = '$id'");        
    if (mysql_numrows($sql) != 0) {
    while ($row = mysql_fetch_array($sql)) {
    $num = (float)$row['amount'];
    if($num == $price){
    $valid_price = true;
    }
    }
    }
    return $valid_price;
    */
    return true;
}
function updatePayments($data)
{
    global $conn,$t_payments,$t_client_banners;
    if (is_array($data)) {
        $stmt = $conn->prepare("INSERT INTO $t_payments (txn_id, name, email, payment_amount, payment_status, period, adlink, imglink, paypal_email, ip, createdtime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $data['txn_id']);
      $stmt->bindParam(2, $data['name']);
      $stmt->bindParam(3, $data['email']);

        $stmt->bindParam(4, $data['payment_amount']);
        $stmt->bindParam(5, $data['payment_status']);
        $stmt->bindParam(6, $data['option_selection1']);
        $stmt->bindParam(7, $data['option_selection2']);
        $stmt->bindParam(8, $data['option_selection3']);
        $stmt->bindParam(9, $data['payer_email']);
        $stmt->bindParam(10, $data['custom']);
        $stmt->bindParam(11, date("Y-m-d H:i:s"));
        $stmt->execute();
//
 $stmta = $conn->prepare("INSERT INTO $t_client_banners (txn_id, image, alt,  link, impressions, clicks, period, status, created, expires) VALUES (?, ?, ?, ?, '0', '0', ?, 'p', ?, ?)");
        $stmta->bindParam(1, $data['txn_id']);
     //$fecha_ini = strtotime(date("Y-m-d", strtotime($fecha_ini)) . " +1 day");
$date = date("Y-m-d H:i:s");// current date



        $stmta->bindParam(2, $data['option_selection3']); //
        $stmta->bindParam(3, $data['option_selection2']); //
        $stmta->bindParam(4, $data['option_selection2']); //
        $stmta->bindParam(5, $data['option_selection1']); //
      //  $stmta->bindParam(7, $data['custom']);
     //   $stmta->bindParam(8, $data["p"]);

        $stmta->bindParam(6, date("Y-m-d H:i:s"));
        $stmta->bindParam(7, date("Y-m-d, H:i:s", strtotime('+1 day')));

        $stmta->execute();

//
        return true;
    }
}
?>